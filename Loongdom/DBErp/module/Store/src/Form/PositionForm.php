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
use Store\Validator\WarehousePositionCodeValidator;
use Zend\Form\Form;

class PositionForm extends Form
{
    private $entityManager;
    private $position;

    public function __construct($entityManager = null, $position = null, $name = 'position-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager= $entityManager;
        $this->position     = $position;

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'positionSn',
            'attributes'    => [
                'id'            => 'positionSn',
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
            'name'      => 'positionSn',
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
                    'name'      => WarehousePositionCodeValidator::class,
                    'options'    => [
                        'entityManager'     => $this->entityManager,
                        'warehousePosition' => $this->position
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

    }
}