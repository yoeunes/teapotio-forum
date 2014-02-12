<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    ImageBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\ImageBundle\Controller;

use Teapotio\Components\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TeapotioImageBundle:Default:index.html.twig', array('name' => $name));
    }
}
