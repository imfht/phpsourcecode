<?php


namespace Bluehouseapp\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class MemberImageType  extends AbstractType{

    protected $className,$isEdit;

    /**
     * Constructor.
     *
     * @param string $className
     */
    public function __construct($isEdit)
    {

        $this->isEdit=$isEdit;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('userImage','file',array(
            'label'=>' ',
            'required'=>!$this->isEdit,
            'attr'=>array(

            )
        ));
        $builder ->add('上传头像','submit',array(
        'attr'=>array(
            'style'=>'display:block;margin-top:20px'
        )
    ))
    ;

    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' =>'Bluehouseapp\Bundle\CoreBundle\Entity\Member'      // $this->$className
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bluehouseapp_member_image';
    }
} 