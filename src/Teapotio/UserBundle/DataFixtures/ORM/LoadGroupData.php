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

namespace Teapotio\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Teapotio\UserBundle\Entity\UserGroup;

class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $adminGroup = new UserGroup();
        $adminGroup->setName('Admin');
        $adminGroup->setRole('ROLE_ADMIN');

        $manager->persist($adminGroup);

        $userGroup = new UserGroup();
        $userGroup->setName('User');
        $userGroup->setRole('ROLE_USER');

        $manager->persist($userGroup);

        $manager->flush();

        $this->addReference('role-admin', $adminGroup);
        $this->addReference('role-user', $userGroup);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 100;
    }
}