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

        // We know we'll build the board list eventually so we get all of them
        $this->container->get('teapotio.forum.board')->getBoards();

        $board = null;

        if ($event->getRequest()->attributes->get('boardSlug') !== null
            && method_exists($controller, 'setBoard') === true) {

            $board = $this->container->get('teapotio.forum.path')->getCurrentBoard();

            if ($board === null) {
                throw $controller->createNotFoundException();
            }
            else {
                $controller->setBoard($board);
            }
        }

        if ($event->getRequest()->attributes->get('topicSlug') !== null
            && method_exists($controller, 'setTopic') === true
            && $board !== null) {

            $topic = $this->container->get('teapotio.forum.path')->getCurrentTopic();

            if ($topic === null) {
                throw $controller->createNotFoundException();
            }
            else {
                $controller->setTopic($topic);
            }
        }

        if ($event->getRequest()->attributes->get('messageId') !== null
            && method_exists($controller, 'setMessage') === true) {

            $message = $this->container->get('teapotio.forum.path')->getCurrentMessage();

            if ($message === null) {
                throw $controller->createNotFoundException();
            }
            else {
                $controller->setMessage($message);
            }
        }

    }
}
