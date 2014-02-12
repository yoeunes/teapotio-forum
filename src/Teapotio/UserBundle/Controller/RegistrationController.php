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

use Teapotio\Base\UserBundle\Form\UserSignupType;
use Teapotio\UserBundle\Entity\User;
use Teapotio\UserBundle\Entity\UserGroup;

use Teapotio\Base\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController
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

                $this->redirect("/");
            }
        }

        return $this->render('TeapotioBaseUserBundle:Registration:signup.html.twig', array(
            'form' => $form->createView()
        ));
    }
}