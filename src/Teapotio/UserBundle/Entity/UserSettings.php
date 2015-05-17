<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    UserBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Teapotio\UserBundle\Entity\UserSettings
 *
 * @ORM\Table(name="`user_settings`")
 * @ORM\Entity()
 */
class UserSettings
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection $users
     *
     * @ORM\OneToOne(targetEntity="\Teapotio\UserBundle\Entity\User", inversedBy="settings")
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="background_color", type="string", length=6, nullable=true)
     */
    protected $backgroundColor;

    /**
     * @var string
     *
     * @ORM\Column(name="background_tiled", type="boolean", nullable=true)
     */
    protected $backgroundTiled = false;

    /**
     * @var \Teapotio\ImageBundle\Entity\Image $backgroumdImage
     *
     * @ORM\ManyToOne(targetEntity="\Teapotio\ImageBundle\Entity\Image")
     * @ORM\JoinColumn(name="background_image_id")
     */
    protected $backgroundImage;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     * @return UserSettings
     */
    public function setUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Set backgroundColor
     *
     * @param string $backgroundColor
     * @return UserSettings
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = str_replace("#", "", $backgroundColor);

        return $this;
    }

    /**
     * Get backgroundColor
     *
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * Set backgroundImage
     *
     * @param Teapotio\ImageBundle\Entity\Image $backgroundImage
     * @return UserSettings
     */
    public function setBackgroundImage(\Teapotio\ImageBundle\Entity\Image $backgroundImage = null)
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }

    /**
     * Get backgroundImage
     *
     * @return Teapotio\ImageBundle\Entity\Image
     */
    public function getBackgroundImage()
    {
        return $this->backgroundImage;
    }

    /**
     * Set backgroundTiled
     *
     * @param boolean $backgroundTiled
     * @return UserSettings
     */
    public function setBackgroundTiled($backgroundTiled)
    {
        $this->backgroundTiled = $backgroundTiled;

        return $this;
    }

    /**
     * Get backgroundTiled
     *
     * @return boolean
     */
    public function getBackgroundTiled()
    {
        return $this->backgroundTiled;
    }
}
