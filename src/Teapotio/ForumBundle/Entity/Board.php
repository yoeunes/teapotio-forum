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

use Doctrine\Common\Collections\ArrayCollection;
use Teapotio\Base\ForumBundle\Entity\Board as BaseBoard;

use Teapotio\Base\ForumBundle\Entity\BoardInterface;

/**
 * Teapotio\ForumBundle\Entity\Board
 *
 * @ORM\Table(name="forum_board", indexes={
 *     @ORM\Index(name="slug_idx", columns={"slug"})
 * })
 * @ORM\Entity(repositoryClass="Teapotio\ForumBundle\Repository\BoardRepository")
 */
class Board extends BaseBoard implements BoardInterface
{

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="Teapotio\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var ArrayCollection $topics
     *
     * @ORM\OneToMany(targetEntity="Teapotio\ForumBundle\Entity\Topic", mappedBy="board")
     */
    protected $topics;

    /**
     * @var \Teapotio\ForumBundle\Entity\Board $board
     *
     * @ORM\ManyToOne(targetEntity="Teapotio\ForumBundle\Entity\Board", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @var ArrayCollection $children
     *
     * @ORM\OneToMany(targetEntity="Teapotio\ForumBundle\Entity\Board", mappedBy="parent")
     */
    protected $children;

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
