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

namespace Stock\Form;

use Admin\Data\Config;
use Doctrine\ORM\EntityManager;
use Stock\Validator\StockCheckCodeValidator;
use Zend\Form\Form;
use Zend\Session\Container;

class StockCheckForm extends Form
{
    private $entityManager;
    private $sessionAdmin;
    private $stockCheck;
    public function __construct(EntityManager $entityManager = null, $stockCheck = null, $name = 'stock-check-form', $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager    = $entityManager;
        $this->sessionAdmin     = new Container('admin');
        $this->stockCheck       = $stockCheck;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'stockCheckSn',
            'attributes'    => [
                'id'            => 'stockCheckSn',
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
            'type'  => 'text',
            'name'  => 'stockCheckUser',
            'attributes'    => [
                'id'            => 'stockCheckUser',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'stockCheckTime',
            'attributes'    => [
                'id'            => 'stockCheckTime',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'textarea',
            'name'  => 'stockCheckInfo',
            'attributes'    => [
                'id'            => 'stockCheckInfo',
                'class'         => 'form-control',
                'rows'          => 4
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
            'name'      => 'stockCheckSn',
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
                    'name'      => StockCheckCodeValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager,
                        'stockCheck'    => $this->stockCheck,
                        'managerId'     => $this->sessionAdmin->offsetGet('manager_id')
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
            'name'      => 'stockCheckUser',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'stockCheckTime',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'stockCheckInfo',
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