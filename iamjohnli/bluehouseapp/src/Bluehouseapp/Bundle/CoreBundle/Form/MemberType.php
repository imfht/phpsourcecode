<?php


namespace Bluehouseapp\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class MemberType  extends AbstractResourceType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nickname',"text",array(
                'label'=>'昵称(必填)',
                'required'=>true,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'placeholder'=>'昵称'
                )
            ))
            ->add('city','text',array(
                'label'=>'我的位置',
                'required'=>false,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'placeholder'=>'比如：北京东直门'
                )
            ));


        $builder
            ->add('website','text',array(
                'label'=>'个人站点',
                'required'=>false,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'placeholder'=>'个人网站或者个人博客'
                )
            ))

            ->add('weibo','text',array(
                'label'=>'微博地址',
                'required'=>false,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'placeholder'=>'http://weibo.com/yourWeiboUsername'
                )
            ))
            ->add('oschina','text',array(
                'label'=>'OSC地址',
                'required'=>false,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'placeholder'=>'http://my.oschina.net/yourOSCUsername'
                )
            ))
            ->add('description','textarea',array(
                'label'=>'个人介绍',
                'required'=>false,
                'attr'=>array(
                    'class'=>'input-block-level',
                    'rows'=>6,
                    'placeholder'=>'请用简洁的话介绍一下自己，不要超过400个字'
                )
            ))

            ->add('保存','submit',array(
                'attr'=>array(
                    'style'=>'display:block;margin-top:20px'
                )
            ))
        ;

    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'bluehouseapp_member';
    }
}
