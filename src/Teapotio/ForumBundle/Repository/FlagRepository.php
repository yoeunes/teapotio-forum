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

namespace Teapotio\ForumBundle\Repository;

use Teapotio\ForumBundle\Entity\Moderation;

use Teapotio\Base\ForumBundle\Repository\FlagRepository as EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class FlagRepository extends EntityRepository
{

}