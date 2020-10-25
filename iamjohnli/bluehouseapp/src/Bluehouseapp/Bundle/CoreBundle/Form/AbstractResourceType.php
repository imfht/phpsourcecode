<?php


namespace Bluehouseapp\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**

 */
abstract class AbstractResourceType extends AbstractType
{
    /**
     * @var string
     */
    protected $dataClass = null;

    /**
     * @var string[]
     */
    protected $validationGroups = array();

    /**
     * @param string   $dataClass        FQCN
     * @param string[] $validationGroups
     */
    public function __construct($dataClass, array $validationGroups = array())
    {
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'validation_groups' => $this->validationGroups,
        ));
    }
}
