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

namespace Shop\Form;

use Shop\Validator\ShopOrderSnValidator;
use Zend\Form\Form;

class ShopOrderForm extends Form
{
    private $entityManager;
    private $appAccessId;

    public function __construct($entityManager = null, $appAccessId = null, $name = 'shop-order-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');

        $this->entityManager= $entityManager;
        $this->appAccessId  = $appAccessId;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add(['type' => 'text', 'name' => 'order_sn']);
        $this->add(['type' => 'text', 'name' => 'buy_name']);
        $this->add(['type' => 'text', 'name' => 'payment_code']);
        $this->add(['type' => 'text', 'name' => 'payment_name']);
        $this->add(['type' => 'text', 'name' => 'payment_cost']);
        $this->add(['type' => 'text', 'name' => 'payment_certification']);
        $this->add(['type' => 'text', 'name' => 'express_code']);
        $this->add(['type' => 'text', 'name' => 'express_name']);
        $this->add(['type' => 'text', 'name' => 'express_cost']);
        $this->add(['type' => 'text', 'name' => 'other_cost']);
        $this->add(['type' => 'text', 'name' => 'other_info']);
        $this->add(['type' => 'text', 'name' => 'discount_amount']);
        $this->add(['type' => 'text', 'name' => 'discount_info']);
        $this->add(['type' => 'text', 'name' => 'goods_amount']);
        $this->add(['type' => 'text', 'name' => 'order_amount']);
        $this->add(['type' => 'text', 'name' => 'order_message']);
        $this->add(['type' => 'text', 'name' => 'add_time']);
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'order_sn',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            'validators'=> [
                [
                    'name'      => ShopOrderSnValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager,
                        'appAccessId'   => $this->appAccessId
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'buy_name',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'payment_code',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'payment_name',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'payment_cost',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'ToFloat']
            ],
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => 0,
                        'inclusive' => true
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'payment_certification',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'express_code',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'express_name',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'express_cost',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'ToFloat']
            ],
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => 0,
                        'inclusive' => true
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'other_cost',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'ToFloat']
            ],
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => 0,
                        'inclusive' => true
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'other_info',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'discount_amount',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'ToFloat']
            ],
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => 0,
                        'inclusive' => true
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'discount_info',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goods_amount',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'ToFloat']
            ],
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => 0,
                        'inclusive' => true
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'order_amount',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'ToFloat']
            ],
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => 0,
                        'inclusive' => true
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'order_message',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'add_time',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);
    }
}