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

use Teapotio\ForumBundle\Entity\MessageStar;
use Teapotio\Base\ForumBundle\Service\MessageStarServiceInterface;
use Teapotio\Base\ForumBundle\Service\MessageStarService as BaseMessageStarService;

class MessageStarService extends BaseMessageStarService implements MessageStarServiceInterface
{
    public function createMessageStar()
    {
        return new MessageStar();
    }

}
