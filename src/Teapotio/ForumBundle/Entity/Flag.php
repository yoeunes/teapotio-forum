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
use Teapotio\Base\ForumBundle\Entity\Flag as BaseFlag;
use Doctrine\Common\Collections\ArrayCollection;

use Teapotio\Base\ForumBundle\Entity\FlagInterface;

/**
 * Teapotio\ForumBundle\Entity\Flag
 *
 * @ORM\Table(name="`forum_flag`")
 * @ORM\Entity(repositoryClass="Teapotio\ForumBundle\Repository\FlagRepository")
 */
class Flag extends BaseFlag implements FlagInterface
{
    /**
     * @var ArrayCollection $users
     *
     * @ORM\ManyToMany(targetEntity="Teapotio\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $users;

    /**
     * @var \Teapotio\ForumBundle\Entity\Topic $topic
     *
     * @ORM\OneToOne(targetEntity="Teapotio\ForumBundle\Entity\Topic", fetch="EAGER")
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id")
     */
    protected $topic;

    /**
     * @var \Teapotio\ForumBundle\Entity\Message $message
     *
     * @ORM\OneToOne(targetEntity="Teapotio\ForumBundle\Entity\Message", fetch="EAGER")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    protected $message;

    /**
     * @var \Teapotio\ForumBundle\Entity\Moderation $moderation
     *
     * @ORM\OneToOne(targetEntity="Teapotio\ForumBundle\Entity\Moderation", inversedBy="flag", fetch="EAGER")
     * @ORM\JoinColumn(name="moderation_id", referencedColumnName="id")
     */
    protected $moderation;
}
