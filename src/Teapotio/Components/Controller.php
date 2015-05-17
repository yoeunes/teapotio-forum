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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

class Controller extends BaseController {


    /**
     * Renders json response.
     *
     * @param  array  $data   an array of data to send
     *
     * @return Response  A Response instance
     */
    protected function renderJson(array $data = array())
    {
        $response = new JsonResponse();
        $response->setData($data);

        return $response;
    }

    /**
     * Given some HTML send a response.
     *
     * @param string   $html   The view name
     *
     * @return Response A Response instance
     */
    protected function renderHtml($html)
    {
        $response = new Response();
        $response->setContent($html);

        return $response;
    }


    /**
     * Generate a title and translate etc etc
     *
     * @param  string   $key
     * @param  array    $params
     *
     * @return string
     */
    protected function generateTitle($key, $params = array())
    {
        return $this->get('teapotio.site')->generateTitle($key, $params);
    }

}
