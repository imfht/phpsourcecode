<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Sales\Form;

use Doctrine\ORM\EntityManager;
use Sales\Validator\SalesOrderGoodsArrayValidator;
use Zend\Form\Form;

class SalesOrderGoodsForm extends Form
{
    private $entityManager;

    public function __construct(EntityManager $entityManager = null, $name = 'sales-order-goods-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->entityManager = $entityManager;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'goodsId',
            'attributes'    => [
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'salesGoodsPrice',
            'attributes'    => [
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'salesGoodsSellNum',
            'attributes'    => [
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'salesGoodsTax',
            'attributes'    => [
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'salesGoodsAmount',
            'attributes'    => [
                'class'         => 'form-control'
            ]
        ]);
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'goodsId',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ],
            'validators'=> [
                [
                    'name' => SalesOrderGoodsArrayValidator::class,
                    'options' => [
                        'entityManager' => $this->entityManager,
                        'goodsField'    => 'goodsId'
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'salesGoodsPrice',
            'required'  => true,
            'validators'=> [
                [
                    'name' => SalesOrderGoodsArrayValidator::class,
                    'options' => [
                        'goodsField' => 'goodsPrice'
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'salesGoodsTax',
            'required'  => true,
            'validators'=> [
                [
                    'name' => SalesOrderGoodsArrayValidator::class,
                    'options' => [
                        'goodsField' => 'goodsTax'
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'salesGoodsSellNum',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ],
            'validators'=> [
                [
                    'name' => SalesOrderGoodsArrayValidator::class,
                    'options' => [
                        'goodsField' => 'goodsSellNum'
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'salesGoodsAmount',
            'required'  => true,
            'validators'=> [
                [
                    'name' => SalesOrderGoodsArrayValidator::class,
                    'options' => [
                        'goodsField' => 'goodsAmount'
                    ]
                ]
            ]
        ]);
    }
}