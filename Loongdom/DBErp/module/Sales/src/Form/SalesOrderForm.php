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

use Admin\Data\Config;
use Sales\Validator\SalesOrderCodeValidator;
use Zend\Form\Form;

class SalesOrderForm extends Form
{
    private $entityManager;
    private $salesOrder;

    public function __construct($entityManager = null, $salesOrder = null, $name = 'sales-order-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->entityManager= $entityManager;
        $this->salesOrder   = $salesOrder;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'select',
            'name'  => 'customerId',
            'attributes'    => [
                'id'            => 'customerId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'receivablesCode',
            'attributes'    => [
                'id'            => 'receivablesCode',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'salesOrderSn',
            'attributes'    => [
                'id'            => 'salesOrderSn',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'textarea',
            'name'  => 'customerAddress',
            'attributes'    => [
                'id'            => 'customerAddress',
                'class'         => 'form-control',
                'cols'          => 5
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customerContacts',
            'attributes'    => [
                'id'            => 'customerContacts',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customerPhone',
            'attributes'    => [
                'id'            => 'customerPhone',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customerTelephone',
            'attributes'    => [
                'id'            => 'customerTelephone',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'textarea',
            'name'  => 'salesOrderInfo',
            'attributes'    => [
                'id'            => 'salesOrderInfo',
                'class'         => 'form-control',
                'cols'          => 5
            ]
        ]);

        $this->add([
            'type'  => 'csrf',
            'name'  => 'dberp_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout'  => Config::POST_TOKEN_TIMEOUT
                ]
            ]
        ]);
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'customerId',
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
            'name'      => 'receivablesCode',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 1,
                        'max'   => 20
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'salesOrderSn',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 1,
                        'max'   => 50
                    ]
                ],
                [
                    'name'      => SalesOrderCodeValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager,
                        'salesOrder'    => $this->salesOrder
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customerAddress',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 1,
                        'max'   => 255
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customerContacts',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 1,
                        'max'   => 30
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customerPhone',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 1,
                        'max'   => 20
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customerTelephone',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 1,
                        'max'   => 20
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'salesOrderInfo',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 1,
                        'max'   => 500
                    ]
                ]
            ]
        ]);
    }
}