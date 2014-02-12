<?php

namespace Teapotio\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TeapotioUserBundle extends Bundle
{
    public function getParent()
    {
        return 'TeapotioBaseUserBundle';
    }
}
