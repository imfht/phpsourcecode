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

namespace Purchase\Form;

use Admin\Data\Config;
use Store\Validator\WarehouseCodeValidator;
use Zend\Form\Form;

class WarehouseOrderForm extends Form
{
    private $entityManager;
    private $warehouseOrder;

    public function __construct($entityManager = null, $warehouseOrder = null, $name = 'warehouse-order', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager = $entityManager;
        $this->warehouseOrder= $warehouseOrder;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'warehouseOrderSn',
            'attributes'    => [
                'id'            => 'warehouseOrderSn',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'warehouseId',
            'attributes'    => [
                'id'            => 'warehouseId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'warehouseOrderState',
            'attributes'    => [
                'id'            => 'warehouseOrderState',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'textarea',
            'name'  => 'warehouseOrderInfo',
            'attributes'    => [
                'id'            => 'warehouseOrderInfo',
                'class'         => 'form-control',
                'cols'          => 3
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
            'name'      => 'warehouseOrderSn',
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
                    'name'      => WarehouseCodeValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager,
                        'warehouseOrder'=> $this->warehouseOrder
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'warehouseId',
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
            'name'      => 'warehouseOrderState',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'warehouseOrderInfo',
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