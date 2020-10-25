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
use Sales\Validator\SalesOrderGoodsIdReturnValidator;
use Sales\Validator\SalesOrderGoodsReturnAmountValidator;
use Sales\Validator\SalesOrderGoodsReturnNumValidator;
use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class SalesOrderReturnForm extends Form
{
    private $entityManager;
    private $salesOrderId;
    private $translator;

    public function __construct($entityManager = null, $salesOrderId = null, $name = 'sales-return-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager    = $entityManager;
        $this->translator       = new Translator();
        $this->salesOrderId     = $salesOrderId;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'salesGoodsId'
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goodsReturnNum'
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goodsReturnAmount'
        ]);

        $this->add([
            'type'  => 'textarea',
            'name'  => 'salesOrderReturnInfo',
            'attributes'    => [
                'id'            => 'salesOrderReturnInfo',
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
            'name'      => 'salesGoodsId',
            'required'  => true,
            'validators'=> [
                [
                    'name'      => SalesOrderGoodsIdReturnValidator::class,
                    'options'   => [
                        'entityManager' => $this->entityManager,
                        'salesOrderId'  => $this->salesOrderId
                    ]
                ]
            ],
            'error_message' => $this->translator->translate('请选择退货商品')
        ]);

        $inputFilter->add([
            'name'      => 'goodsReturnNum',
            'required'  => true,
            'validators'=> [
                [
                    'name'      => SalesOrderGoodsReturnNumValidator::class,
                    'options'   => [
                        'entityManager' => $this->entityManager
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goodsReturnAmount',
            'required'  => true,
            'validators'=> [
                [
                    'name'      => SalesOrderGoodsReturnAmountValidator::class,
                    'options'   => [
                        'entityManager' => $this->entityManager
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'salesOrderReturnInfo',
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