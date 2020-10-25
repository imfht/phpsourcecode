<?php

namespace Bluehouseapp\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
class NodeType extends AbstractResourceType
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
                    'placeholder'=>'节点名称'

                )
            ))
            ->add('code','text',array(
                'label'=>'代码(必填:英文字母或数字)',
                'required'=>true,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'placeholder'=>'节点代码'

                )
            ))
            ->add('category','bluehouseapp_category_entity_choice',array(
                'empty_value'=>'请选择分类',
                'label'=>'分类(必填)',
                'required'=>true,
                'attr'=>array(
                    'class'=>'input-block-level'
                )
            ))
        /*
            ->add('category', 'entity', array(
                'label'=>'分类(必填)',
                'attr'=>array(
                    'class'=>'input-block-level'

                ),
                'class'=>'BluehouseappCoreBundle:Category',
                'property'=>'name','required'  => true,
                'query_builder' => function(EntityRepository $er) {

                        return $er->createQueryBuilder('c')->orderBy('c.no', 'ASC')
                            ->where('c.enabled = :enabled')
                            ->andWhere('c.status = :status')
                            ->setParameters(array('enabled' => true,'status'=>true))
                            ;

                    },

            ))
*/
        ->add('image','file',array(
            'label'=>'节点头像',
            'required'=>false,//!$options['isEdit'],//!$this->isEdit,
            'attr'=>array(

            )
        ))
            ->add('no',null,array(
                'label'=>'显示序号(必填)',
                'required'=>true,
                'attr'=>array(
                    'class'=>'input-block-level'

                )
            ))
            ->add('description','textarea',array(
                'label'=>'描述',
                'required'=>false,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'rows'=>3,
                    'placeholder'=>'节点简短介绍'
                )
            ))
//            ->add('保存','submit',array(
//                'attr'=>array(
//                    'style'=>'margin-top:20px;'
//                )
//            ))
        ;
    }
    


    /**
     * @return string
     */
    public function getName()
    {
        return 'bluehouseapp_node';
    }
}
