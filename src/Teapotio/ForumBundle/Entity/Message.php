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

namespace Teapotio\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Teapotio\Base\ForumBundle\Entity\Message as BaseMessage;

use Teapotio\Base\ForumBundle\Entity\MessageInterface;

/**
 * Teapotio\ForumBundle\Entity\Message
 *
 * @ORM\Table(name="forum_message")
 * @ORM\Entity(repositoryClass="Teapotio\ForumBundle\Repository\MessageRepository")
 */
class Message extends BaseMessage implements MessageInterface
{

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="Teapotio\UserBundle\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var \Teapotio\ForumBundle\Entity\Topic $topic
     *
     * @ORM\ManyToOne(targetEntity="Teapotio\ForumBundle\Entity\Topic", inversedBy="messages", fetch="EAGER")
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id")
     */
    protected $topic;

    /**
     * @var ArrayCollection $stars
     *
     * @ORM\OneToMany(targetEntity="Teapotio\ForumBundle\Entity\MessageStar", mappedBy="message")
     */
    protected $stars;

}