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

namespace Teapotio\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email')
            ->add('old_password', 'password', array(
                'label' => 'old.password'
            ))
            ->add('password', 'repeated', array(
                'type'            => 'password',
                'first_options'   => array('label' => 'password'),
                'second_options'  => array('label' => 'confirm.password'),
                'invalid_message' => 'password.not.match'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Teapotio\Base\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'teapotio_userprofile';
    }
}
