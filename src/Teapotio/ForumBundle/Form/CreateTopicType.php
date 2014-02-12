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

namespace Teapotio\ForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Teapotio\Base\ForumBundle\Form\CreateTopicType as BaseCreateTopicType;

class CreateTopicType extends BaseCreateTopicType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $wysiwygClass = 'wysiwyg';

        $builder
            ->add('title')
            ->add('body', 'wysiwyg_textarea', array(
                'label'  => false,
                'mapped' => false,
                'attr'   => array('class' => $wysiwygClass)
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Teapotio\Base\ForumBundle\Entity\Topic'
        ));
    }

    public function getName()
    {
        return 'teapotio_createtopic';
    }
}
