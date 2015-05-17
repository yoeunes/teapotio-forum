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

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('displayGroup', 'entity', array(
              'class'    => 'TeapotioUserBundle:UserGroup',
              'property' => 'name',
              'query_builder' => function(EntityRepository $er) {
                  return $er->createQueryBuilder('g')
                      ->orderBy('g.name', 'ASC');
              },
              'expanded' => true,
            ))
            ->add('groups', 'entity', array(
              'class'    => 'TeapotioUserBundle:UserGroup',
              'property' => 'name',
              'query_builder' => function(EntityRepository $er) {
                  return $er->createQueryBuilder('g')
                      ->orderBy('g.name', 'ASC');
              },
              'multiple' => true,
              'expanded' => true,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Teapotio\Base\UserBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'teapotio_usergroup';
    }
}
