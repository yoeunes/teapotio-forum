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

namespace Teapotio\UserBundle\Repository;

use Teapotio\Base\UserBundle\Repository\UserRepository as BaseUserRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\NoResultException;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserRepository extends BaseUserRepository
{

    /**
     * Get a collection of Users by user ids
     *
     * @param  array  $ids
     *
     * @return ArrayCollection
     */
    public function getByIds($ids)
    {
        $queryBuilder = $this->createQueryBuilder('u')
                             ->select(array('u', 'i', 'g', 's', 'fs'))
                             ->leftJoin('u.defaultAvatar', 'i')
                             ->leftJoin('u.groups', 'g')
                             ->leftJoin('u.settings', 's')
                             ->leftJoin('u.forumStat', 'fs');

        $queryBuilder->where($queryBuilder->expr()->in('u.id', $ids));

        return new ArrayCollection($queryBuilder->getQuery()->getResult());
    }

    /**
     * Overriding to include any joins
     *
     * @param  string $username
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->select(array('u', 'us', 'ug', 'ufs'))
            ->where('u.username = :username OR u.email = :email')
            ->leftJoin('u.settings', 'us')
            ->leftJoin('u.groups', 'ug')
            ->leftJoin('u.forumStat', 'ufs')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
        ;

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            throw new UsernameNotFoundException(sprintf('Unable to find an active admin AcmeUserBundle:User object identified by "%s".', $username), null, 0, $e);
        }

        return $user;
    }

}
