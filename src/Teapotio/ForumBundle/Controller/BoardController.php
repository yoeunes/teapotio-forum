<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    ForumBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\ForumBundle\Controller;

use Teapotio\ForumBundle\Entity\Board;
use Teapotio\Base\ForumBundle\Form\CreateBoardType;

class BoardController extends BaseController
{
    public function newAction($boardSlug = null)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $user = $this->getUser();
        $board = $this->getBoard();

        if ($this->get('teapotio.forum.access_permission')->canCreateBoard($user, $board) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $parentBoard = null;
        if ($boardSlug !== null) {
            $parentBoard = $this->getBoard();
        }

        /**
         * Making sure it's the right URL
         */
        if ($parentBoard !== null) {
            $realBoardSlug = $this->container->get('teapotio.forum.board')->buildSlug($parentBoard);
            if ($realBoardSlug !== $boardSlug) {
                return $this->redirect(
                    $this->generateUrl(
                        'ForumNewBoardInBoard',
                        array(
                            'boardSlug' => $realBoardSlug,
                            'boardId'   => $parentBoard->getId()
                        )
                    )
                );
            }
        }

        $request = $this->get('request');

        $board = new Board();

        $form = $this->createForm(new CreateBoardType(), $board);

        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            $boardId = $request->request->get('board_id');

            if ($board === null && $boardId === '') {
                $form->addError(new FormError($this->get('translator')->trans('Board.selected.not.valid')));
            } else if ($boardId !== null) {
                $parentBoard = $this->get('teapotio.forum.board')->getById($boardId);
            }

            try {
                $this->get('teapotio.forum.path')->onBoardCreate($board);
            } catch (\Exception $e) {
                $form->addError(new FormError('This.board.name.may.already.be.used'));
            }

            if ($form->isValid() === true) {
                $board->setParent($parentBoard);

                $this->get('teapotio.forum.board')->save($board);

                return $this->redirect($this->get('teapotio.forum')->forumPath('ForumListTopicsByBoard', $parentBoard));
            }
        }

        $infoNotices = array();
        if ($parentBoard !== null) {
            $infoNotices[] = $this->get('translator')->trans('Create.board.in.notice', array('board_name' => $parentBoard->getTitle()));
            $title = $this->generateTitle('Create.a.new.board.in.%title%', array('%title%' => $parentBoard->getTitle()));
        } else {
            $title = $this->generateTitle('Create.a.new.board');
        }

        $params = array(
            'form'          => $form->createView(),
            'board'         => $board,
            'current_board' => $parentBoard,
            'page_title'    => $title,
            'info_notices'  => $infoNotices,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioBaseForumBundle:Board:raw/new.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioBaseForumBundle:Board:new.html.twig', $params);
    }

    public function editAction($boardSlug)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $request = $this->get('request');

        $user = $this->getUser();
        $board = $this->getBoard();

        if ($this->get('teapotio.forum.access_permission')->canEdit($user, $board) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        if ($board === null) {
            return $this->redirect($this->generateUrl('ForumNewBoard'));
        }

        $form = $this->createForm(new CreateBoardType(), $board);

        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            try {
                $this->get('teapotio.forum.path')->onBoardEdit($board);
            } catch (\Exception $e) {
                $form->addError(new FormError('This.board.name.may.already.be.used'));
            }

            if ($form->isValid() === true) {
                $this->get('teapotio.forum.board')->save($board);

                return $this->redirect($this->get('teapotio.forum')->forumPath('ForumEditBoard', $board));
            }
        }

        $groups = $this->get('teapotio.user.group')->getAllGroups();

        $title = $this->generateTitle('Edit.%title%', array('%title%' => $board->getTitle()));

        $params = array(
            'form'          => $form->createView(),
            'current_board' => $board,
            'page_title'    => $title,
            'groups'        => $groups,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioBaseForumBundle:Board:raw/edit.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioBaseForumBundle:Board:edit.html.twig', $params);
    }

    public function moveAction($boardSlug)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $request = $this->get('request');

        $board = $this->getBoard();
        $user = $this->getUser();

        if ($this->get('teapotio.forum.access_permission')->canEdit($user, $board) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $moveToBoard = $this->get('teapotio.forum.board')->getById($request->request->get('board_id'));

        if ($board === null || $moveToBoard === null) {
            $this->get('session')
                 ->getFlashBag()
                 ->add('delete_error', $this->get('translator')->trans('Board.selected.not.valid'));

            return $this->redirect($this->get('teapotio.forum')->forumPath('ForumEditBoard', $board));
        }
    }

    public function editPermissionsAction($boardSlug)
    {
        if ($this->container->get('teapotio.forum.access_permission')->isAdmin($this->getUser()) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $request = $this->get('request');

        $board = $this->getBoard();
        $user = $this->getUser();

        $formName = 'permissions';

        if ($this->get('request')->isMethod('POST') === true) {
            $postData = $this->get('request')->request->get($formName);

            foreach ($postData as $groupId => $permissions) {
                $this->container
                     ->get('teapotio.forum.access_permission')
                     ->setPermissionsOnBoardsFromPostData($groupId, $permissions);
            }
        }

        return $this->redirect($this->get('teapotio.forum')->forumPath('ForumEditBoard', $board));
    }

    public function deleteAction($boardSlug)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $request = $this->get('request');

        $board = $this->getBoard();
        $user = $this->getUser();

        if ($this->get('teapotio.forum.access_permission')->canDelete($user, $board) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $moveToBoard = $this->get('teapotio.forum.board')->getById($request->request->get('board_id'));

        if ($board === null || $moveToBoard === null) {
            $this->get('session')
                 ->getFlashBag()
                 ->add('delete_error', $this->get('translator')->trans('Board.selected.not.valid'));

            return $this->redirect($this->get('teapotio.forum')->forumPath('ForumEditBoard', $board));
        }

        try {
            $this->get('teapotio.forum.board')->moveContent($board, $moveToBoard);

            $this->get('teapotio.forum.board')->delete($board);

            return $this->redirect($this->get('teapotio.forum')->forumPath('ForumListLatestTopics'));
        } catch (\Teapotio\BaseForumBundle\Exception\InvalidBoardException $e) {
            $this->get('session')
                 ->getFlashBag()
                 ->add('delete_error', $this->get('translator')->trans('Cannot.move.content.to.children'));

            return $this->redirect($this->get('teapotio.forum')->forumPath('ForumEditBoard', $board));
        }


    }

    public function listAction()
    {
        $boards = $this->get('teapotio.forum.board')->getBoards(0, 10);

        return $this->render('TeapotioBaseForumBundle:Board:list.html.twig', array(
            'boards' => $boards
        ));
    }
}
