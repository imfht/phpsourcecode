<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 14-11-26
 * Time: 下午10:33
 */

namespace Bluehouseapp\Bundle\CoreBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\Options;



class CategoryEntityType extends AbstractType{

    protected $className;

    /**
     * Constructor.
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $queryBuilder = function (Options $options) {
            if (!$options['disabled']) {
                return function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->orderBy('c.no', 'ASC')
                        ->where('c.enabled = :enabled')
                        ->andWhere('c.status = :status')
                        ->setParameters(array('enabled' => true,'status'=>true))
                        ;
                };
            } else {
                return function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('c');
                };
            }
        };

        $resolver
            ->setDefaults(array(
                'class'    => $this->className,
                'disabled'=>false,
                'query_builder' => $queryBuilder
            ))
        ;
    }


    public function getName()
    {
        return 'bluehouseapp_category_entity_choice';
    }
    public function getParent()
    {
        return 'entity';
    }
} 