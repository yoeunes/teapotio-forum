<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    BaseUserBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\UserBundle\Form\Type;

use Teapotio\Base\UserBundle\Form\Type\UserSignupType as BaseUserSignupType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserSignupType extends BaseUserSignupType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email', 'email')
            ->add('password', 'repeated', array(
                'type'            => 'password',
                'first_options'   => array('label' => 'password'),
                'second_options'  => array('label' => 'confirm.password'),
                'invalid_message' => 'password.not.match'
            ))
            ->add('captcha', 'captcha');
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Teapotio\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'teapotio_usersignup';
    }
}
