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
use Teapotio\Base\ForumBundle\Entity\MessageStar as BaseMessageStar;

use Teapotio\Base\ForumBundle\Entity\MessageStarInterface;

/**
 * Teapotio\ForumBundle\Entity\MessageStar
 *
 * @ORM\Table(name="forum_message_star")
 * @ORM\Entity(repositoryClass="Teapotio\ForumBundle\Repository\MessageStarRepository")
 */
class MessageStar extends BaseMessageStar implements MessageStarInterface
{

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="Teapotio\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var \Teapotio\ForumBundle\Entity\Message $message
     *
     * @ORM\ManyToOne(targetEntity="Teapotio\ForumBundle\Entity\Message", inversedBy="stars")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    protected $message;

}