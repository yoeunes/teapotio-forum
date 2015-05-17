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

use Teapotio\ImageBundle\Form\Type\ImageNoNameType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('backgroundColor', 'text', array(
                'label'    => 'Background.color',
                'required' => false
            ))
            ->add('backgroundImage', new ImageNoNameType(), array(
                'label'    => ' ',
                'required' => false
            ))
            ->add('backgroundTiled', 'choice', array(
                'label'     => 'Background.tiled',
                'required'  => false,
                'choices'   => array(1 => 'Yes', 0 => 'No'),
                'expanded'  => true,
                'multiple'  => false
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Teapotio\UserBundle\Entity\UserSettings'
        ));
    }

    public function getName()
    {
        return 'teapotio_userbundle_usersettingstype';
    }
}
