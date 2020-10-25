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

namespace Store\Form;

use Admin\Data\Config;
use Store\Validator\WarehouseCodeValidator;
use Zend\Form\Form;

class WarehouseForm extends Form
{
    private $entityManager;
    private $warehouse;

    public function __construct($entityManager = null, $warehouse = null, $name = 'warehouse-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager    = $entityManager;
        $this->warehouse        = $warehouse;

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'warehouseSn',
            'attributes'    => [
                'id'            => 'warehouseSn',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'warehouseName',
            'attributes'    => [
                'id'            => 'warehouseName',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'warehouseContacts',
            'attributes'    => [
                'id'            => 'warehouseContacts',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'warehousePhone',
            'attributes'    => [
                'id'            => 'warehousePhone',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'warehouseSort',
            'attributes'    => [
                'id'            => 'warehouseSort',
                'class'         => 'form-control',
                'value'         => 255
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

    protected function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'warehouseSn',
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
                ],
                [
                    'name'      => WarehouseCodeValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager,
                        'warehouse'      => $this->warehouse
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'warehouseName',
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
            'name'      => 'warehouseContacts',
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
                        'max'   => 50
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'warehousePhone',
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
            'name'      => 'warehouseSort',
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

    }
}