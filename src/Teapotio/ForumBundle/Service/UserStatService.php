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

use Teapotio\ForumBundle\Entity\UserStat;
use Teapotio\Base\ForumBundle\Service\UserStatService as BaseUserStatService;

class UserStatService extends BaseUserStatService
{
    public function createUserStat()
    {
        return new UserStat();
    }
}
