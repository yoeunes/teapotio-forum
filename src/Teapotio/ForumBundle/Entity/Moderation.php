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
use Teapotio\Base\ForumBundle\Entity\Moderation as BaseModeration;

use Teapotio\Base\ForumBundle\Entity\ModerationInterface;

/**
 * Teapotio\ForumBundle\Entity\Moderation
 *
 * @ORM\Table(name="`forum_moderation`")
 * @ORM\Entity(repositoryClass="Teapotio\ForumBundle\Repository\ModerationRepository")
 */
class Moderation extends BaseModeration implements ModerationInterface
{

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="Teapotio\UserBundle\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var \Teapotio\ForumBundle\Entity\Board $board
     *
     * @ORM\ManyToOne(targetEntity="Teapotio\ForumBundle\Entity\Board")
     * @ORM\JoinColumn(name="board_id", referencedColumnName="id")
     */
    protected $board;

    /**
     * @var \Teapotio\ForumBundle\Entity\Topic $topic
     *
     * @ORM\ManyToOne(targetEntity="Teapotio\ForumBundle\Entity\Topic", fetch="EAGER")
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id")
     */
    protected $topic;

    /**
     * @var \Teapotio\ForumBundle\Entity\Message $message
     *
     * @ORM\ManyToOne(targetEntity="Teapotio\ForumBundle\Entity\Message", fetch="EAGER")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    protected $message;

    /**
     * @var \Teapotio\ForumBundle\Entity\Flag $flag
     *
     * @ORM\OneToOne(targetEntity="Teapotio\ForumBundle\Entity\Flag", mappedBy="moderation", fetch="EAGER")
     */
    protected $flag;
}