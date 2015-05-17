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
use Symfony\Component\HttpKernel\HttpKernel;

class RequestListener
{
    protected $securityContext;
    protected $boardService;
    protected $pathService;

    public function __construct($securityContext, $boardService, $pathService)
    {
        $this->securityContext = $securityContext;
        $this->boardService = $boardService;
        $this->pathService = $pathService;
    }

    public function onKernelRequest(FilterControllerEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()
            || $this->securityContext->getToken() === null) {
            // don't do anything if it's not the master request
            return;
        }

        $controller = $event->getController()[0];

        // We know we'll build the board list eventually so we get all of them
        $this->boardService->getBoards();

        $board = null;

        if ($event->getRequest()->attributes->get('boardSlug') !== null
            && method_exists($controller, 'setBoard') === true) {

            $board = $this->pathService->getCurrentBoard();

            if ($board === null) {
                throw $controller->createNotFoundException();
            }
        }

        if ($event->getRequest()->attributes->get('topicSlug') !== null
            && method_exists($controller, 'setTopic') === true
            && $board !== null) {

            $topic = $this->pathService->getCurrentTopic();

            if ($topic === null) {
                throw $controller->createNotFoundException();
            }
        }

        if ($event->getRequest()->attributes->get('messageId') !== null
            && method_exists($controller, 'setMessage') === true) {

            $message = $this->pathService->getCurrentMessage();

            if ($message === null) {
                throw $controller->createNotFoundException();
            }
        }

    }
}
