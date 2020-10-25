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
use Doctrine\ORM\EntityManager;
use Sales\Validator\SendOrderSnValidator;
use Sales\Validator\SendOrderWarehouseValidator;
use Zend\Form\Form;

class SendOrderForm extends Form
{
    private $entityManager;
    private $sendOrderGoods;

    public function __construct(EntityManager $entityManager, $sendOrderGoods, $name = 'send-order-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->entityManager    = $entityManager;
        $this->sendOrderGoods   = $sendOrderGoods;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'sendOrderSn',
            'attributes'    => [
                'id'            => 'sendOrderSn',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'sendWarehouse'
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
            'name'      => 'sendOrderSn',
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
                    'name'      => SendOrderSnValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'sendWarehouse',
            'required'  => true,
            'validators'=> [
                [
                    'name' => SendOrderWarehouseValidator::class,
                    'options' => [
                        'entityManager' => $this->entityManager,
                        'sendOrderGoods'=> $this->sendOrderGoods
                    ]
                ]
            ]
        ]);

    }
}