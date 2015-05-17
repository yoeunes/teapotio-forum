<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    UserBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\UserBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;

use Teapotio\Components\Controller;

class SecurityController extends Controller
{
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $title = $this->generateTitle("Login");

        $params = array(
            'page_title'    => $title,
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'login_error'   => $error,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioUserBundle:partial:security/login.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioUserBundle:page:security/login.html.twig', $params);
    }
}
