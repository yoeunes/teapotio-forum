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

use Teapotio\ForumBundle\Entity\Message;
use Teapotio\ForumBundle\Form\Type\CreateMessageType;

use Doctrine\Common\Collections\ArrayCollection;

class MessageController extends BaseController
{
    public function newAction($boardSlug, $topicSlug)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $board = $this->getBoard();
        $topic = $this->getTopic();
        $user = $this->getUser();

        $request = $this->get('request');

        $message = new Message();
        $message->setUser($user);

        if ($this->get('teapotio.forum.access_permission')->canCreateMessage($user, $board) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $form = $this->createForm(new CreateMessageType(), $message);

        if ($request->getMethod() === 'POST') {
            $form->bind($request);
            if ($form->isValid() === true) {
                $message->setTopic($topic);
                $this->get('teapotio.forum.message')->save($message);

                /**
                 * Redirect to Topic
                 */
                return $this->redirect(
                    $this->get('teapotio.forum')->forumPath('ForumListMessagesByTopic', $topic)
                    ."#". $this->get('translator')->trans('bottom')
                );
            }
        }

        return $this->render('TeapotioForumBundle:page:message/new.html.twig', array(
            'form'          => $form->createView(),
            'current_board' => $board,
            'topic'         => $topic,
            'message'       => $message,
        ));
    }

