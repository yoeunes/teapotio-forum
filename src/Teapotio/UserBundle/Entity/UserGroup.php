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

use Teapotio\Base\UserBundle\Entity\UserGroup as BaseUserGroup;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\RoleInterface;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Teapotio\UserBundle\Entity\UserGroup
 *
 * @ORM\Table(name="`user_group`")
 * @ORM\Entity()
 * @UniqueEntity("name")
 * @UniqueEntity("role")
 */
class UserGroup extends BaseUserGroup
{
    /**
     * @var ArrayCollection $displayUsers
     *
     * @ORM\OneToMany(targetEntity="\Teapotio\UserBundle\Entity\User", mappedBy="displayGroup")
     */
    protected $displayUsers;

    /**
     * @var ArrayCollection $users
     *
     * @ORM\ManyToMany(targetEntity="\Teapotio\UserBundle\Entity\User", mappedBy="groups")
     */
    protected $users;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="permissions", nullable=true)
     */
    protected $permissions;

    /**
     * Returns a multidimensioal array of permissions
     *
     * First level key can be "VIEW", "EDIT", "CREATE", "DELETE"
     * Second level key is the type of the object
     * Third level values are the IDs of the authorized IDs
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set the permissions
     *
     * @param   array   $array
     *
     * @return  UserGroup
     */
    public function setPermissions($array)
    {
        $this->permissions = $array;

        return $this;
    }

    /**
     * Set a single permission on an object
     *
     * @param string  $action
     * @param object  $entity
     *
     * @return  UserGroup
     */
    public function addPermission($action, $entity)
    {
        $this->permissions[strtoupper($action)][get_class($entity)][] = $entity->getId();

        return $this;
    }

}
