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

namespace Teapotio\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Teapotio\Base\ForumBundle\Entity\Stat as BaseStat;

use Teapotio\Base\ForumBundle\Entity\StatInterface;

/**
 * Teapotio\ForumBundle\Entity\Stat
 *
 * @ORM\Table(name="`forum_stat`")
 * @ORM\Entity
 */
class Stat extends BaseStat implements StatInterface
{

}