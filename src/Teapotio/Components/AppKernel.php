<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    Components
 * @author     Thomas Potaire
 */

namespace Teapotio\Components;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new \Gregwar\ImageBundle\GregwarImageBundle(),
            new \Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new \Exercise\HTMLPurifierBundle\ExerciseHTMLPurifierBundle(),

            new \Teapotio\Base\UserBundle\TeapotioBaseUserBundle(),
            new \Teapotio\Base\ForumBundle\TeapotioBaseForumBundle(),
            new \Teapotio\Base\CacheBundle\TeapotioBaseCacheBundle(),
            new \Teapotio\Base\ThemeBundle\TeapotioBaseThemeBundle(),

            new \Teapotio\ForumBundle\TeapotioForumBundle(),
            new \Teapotio\SiteBundle\TeapotioSiteBundle(),
            new \Teapotio\UserBundle\TeapotioUserBundle(),
            new \Teapotio\ImageBundle\TeapotioImageBundle(),
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
