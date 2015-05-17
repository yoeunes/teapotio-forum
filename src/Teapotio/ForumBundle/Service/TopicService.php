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
use Teapotio\Base\ForumBundle\Service\TopicServiceInterface;
use Teapotio\Base\ForumBundle\Service\TopicService as BaseTopicService;
use Teapotio\Base\ForumBundle\Entity\BoardInterface;
use Teapotio\Base\ForumBundle\Entity\TopicInterface;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

class TopicService extends BaseTopicService implements TopicServiceInterface
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
        $message->setTopicBody(true);
        $message->setPosition(1);
        $message->setUser($user);
        $message->setTopic($topic);

        $this->container->get('teapotio.forum.message')->save($message);

        return array($topic);
    }

    /**
     * Returns an array of two lists
     *  - a list of pinned topics with their body loaded
     *  - a list of topics without their body
     *
     * @param  array $boardIds
     *
     * @return array(ArrayCollection, Paginator)
     */
    public function getProcessedListTopicsByBoardIds($boardIds)
    {
        $topicsPerPage = $this->container->get('teapotio.forum')->getTotalTopicsPerPage();

        $page = ($this->container->get('request')->get('page') === null) ? 1 : $this->container->get('request')->get('page');
        $offset = ($page - 1) * $topicsPerPage;

        $board = $this->container->get('teapotio.forum.path')->getCurrentBoard();

        $pinnedTopics = new ArrayCollection();
        $pinnedTopicIds = new ArrayCollection();
        if (count($boardIds) === 1) {
            $topics = $this->container->get('teapotio.forum.topic')->getLatestTopicsByBoard($board, $offset, $topicsPerPage);
            // Only load on first page
            if ($page === 1) {
                foreach ($topics as $topic) {
                  if ($topic->isPinned() === false) {
                    break;
                  }

                  $pinnedTopics->add($topic);
                  $pinnedTopicIds->add($topic->getId());
                }
            }
        }
        else {
            $topics = $this->container->get('teapotio.forum.topic')->getLatestTopicsByBoardIds($boardIds, $offset, $topicsPerPage);
            // Only load on first page
            if ($page === 1) {
                $pinnedTopics = $this->container->get('teapotio.forum.topic')->getLatestPinnedTopicsByBoard($board);
                $pinnedTopicIds = $pinnedTopics->map(function (TopicInterface $topic) {
                  return $topic->getId();
                });
            }
        }

        if ($pinnedTopicIds->count()) {
          $bodies = $this->container->get('teapotio.forum.message')->getTopicBodiesByTopicIds($pinnedTopicIds->toArray());

          foreach ($pinnedTopics as $topic) {
            if (isset($bodies[$topic->getId()])) {
              $topic->setBody($bodies[$topic->getId()]);
            }
          }
        }

        return array($pinnedTopics, $topics);
    }
}
