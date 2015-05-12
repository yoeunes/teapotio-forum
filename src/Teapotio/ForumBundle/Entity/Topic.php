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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Teapotio\Base\ForumBundle\Entity\Topic as BaseTopic;

use Teapotio\Base\ForumBundle\Entity\TopicInterface;

/**
 * Teapotio\ForumBundle\Entity\topic
 *
 * @ORM\Table(name="forum_topic", indexes={
 *     @ORM\Index(name="slug_idx", columns={"slug"}),
 *     @ORM\Index(name="last_message_date_idx", columns={"last_message_date"}),
 *     @ORM\Index(name="total_messages_idx", columns={"total_posts"})
 * })
 * @ORM\Entity(repositoryClass="Teapotio\ForumBundle\Repository\TopicRepository")
 */
class Topic extends BaseTopic implements TopicInterface
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
     * @ORM\ManyToOne(targetEntity="Teapotio\ForumBundle\Entity\Board", inversedBy="topics")
     * @ORM\JoinColumn(name="board_id", referencedColumnName="id")
     */
    protected $board;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Teapotio\ForumBundle\Entity\Message", mappedBy="topic")
     */
    protected $messages;

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="Teapotio\UserBundle\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(name="last_user_id", referencedColumnName="id")
     */
    protected $lastUser;

    /**
     * @var integer $legacyId
     *
     * @ORM\Column(name="legacy_id", type="integer", nullable=true)
     */
    protected $legacyId;

    /**
     * Set legacyId
     *
     * @param integer $legacyId
     * @return User
     */
    public function setLegacyId($legacyId)
    {
        $this->legacyId = $legacyId;

        return $this;
    }

    /**
     * Get legacyId
     *
     * @return integer
     */
    public function getLegacyId()
    {
        return $this->legacyId;
    }

}
