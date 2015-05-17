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
use Teapotio\ForumBundle\Form\Type\CreateTopicType;

use Teapotio\Base\ForumBundle\Entity\TopicInterface;
use Teapotio\Base\ForumBundle\Exception\DuplicateTopicException;
use Symfony\Component\Form\FormError;

class TopicController extends BaseController
{
    public function newAction($boardSlug = null)
    {
        $board = $this->getBoard();
        $user = $this->getUser();

        $this->throwAccessDeniedIfLoggedOut();
        $this->throwAccessDeniedIfPermission('canCreateTopic', $user, $board);

        $request = $this->get('request');

        // List of existing topics with the same slug or title
        $existingTopics = array();

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
                try {
                    $topic->setBoard($board);
                    $this->get('teapotio.forum.topic')->save($topic);

                    $message = new Message();
                    $message->setBody($form['body']->getData());
                    $message->setTopic($topic);
                    $message->setIsTopicBody(true);

                    $this->get('teapotio.forum.message')->save($message);

                    return $this->redirect($this->get('teapotio.forum')->forumPath('ForumListMessagesByTopic', $topic));
                } catch (DuplicateTopicException $e) {
                    $form->addError(new FormError($this->get('translator')->trans('Topic.title.is.already.in.use')));
                    $existingTopics = $e->topics;
                }
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
            'form'            => $form->createView(),
            'current_board'   => $board,
            'page_title'      => $title,
            'info_notices'    => $infoNotices,
            'existing_topics' => $existingTopics,
        );

        return $this->superRender('TeapotioForumBundle:page:topic/new.html.twig', $params);
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

        // List of existing topics with the same slug or title
        $existingTopics = array();

        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            if ($form->isValid() === true) {
                try {
                    $message->setBody($form['body']->getData());

                    $this->get('teapotio.forum.topic')->save($topic);
                    $this->get('teapotio.forum.message')->save($message);

                    return $this->redirect($this->get('teapotio.forum')->forumPath('ForumListMessagesByTopic', $topic));
                } catch (DuplicateTopicException $e) {
                    $form->addError(new FormError($this->get('translator')->trans('Topic.title.is.already.in.use')));
                    $existingTopics = $e->topics;
                }
            }

        }

        $infoNotices = array();
        $title = $this->generateTitle('Edit.topic.%title%', array('%title%' => $topic->getTitle()));

        $params = array(
            'form'            => $form->createView(),
            'message'         => $message,
            'topic'           => $topic,
            'existing_topics' => $existingTopics,
            'current_board'   => $board,
            'page_title'      => $title,
            'info_notices'    => $infoNotices,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioForumBundle:partial:topic/edit.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioForumBundle:page:topic/edit.html.twig', $params);
    }

    public function latestAction()
    {
        if ($this->get('teapotio.forum.board')->getViewableBoards()->count() === 0) {
            if ($this->get('request')->isXmlHttpRequest() === true) {
                $html = $this->get('teapotio.user')
                             ->renderAccessRestricted('TeapotioForumBundle:partial:topic/nonAuthorized.html.twig');
                return $this->renderJson(array(
                    'html'   => $html,
                    'title'  => $title
                ));
            }

            $html = $this->get('teapotio.user')
                         ->renderAccessRestricted('TeapotioForumBundle:page:topic/nonAuthorized.html.twig');

            return $this->renderHtml($html);
        }

        $topicsPerPage = $this->get('teapotio.forum')->getTotalTopicsPerPage();
        $messagesPerPage = $this->get('teapotio.forum')->getTotalMessagesPerPage();

        $page = ($this->get('request')->get('page') === null) ? 1 : $this->get('request')->get('page');
        $offset = ($page - 1) * $topicsPerPage;

        $topics = $this->get('teapotio.forum.topic')->getLatestTopics($offset, $topicsPerPage);

        $boards = $this->get('teapotio.forum.board')->getBoards();

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
                'html'   => $this->renderView('TeapotioForumBundle:partial:topic/list.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioForumBundle:page:topic/list.html.twig', $params);
    }

    public function listAction($boardSlug)
    {
        $board = $this->getBoard();

        if ($board === null) {
            throw $this->createNotFoundException();
        }

        // Making sure it's the right URL
        $realBoardSlug = $this->container->get('teapotio.forum.board')->buildSlug($board);
        if ($realBoardSlug !== $boardSlug) {
            return $this->redirect($this->get('teapotio.forum')->forumPath('ForumListTopicsByBoard', $board));
        }

        $user = $this->getUser();
        $this->throwAccessDeniedIfPermission('canView', $user, $board);

        $boardIds = $this->get('teapotio.forum.board')->getChildrenIdsFromBoard($board, $user);
        $boardIds[] = $board->getId();

        $topicsPerPage = $this->get('teapotio.forum')->getTotalTopicsPerPage();
        $messagesPerPage = $this->get('teapotio.forum')->getTotalMessagesPerPage();

        list($pinnedTopics, $topics) = $this->get('teapotio.forum.topic')->getProcessedListTopicsByBoardIds($boardIds);

        $pinnedTopicIds = $pinnedTopics->map(function (TopicInterface $topic) {
          return $topic->getId();
        });

        $title = $this->generateTitle('All.topics.in.%title%', array('%title%' => $board->getTitle()));

        $params = array(
            'topics_per_page'   => $topicsPerPage,
            'messages_per_page' => $messagesPerPage,
            'pinned_topics'     => $pinnedTopics,
            'pinned_topic_ids'  => $pinnedTopicIds,
            'topics'            => $topics,
            'current_board'     => $board,
            'showBoard'         => true,
            'page_title'        => $title,
        );

        return $this->superRender('TeapotioForumBundle:page:topic/list.html.twig', $params);
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

        $toggle = null;

        switch ($action) {
            case 'pin':
                $toggle = !$topic->isPinned();
                if ($topic->isPinned() === true) {
                    $this->get('teapotio.forum.topic')->unpin($topic);
                }
                else {
                    $this->get('teapotio.forum.topic')->pin($topic);
                }
                break;
            case 'delete':
                $toggle = !$topic->isDeleted();
                if ($topic->isDeleted() === true) {
                    $this->get('teapotio.forum.topic')->undelete($topic, true); // true: bubble down
                }
                else {
                    $this->get('teapotio.forum.topic')->delete($topic, true); // true: bubble down
                }
                break;
            case 'lock':
                $toggle = !$topic->isLocked();
                if ($topic->isLocked() === true) {
                    $this->get('teapotio.forum.topic')->unlock($topic);
                }
                else {
                    $this->get('teapotio.forum.topic')->lock($topic);
                }
                break;
            case 'flag':
                $toggle = true;
                $this->get('teapotio.forum.flag')->flag($topic, $this->get('security.context')->getToken()->getUser());
                break;
        }

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array('success' => 1, 'toggle' => (int)$toggle));
        }

        return $this->redirect(
            $this->get('request')->headers->get('referer')
        );
    }
}
