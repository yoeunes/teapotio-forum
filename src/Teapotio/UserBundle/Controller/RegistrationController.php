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

use Teapotio\UserBundle\Form\UserSignupType;
use Teapotio\UserBundle\Entity\User;

use Teapotio\Components\Controller;

class RegistrationController extends Controller
{

    public function signupAction()
    {
        $request = $this->getRequest();

        $user = new User();

        $form = $this->createForm(new UserSignupType(), $user);

        if ($request->getMethod() === 'POST') {
            $form->bind($request);
            if ($form->isValid() === true) {
                $em = $this->get('doctrine')->getManager();

                $groups = $em->getRepository('TeapotioUserBundle:UserGroup')
                            ->findBy(array('role' => 'ROLE_USER'));

                $image = $this->get('teapotio.image')->getById(1); // 1: default image

                $this->get('teapotio.user')->setup(
                    $user->getUsername(),
                    $user->getEmail(),
                    $user->getPassword(),
                    $groups,
                    array($image)
                );

                $this->get('teapotio.user')->postSignup($user);

                return $this->redirect("/");
            }
        }

        $title = '';

        $params = array(
            'page_title' => $title,
            'form'       => $form->createView()
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioUserBundle:partial:registration/signup.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioUserBundle:page:registration/signup.html.twig', $params);
    }

    public function forgotAction($token)
    {
        $title = '';

        $params = array(
            'page_title' => $title
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioUserBundle:partial:registration/forgot.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioUserBundle:page:registration/forgot.html.twig', $params);
    }
}
