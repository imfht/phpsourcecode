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
use Doctrine\ORM\EntityManager;
use Purchase\Validator\OrderGoodsIdReturnValidator;
use Purchase\Validator\OrderGoodsReturnAmountValidator;
use Purchase\Validator\OrderGoodsReturnNumValidator;
use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class OrderGoodsReturnForm extends Form
{
    private $entityManager;
    private $pOrderId;
    private $translator;

    public function __construct(EntityManager $entityManager = null, $pOrderId = null, $name = 'goods-return-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager = $entityManager;
        $this->pOrderId      = $pOrderId;
        $this->translator    = new Translator();

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'pGoodsId'
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
            'name'  => 'pOrderReturnInfo',
            'attributes'    => [
                'id'            => 'p_order_return_info',
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
            'name'      => 'pGoodsId',
            'required'  => true,
            'validators'=> [
                [
                    'name'      => OrderGoodsIdReturnValidator::class,
                    'options'   => [
                        'entityManager' => $this->entityManager,
                        'pOrderId'      => $this->pOrderId
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
                    'name'      => OrderGoodsReturnNumValidator::class,
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
                    'name'      => OrderGoodsReturnAmountValidator::class,
                    'options'   => [
                        'entityManager' => $this->entityManager
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'pOrderReturnInfo',
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