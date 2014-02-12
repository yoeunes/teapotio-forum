<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    ForumBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\ForumBundle\Service;

use Teapotio\ForumBundle\Entity\Flag;
use Teapotio\Base\ForumBundle\Service\FlagService as BaseFlagService;

class FlagService extends BaseFlagService
{
    public function createFlag()
    {
        return new Flag();
    }

}