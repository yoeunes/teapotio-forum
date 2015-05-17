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

namespace Teapotio\ForumBundle\Service;

use Teapotio\ForumBundle\Entity\Topic;
use Teapotio\Base\ForumBundle\Service\TopicService as BaseTopicService;
use Teapotio\Base\ForumBundle\Entity\BoardInterface;

use Symfony\Component\Security\Core\User\UserInterface;

class TopicService extends BaseTopicService
{
    public function createTopic()
    {
        return new Topic();
    }

    /**
     * Create the first topic
     *
     * @param  UserInterface   $user
     * @param  BoardInterface  $board
     *
     * @return array
     */
    public function setup(UserInterface $user, BoardInterface $board)
    {
        $topic = $this->createTopic();

        $topic->setTitle('Welcome on your new Teapotio forum!');
        $topic->setUser($user);
        $topic->setSlug();
        $topic->setBoard($board);

        $this->save($topic);

        $message = $this->container->get('teapotio.forum.message')->createMessage();
        $message->setBody("<p>We would like to welcome you and we hope you will enjoy Teapotio forum as much as we do.");
        $message->setIsTopicBody(true);
        $message->setPosition(1);
        $message->setUser($user);
        $message->setTopic($topic);

        $this->container->get('teapotio.forum.message')->save($message);

        return array($topic);
    }
}
