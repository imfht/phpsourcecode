<?php

namespace Bluehouseapp\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BanedIPsType extends AbstractResourceType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ip','text',array(
                'label'=>'IP',
                'required'=>true,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'placeholder'=>'请输入要屏蔽的IP'
                )
            ))
            ->add('fromDate',null,array(
                'label'=>'开始时间',
                'required'=>true,
                'attr'=>array(

                )))
            ->add('toDate',null,array(
                'label'=>'结束时间',
                'required'=>true,
                'attr'=>array(

                )))

        ;
    }
    

    /**
     * @return string
     */
    public function getName()
    {
        return 'bluehouseapp_banedIPs';
    }
}
