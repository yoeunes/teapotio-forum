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

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new \Teapotio\Base\UserBundle\TeapotioBaseUserBundle(),
            new \Teapotio\Base\ForumBundle\TeapotioBaseForumBundle(),
            new \Teapotio\Base\CacheBundle\TeapotioBaseCacheBundle(),

            new \Teapotio\ForumBundle\TeapotioForumBundle(),
            new \Teapotio\SiteBundle\TeapotioSiteBundle(),
            new \Teapotio\UserBundle\TeapotioUserBundle(),
            new \Teapotio\ImageBundle\TeapotioImageBundle(),
        );

        return $bundles;
    }
}
