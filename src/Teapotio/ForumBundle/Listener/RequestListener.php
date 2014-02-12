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

namespace Teapotio\ForumBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpKernel\HttpKernel;

class RequestListener
{
    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(FilterControllerEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()
            || $this->container->get('security.context')->getToken() === null) {
            // don't do anything if it's not the master request
            return;
        }

        $controller = $event->getController()[0];

        $useId = $this->container->getParameter('teapotio.forum.url.use_id');

        // We know we'll build the board list eventually so we get all of them
        $this->container->get('teapotio.forum.board')->getBoards();

        if ($event->getRequest()->attributes->get('boardSlug') !== null
            && method_exists($controller, 'setBoard') === true) {

            $board = $this->container->get('teapotio.forum.path')->lookupBoard();

            if ($board === null) {
                throw $controller->createNotFoundException();
            }
            else {
                $controller->setBoard($board);
            }
        }

        if ($event->getRequest()->attributes->get('topicSlug') !== null
            && method_exists($controller, 'setTopic') === true) {
            // the topic slug in the URL
            $topicSlug = $event->getRequest()->attributes->get('topicSlug');

            // if the URL uses IDs
            if ($useId === true) {
                $tmpParts = explode('-', $topicSlug);
                $topicId = array_pop($tmpParts);

                $topic = $this->container->get('teapotio.forum.topic')->getById($topicId);
            }
            else {
                $topic = $this->container->get('teapotio.forum.topic')->getBySlug($topicSlug);
            }

            if ($topic === null) {
                throw $controller->createNotFoundException();
            }
            else {
                $controller->setTopic($topic);
            }
        }

        if ($event->getRequest()->attributes->get('messageId') !== null
            && method_exists($controller, 'setMessage') === true) {
            $messageId = $event->getRequest()->attributes->get('messageId');

            $message = $this->container->get('teapotio.forum.message')->find($messageId, null);

            if ($message === null) {
                throw $controller->createNotFoundException();
            }
            else {
                $controller->setMessage($message);
            }
        }

    }
}