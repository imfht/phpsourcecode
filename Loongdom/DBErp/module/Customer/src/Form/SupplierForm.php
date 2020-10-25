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
use Customer\Validator\SupplierCodeValidator;
use Zend\Form\Form;
use Zend\Validator\Hostname;

class SupplierForm extends Form
{
    private $entityManager;
    private $supplier;

    public function __construct($entityManager = null, $supplier = null, $name = 'supplier-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager= $entityManager;
        $this->supplier     = $supplier;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'select',
            'name'  => 'supplierCategoryId',
            'attributes'    => [
                'id'            => 'supplierCategoryId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplierCode',
            'attributes'    => [
                'id'            => 'supplierCode',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplierName',
            'attributes'    => [
                'id'            => 'supplierName',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplierSort',
            'attributes'    => [
                'id'            => 'supplierSort',
                'class'         => 'form-control',
                'value'         => 255
            ]
        ]);

        $this->add([
            'type'  => 'textarea',
            'name'  => 'supplierAddress',
            'attributes'    => [
                'id'            => 'supplierAddress',
                'class'         => 'form-control',
                'cols'          => 3
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
            'name'  => 'supplierContacts',
            'attributes'    => [
                'id'            => 'supplierContacts',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplierPhone',
            'attributes'    => [
                'id'            => 'supplierPhone',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplierTelephone',
            'attributes'    => [
                'id'            => 'supplierTelephone',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplierBank',
            'attributes'    => [
                'id'            => 'supplierBank',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplierBankAccount',
            'attributes'    => [
                'id'            => 'supplierBankAccount',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplierTax',
            'attributes'    => [
                'id'            => 'supplierTax',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'email',
            'name'  => 'supplierEmail',
            'attributes'    => [
                'id'            => 'supplierEmail',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'textarea',
            'name'  => 'supplierInfo',
            'attributes'    => [
                'id'            => 'supplierInfo',
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
            'name'      => 'supplierCategoryId',
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
            'name'      => 'supplierCode',
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
                    'name'      => SupplierCodeValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager,
                        'supplier'      => $this->supplier
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'supplierName',
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
            'name'      => 'supplierSort',
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
            'name'      => 'supplierAddress',
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
            'name'      => 'supplierContacts',
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
            'name'      => 'supplierPhone',
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
            'name'      => 'supplierTelephone',
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
            'name'      => 'supplierBank',
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
            'name'      => 'supplierBankAccount',
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
            'name'      => 'supplierTax',
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
            'name'      => 'supplierEmail',
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
            'name'      => 'supplierInfo',
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