<?php

namespace Bluehouseapp\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractResourceType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title','text',array(
                'label'=>'标题(必填)',
                'required'=>true,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'placeholder'=>'标题'
                )
            ))
            ->add('content','textarea',array(
                'label'=>'主题内容(必填)',
                'required'=>true,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'rows'=>10,
                    'placeholder'=>'主题内容.'
                )
            ))

            ->add('创建','submit',array(
                'attr'=>array(
                    'style'=>'margin-top:20px;'
                )
            ))
        ;
    }
    

    /**
     * @return string
     */
    public function getName()
    {
        return 'bluehouseapp_post';
    }
}
