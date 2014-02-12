<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    SiteBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\SiteBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MainService
{
    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * Generate a title and translate etc etc
     *
     * @param  string   $key
     * @param  array    $params
     *
     * @return string
     */
    public function generateTitle($key, $params = array())
    {
        return $this->container
                    ->get('translator')
                    ->trans($key, $params) .' - '. $this->container->getParameter('forum_title');
    }


}