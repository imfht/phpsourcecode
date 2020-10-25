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

use Zend\Form\Form;

class ShopOrderGoodsForm extends Form
{
    public function __construct($name = 'shop-order-goods-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add(['type' => 'text', 'name' => 'goods_name']);
        $this->add(['type' => 'text', 'name' => 'goods_spec']);
        $this->add(['type' => 'text', 'name' => 'goods_sn']);
        $this->add(['type' => 'text', 'name' => 'goods_barcode']);
        $this->add(['type' => 'text', 'name' => 'unit_name']);
        $this->add(['type' => 'text', 'name' => 'goods_price']);
        $this->add(['type' => 'text', 'name' => 'goods_type']);
        $this->add(['type' => 'text', 'name' => 'buy_num']);
        $this->add(['type' => 'text', 'name' => 'goods_amount']);
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'goods_name',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goods_spec',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goods_sn',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goods_barcode',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'unit_name',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goods_price',
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
            'name'      => 'goods_type',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ],
            'validators'=> [
                [
                    'name'      => 'InArray',
                    'options'   => [
                        'haystack'  => [1, 2]
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'buy_num',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ],
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => 0
                    ]
                ]
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
    }
}