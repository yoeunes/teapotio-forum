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

use Teapotio\Base\UserBundle\Entity\UserToken as BaseUserToken;

use Doctrine\ORM\Mapping as ORM;

/**
 * Teapotio\UserBundle\Entity\UserToken
 *
 * @ORM\Table(name="`user_token`")
 * @ORM\Entity()
 */
class UserToken extends BaseUserToken
{

    /**
     * @var User $user
     *
     * @ORM\OneToOne(targetEntity="\Teapotio\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

}