<?php

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
