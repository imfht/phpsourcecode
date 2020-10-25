<?php

namespace Bluehouseapp\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractResourceType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text',array(
                'label'=>'名称(必填)',
                'required'=>true,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'placeholder'=>'分类名称'

                )
            ))

            ->add('no','text',array(
                'label'=>'序号(必填)',
                'required'=>true,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'placeholder'=>'显示序号'

                )
            ))

        ;
    }
    


    /**
     * @return string
     */
    public function getName()
    {
        return 'bluehouseapp_category';
    }
}
