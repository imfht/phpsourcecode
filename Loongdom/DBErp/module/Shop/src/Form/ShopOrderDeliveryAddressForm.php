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

class ShopOrderDeliveryAddressForm extends Form
{
    public function __construct($name = 'shop-order-delivery-address-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add(['type' => 'text', 'name' => 'delivery_name']);
        $this->add(['type' => 'text', 'name' => 'region_info']);
        $this->add(['type' => 'text', 'name' => 'region_address']);
        $this->add(['type' => 'text', 'name' => 'zip_code']);
        $this->add(['type' => 'text', 'name' => 'delivery_phone']);
        $this->add(['type' => 'text', 'name' => 'delivery_telephone']);
        $this->add(['type' => 'text', 'name' => 'delivery_info']);
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'delivery_name',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'region_info',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'region_address',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'zip_code',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            /*'validators'=> [
                [
                    'name'      => 'PostCode'
                ]
            ]*/
        ]);

        $inputFilter->add([
            'name'      => 'delivery_phone',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            /*'validators'=> [
                [
                    'name'      => 'PhoneNumber',
                    'options'   => [
                        'country'=> 'CN'
                    ]
                ]
            ]*/
        ]);

        $inputFilter->add([
            'name'      => 'delivery_telephone',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            /*'validators'=> [
                [
                    'name'      => 'PhoneNumber',
                    'options'   => [
                        'country'=> 'CN'
                    ]
                ]
            ]*/
        ]);

        $inputFilter->add([
            'name'      => 'delivery_info',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);
    }
}