    public function editAction($boardSlug, $topicSlug, $messageId)
    {
        $message = $this->getMessage();
        $topic = $this->getTopic();
        $board = $this->getBoard();

        $form = $this->createForm(new CreateMessageType(), $message);

        $title = $this->generateTitle('Edit.message');

        $params = array(
            'current_board' => $board,
            'current_topic' => $topic,
            'form'          => $form->createView(),
            'message'       => $message,
            'page_title'    => $title,
        );

        $request = $this->get('request');

        if ($request->getMethod() === 'POST') {

            $form->bind($request);

            if ($form->isValid() === true) {
                $this->get('teapotio.forum.message')->save($message);

                return $this->redirect(
                    $this->get('teapotio.forum')->forumPath('ForumListMessagesByTopic', $message->getTopic())
                    . '#message-' . $message->getPosition()
                );
            }
        }

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'title' => $title,
                'html'  => $this->renderView('TeapotioForumBundle:partial:message/edit.html.twig', $params),
            ));
        }

        return $this->render('TeapotioForumBundle:page:message/edit.html.twig', $params);
    }

    public function flagAction($boardSlug, $topicSlug, $messageId)
    {
        return $this->manipulateMessage('flag', $boardSlug, $topicSlug, $messageId);
    }

    public function deleteAction($boardSlug, $topicSlug, $messageId)
    {
        $message = $this->getMessage();
        $user = $this->getUser();

        if ($this->get('teapotio.forum.access_permission')->canDelete($user, $message) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        return $this->manipulateMessage('delete', $boardSlug, $topicSlug, $messageId);
    }

    public function starAction($boardSlug, $topicSlug, $messageId)
    {
        return $this->manipulateMessage('star', $boardSlug, $topicSlug, $messageId);
    }

    public function unstarAction($boardSlug, $topicSlug, $messageId)
    {
        return $this->manipulateMessage('unstar', $boardSlug, $topicSlug, $messageId);
    }

    private function manipulateMessage($action, $boardSlug, $topicSlug, $messageId)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $board = $this->getBoard();
        $topic = $this->getTopic();

        if (true !== $response = $this->isUrlValid($board, $topic, $boardSlug, $topicSlug)) {
            return $response;
        }

        $message = $this->getMessage();

        switch ($action) {
            case 'delete':
                if ($message->isDeleted() === true) {
                    $this->get('teapotio.forum.message')->undelete($message, true); // true: bubble up
                }
                else {
                    $this->get('teapotio.forum.message')->delete($message, true); // true: bubble up
                }
                break;
            case 'flag':
                if ($message->isTopicBody() === true) {
                    $this->get('teapotio.forum.flag')->flag($message->getTopic(), $this->get('security.context')->getToken()->getUser());
                }
                else {
                    $this->get('teapotio.forum.flag')->flag($message, $this->get('security.context')->getToken()->getUser());
                }
                break;
            case 'star':
                    $this->get('teapotio.forum.message_star')->star($message, $this->get('security.context')->getToken()->getUser());
                break;
            case 'unstar':
                    $this->get('teapotio.forum.message_star')->unstar($message, $this->get('security.context')->getToken()->getUser());
                break;
        }

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array('success' => 1));
        }

        return $this->redirect(
            $this->get('request')->headers->get('referer')
        );
    }

    public function quoteAction()
    {
        $html = $this->get('teapotio.forum.message')->renderBodyQuote($this->getMessage());

        $html = $this->get('teapotio.forum.message')->parseRenderedHtml($html);

        return $this->renderJson(array('html' => $html));
    }

    public function replyAction()
    {
        $html = $this->get('teapotio.forum.message')->renderBodyReply($this->getMessage()->getUser());

        $html = $this->get('teapotio.forum.message')->parseRenderedHtml($html);

        return $this->renderJson(array('html' => $html));
    }

    public function listAction($boardSlug, $topicSlug)
    {
        $board = $this->getBoard();
        $topic = $this->getTopic();
        $user = $this->getUser();

        $isUserModerator = $this->get('teapotio.forum.access_permission')->isModerator($user, $board);

        if (true !== $response = $this->isUrlValid($board, $topic, $boardSlug, $topicSlug)) {
            return $response;
        }

        $this->throwAccessDeniedIfPermission('canView', $user, $topic);

        if ($isUserModerator === false && $topic->isDeleted() === true) {
            throw $this->createNotFoundException();
        }

        $this->get('teapotio.forum.topic')->view($topic);

        // Build the form if the user is allowed
        $form = $message = null;
        if ($this->get('teapotio.forum.access_permission')->canCreateMessage($user, $board) === true) {
            $message = new Message();
            $message->setUser($user);
            $message->setBody($this->renderView(
                'TeapotioForumBundle:component:rules.html.twig',
                array('prefix' => $this->get('translator')->trans('Add.a.new.message'))
            ));

            $form = $this->createForm(new CreateMessageType(), $message, array('new_entry' => true))->createView();
            $message = $form->vars['value'];
        }

        $messagesPerPage = $this->get('teapotio.forum')->getTotalMessagesPerPage();
        $page = ($this->get('request')->get('page') === null) ? 1 : $this->get('request')->get('page');
        $offset = ($page - 1) * $messagesPerPage;

        $isDeleted = $isUserModerator === true ? null : false;

        $messages = $this->get('teapotio.forum.message')->getMessagesByTopic($topic, $offset, $messagesPerPage, $isDeleted);

        $stars = $this->get('teapotio.forum.message_star')->getStarsByMessages($messages);
        $userStars = $this->get('teapotio.forum.message_star')->getUserStarsByMessages($messages);

        // Load user models - it reduces the number of queries
        $userIds = array();
        foreach ($messages as $message) {
          $userIds[] = $message->getUser()->getId();
        }
        $users = $this->get('teapotio.user')->getByIds($userIds);

        $flags = new ArrayCollection();
        $flagTopic = null;
        if ($isUserModerator === true) {
            // The potential flag of the topic
            $flagTopic = $this->get('teapotio.forum.flag')->getByTopic($topic);

            // The potential flags in the list
            $flags = $this->get('teapotio.forum.flag')->getByMessages($messages, $board);
        }

        $this->get('teapotio.forum.message')->parseOutputBodies($messages);

        $title = $this->generateTitle('%title%', array('%title%' => $topic->getTitle()));

        $params = array(
            'messages_per_page'   => $messagesPerPage,
            'messages'            => $messages,
            'messages_stars'      => $stars,
            'messages_user_stars' => $userStars,
            'messages_users'      => $users,
            'flags'               => $flags,
            'flag_topic'          => $flagTopic,
            'stars'               => $stars,
            'current_board'       => $board,
            'topic'               => $topic,
            'message'             => $message,
            'form'                => $form,
            'page_title'          => $title
        );

        return $this->superRender('TeapotioForumBundle:page:message/list.html.twig', $params);
    }
}
