<?php

namespace Teapotio\Components;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new \Gregwar\ImageBundle\GregwarImageBundle(),
            new \Exercise\HTMLPurifierBundle\ExerciseHTMLPurifierBundle(),

            new \Teapotio\Base\UserBundle\TeapotioBaseUserBundle(),
            new \Teapotio\Base\ForumBundle\TeapotioBaseForumBundle(),
            new \Teapotio\Base\CacheBundle\TeapotioBaseCacheBundle(),

            new \Teapotio\ForumBundle\TeapotioForumBundle(),
            new \Teapotio\SiteBundle\TeapotioSiteBundle(),
            new \Teapotio\UserBundle\TeapotioUserBundle(),
            new \Teapotio\ImageBundle\TeapotioImageBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
