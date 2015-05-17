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
    protected $translatorService;
    protected $forumTitle;

    public function __construct ($translatorService, $forumTitle)
    {
        $this->translatorService = $translatorService;
        $this->forumTitle = $forumTitle;
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
        return $this->translatorService->trans($key, $params) .' - '. $this->forumTitle;
    }


}
