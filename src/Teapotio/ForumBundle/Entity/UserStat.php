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
use Teapotio\Base\ForumBundle\Entity\UserStat as BaseUserStat;

use Teapotio\Base\ForumBundle\Entity\UserStatInterface;

/**
 * Teapotio\ForumBundle\Entity\UserStat
 *
 * @ORM\Table(name="`forum_user_stat`")
 * @ORM\Entity(repositoryClass="Teapotio\ForumBundle\Repository\UserStatRepository")
 */
class UserStat extends BaseUserStat implements UserStatInterface
{

    /**
     * @var User $user
     *
     * @ORM\OneToOne(targetEntity="Teapotio\UserBundle\Entity\User", inversedBy="forumStat")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

}
