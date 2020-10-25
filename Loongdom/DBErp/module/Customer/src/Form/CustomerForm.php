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

namespace Customer\Form;

use Admin\Data\Config;
use Customer\Validator\CustomerCodeValidator;
use Zend\Form\Form;
use Zend\Validator\Hostname;

class CustomerForm extends Form
{
    private $entityManager;
    private $customer;

    public function __construct($entityManager = null, $customer = null, $name = 'customer-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager= $entityManager;
        $this->customer     = $customer;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'select',
            'name'  => 'customerCategoryId',
            'attributes'    => [
                'id'            => 'customerCategoryId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customerCode',
            'attributes'    => [
                'id'            => 'customerCode',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customerName',
            'attributes'    => [
                'id'            => 'customerName',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customerSort',
            'attributes'    => [
                'id'            => 'customerSort',
                'class'         => 'form-control',
                'value'         => 255
            ]
        ]);

        $this->add([
            'type'  => 'hidden',
            'name'  => 'regionId',
            'attributes'    => [
                'id'            => 'regionId',
                'class'         => 'region_ids'
            ]
        ]);

        $this->add([
            'type'  => 'hidden',
            'name'  => 'regionValues',
            'attributes'    => [
                'id'            => 'regionValues',
                'class'         => 'region_names'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customerAddress',
            'attributes'    => [
                'id'            => 'customerAddress',
                'class'         => 'form-control'
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
            'type'  => 'text',
            'name'  => 'customerBank',
            'attributes'    => [
                'id'            => 'customerBank',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customerBankAccount',
            'attributes'    => [
                'id'            => 'customerBankAccount',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customerTax',
            'attributes'    => [
                'id'            => 'customerTax',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'email',
            'name'  => 'customerEmail',
            'attributes'    => [
                'id'            => 'customerEmail',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'textarea',
            'name'  => 'customerInfo',
            'attributes'    => [
                'id'            => 'customerInfo',
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
            'name'      => 'customerCategoryId',
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
            'name'      => 'customerCode',
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
                ],
                [
                    'name'      => CustomerCodeValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager,
                        'customer'      => $this->customer
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customerName',
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
                        'max'   => 100
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customerSort',
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
            'name'      => 'regionId',
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
            'name'      => 'regionValues',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
        ]);

        $inputFilter->add([
            'name'      => 'customer_address',
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
                        'max'   => 255
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customerContacts',
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
            'name'      => 'customerBank',
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
                        'max'   => 100
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customerBankAccount',
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
                        'max'   => 30
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customerTax',
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
                        'max'   => 30
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customerEmail',
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
                        'max'   => 30
                    ]
                ],
                [
                    'name'      => 'EmailAddress',
                    'options'   => [
                        'allow'         => Hostname::ALLOW_DNS,
                        'useMxCheck'    => false
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customerInfo',
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
                        'max'   => 255
                    ]
                ]
            ]
        ]);
    }
}