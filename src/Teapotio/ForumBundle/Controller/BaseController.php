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
use Teapotio\ForumBundle\Entity\Topic;
use Teapotio\ForumBundle\Entity\Message;

use Teapotio\Components\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends Controller
{
    /**
     * @var Board
     */
    protected $board = null;

    /**
     * @var Topic
     */
    protected $topic = null;

    /**
     * @var Message
     */
    protected $message = null;

    /**
     * Attach a board to a controller
     *
     * @param  Board  $board
     */
    public function setBoard(Board $board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get an attached board from the controller
     *
     * @return Board
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Attach a topic to a controller
     *
     * @param  Topic  $topic
     */
    public function setTopic(Topic $topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get an attached topic from the controller
     *
     * @return Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Attach a message to a controller
     *
     * @param  Message  $message
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get an attached message from the controller
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get a Board and a Topic
     * Throw not found exception if the topic and/or the board was not found
     *
     * @param  integer   $boardId
     * @param  integer   $topicId
     *
     * @return array(Board, Topic)
     */
    protected function getBoardAndTopic($boardId, $topicId)
    {
        $board = $this->get('teapotio.forum.board')->getById($boardId);

        if ($board === null) {
            throw $this->createNotFoundException();
        }

        $topic = $this->get('teapotio.forum.topic')->getById($topicId);

        if ($topic === null) {
            throw $this->createNotFoundException();
        }

        return array($board, $topic);
    }

    /**
     * Is URL Valid will return true if the url matches the values
     * in the given Board and the given Topic
     *
     * @param  Board   $board
     * @param  Topic   $topic
     * @param  string  $boardSlug
     * @param  string  $topicSlug
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|true
     */
    protected function isUrlValid(Board $board, Topic $topic, $boardSlug, $topicSlug)
    {
        /**
         * Making sure it's the right URL
         */
        $realBoardSlug = $this->container->get('teapotio.forum.board')->buildSlug($board);
        if ($realBoardSlug !== $boardSlug || $topic->getSlug() !== $topicSlug) {
            return $this->redirect(
                $this->get('teapotio.forum')->forumPath(
                    'ForumListMessagesByTopic',
                    $this->getTopic()
                )
            );
        }

        return true;
    }

}
