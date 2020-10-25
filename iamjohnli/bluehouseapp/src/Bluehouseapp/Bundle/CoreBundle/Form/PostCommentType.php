<?php

namespace Bluehouseapp\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostCommentType extends AbstractResourceType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content','textarea',array(
                'label'=>'评论内容',
                'required'=>true,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'rows'=>6
                )
            ))
            ->add('创建评论','submit',array(
                'attr'=>array(
                    'style'=>'margin-top:20px'
                )
            ))
        ;
    }
    


    /**
     * @return string
     */
    public function getName()
    {
        return 'bluehouseapp_postComment';
    }
}
