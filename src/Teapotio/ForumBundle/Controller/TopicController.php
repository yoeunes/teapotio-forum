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

use Teapotio\ForumBundle\Entity\Topic;
use Teapotio\ForumBundle\Entity\Message;
use Teapotio\ForumBundle\Form\CreateTopicType;

use Symfony\Component\Form\FormError;

class TopicController extends BaseController
{
    public function newAction($boardSlug = null)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $board = $this->getBoard();

        $user = $this->getUser();

        if ($this->get('teapotio.forum.access_permission')->canCreateTopic($user, $board) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $request = $this->get('request');

        $topic = new Topic();
        $form = $this->createForm(new CreateTopicType(), $topic);

        if ($request->getMethod() === 'POST') {

            $form->bind($request);

            $boardId = $request->request->get('board_id');

            if ($board === null && $boardId === '') {
                $form->addError(new FormError($this->get('translator')->trans('Board.selected.not.valid')));
            } else if ($boardId !== null) {
                $board = $this->get('teapotio.forum.board')->getById($boardId);
            }

            if ($form->isValid() === true) {
                $user = $this->get('security.context')->getToken()->getUser();

                $topic->setBoard($board);

                $this->get('teapotio.forum.topic')->save($topic);

                $message = new Message();
                $message->setBody($form['body']->getData());
                $message->setTopic($topic);
                $message->setIsTopicBody(true);

                $this->get('teapotio.forum.message')->save($message);

                return $this->redirect($this->get('teapotio.forum')->forumPath('ForumListMessagesByTopic', $topic));
            }
        }

        $infoNotices = array();
        if ($board !== null) {
            $infoNotices[] = $this->get('translator')->trans('Create.topic.in.notice', array('%board_name%' => $board->getTitle()));
            $title = $this->generateTitle('New.topic.in.%title%', array('%title%' => $board->getTitle()));
        } else {
            $title = $this->generateTitle('New.topic');
        }

        $params = array(
            'form'          => $form->createView(),
            'current_board' => $board,
            'page_title'    => $title,
            'info_notices'  => $infoNotices,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioBaseForumBundle:Topic:raw/new.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioBaseForumBundle:Topic:new.html.twig', $params);
    }

    public function editAction()
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $board = $this->getBoard();
        $topic = $this->getTopic();
        $user = $this->getUser();

        if ($this->get('teapotio.forum.access_permission')->canEdit($user, $topic) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $request = $this->getRequest();

        $message = $this->get('teapotio.forum.message')->getTopicBodyByTopic($topic);

        $form = $this->createForm(new CreateTopicType(), $topic);
        $form['body']->setData($message->getBody());

        if ($request->getMethod() === 'POST') {

            $form->bind($request);

            if ($form->isValid() === true) {

                $message->setBody($form['body']->getData());

                $this->get('teapotio.forum.topic')->save($topic);
                $this->get('teapotio.forum.message')->save($message);

                return $this->redirect($this->get('teapotio.forum')->forumPath('ForumListMessagesByTopic', $topic));
            }

        }

        $infoNotices = array();
        $title = $this->generateTitle('Edit.topic.%title%', array('%title%' => $topic->getTitle()));

        $params = array(
            'form'          => $form->createView(),
            'message'       => $message,
            'topic'         => $topic,
            'current_board' => $board,
            'page_title'    => $title,
            'info_notices'  => $infoNotices,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioBaseForumBundle:Topic:raw/edit.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioBaseForumBundle:Topic:edit.html.twig', $params);
    }

    public function latestAction()
    {
        if ($this->get('teapotio.forum.board')->getViewableBoards()->count() === 0) {
            if ($this->get('request')->isXmlHttpRequest() === true) {
                $html = $this->get('teapotio.user')
                             ->renderAccessRestricted('TeapotioBaseForumBundle:Topic:raw/nonAuthorized.html.twig');
                return $this->renderJson(array(
                    'html'   => $html,
                    'title'  => $title
                ));
            }

            $html = $this->get('teapotio.user')
                         ->renderAccessRestricted('TeapotioBaseForumBundle:Topic:nonAuthorized.html.twig');

            return $this->renderHtml($html);
        }

        $topicPerPage = 40;
        $page = ($this->get('request')->get('page') === null) ? 1 : $this->get('request')->get('page');
        $offset = ($page - 1) * $topicPerPage;

        $topics = $this->get('teapotio.forum.topic')->getLatestTopics($offset, $topicPerPage);

        $boards = $this->get('teapotio.forum.board')->getBoards();

        $topicsPerPage = $this->get('teapotio.forum')->getTotalTopicsPerPage();
        $messagesPerPage = $this->get('teapotio.forum')->getTotalMessagesPerPage();

        $title = $this->generateTitle('All.topics');

        $params = array(
            'topics_per_page'   => $topicsPerPage,
            'messages_per_page' => $messagesPerPage,
            'current_board'     => null,
            'topics'            => $topics,
            'boards'            => $boards,
            'showBoard'         => true,
            'page_title'        => $title
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioBaseForumBundle:Topic:raw/listWithPagination.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioBaseForumBundle:Topic:list.html.twig', $params);
    }

    public function listAction($boardSlug)
    {
        $board = $this->getBoard();

        if ($board === null) {
            throw $this->createNotFoundException();
        }

        /**
         * Making sure it's the right URL
         */
        $realBoardSlug = $this->container->get('teapotio.forum.board')->buildSlug($board);
        if ($realBoardSlug !== $boardSlug) {
            return $this->redirect($this->get('teapotio.forum')->forumPath('ForumListTopicsByBoard', $board));
        }

        $user = $this->getUser();

        if ($this->get('teapotio.forum.access_permission')->canView($user, $board) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $boardIds = $this->get('teapotio.forum.board')->getChildrenIdsFromBoard($board);
        $boardIds[] = $board->getId();

        $topicsPerPage = $this->get('teapotio.forum')->getTotalTopicsPerPage();
        $messagesPerPage = $this->get('teapotio.forum')->getTotalMessagesPerPage();

        $page = ($this->get('request')->get('page') === null) ? 1 : $this->get('request')->get('page');
        $offset = ($page - 1) * $topicsPerPage;

        if (count($boardIds) === 1) {
            $topics = $this->get('teapotio.forum.topic')->getLatestTopicsByBoard($board, $offset, $topicsPerPage);
        }
        else {
            $topics = $this->get('teapotio.forum.topic')->getLatestTopicsByBoardIds($boardIds, $offset, $topicsPerPage);
        }

        $title = $this->generateTitle('All.topics.in.%title%', array('%title%' => $board->getTitle()));

        $params = array(
            'topics_per_page'   => $topicsPerPage,
            'messages_per_page' => $messagesPerPage,
            'topics'            => $topics,
            'current_board'     => $board,
            'showBoard'         => true,
            'page_title'        => $title,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioBaseForumBundle:Topic:raw/listWithPagination.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioBaseForumBundle:Topic:list.html.twig', $params);
    }

    public function pinAction($boardSlug, $topicSlug)
    {
        return $this->manipulateTopic('pin', $boardSlug, $topicSlug);
    }

    public function lockAction($boardSlug, $topicSlug)
    {
        return $this->manipulateTopic('lock', $boardSlug, $topicSlug);
    }

    public function deleteAction($boardSlug, $topicSlug)
    {
        $topic = $this->getTopic();
        $user = $this->getUser();

        return $this->manipulateTopic('delete', $boardSlug, $topicSlug);
    }

    public function flagAction($boardSlug, $topicSlug)
    {
        return $this->manipulateTopic('flag', $boardSlug, $topicSlug);
    }

    private function manipulateTopic($action, $boardSlug, $topicSlug)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $board = $this->getBoard();
        $topic = $this->getTopic();

        if (true !== $response = $this->isUrlValid($board, $topic, $boardSlug, $topicSlug)) {
            return $response;
        }

        switch ($action) {
            case 'pin':
                if ($topic->isPinned() === true) {
                    $this->get('teapotio.forum.topic')->unpin($topic);
                }
                else {
                    $this->get('teapotio.forum.topic')->pin($topic);
                }
                break;
            case 'delete':
                if ($topic->isDeleted() === true) {
                    $this->get('teapotio.forum.topic')->undelete($topic, true); // true: bubble down
                }
                else {
                    $this->get('teapotio.forum.topic')->delete($topic, true); // true: bubble down
                }
                break;
            case 'lock':
                if ($topic->isLocked() === true) {
                    $this->get('teapotio.forum.topic')->unlock($topic);
                }
                else {
                    $this->get('teapotio.forum.topic')->lock($topic);
                }
                break;
            case 'flag':
                $this->get('teapotio.forum.flag')->flag($topic, $this->get('security.context')->getToken()->getUser());
                break;
        }

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array('success' => 1));
        }

        return $this->redirect(
            $this->get('request')->headers->get('referer')
        );
    }
}
