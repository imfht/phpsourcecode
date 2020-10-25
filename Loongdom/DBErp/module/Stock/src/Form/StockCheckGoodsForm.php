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

use Doctrine\ORM\EntityManager;
use Stock\Validator\StockCheckGoodsArrayValidator;
use Zend\Form\Form;
use Zend\Session\Container;

class StockCheckGoodsForm extends Form
{
    private $entityManager;
    private $sessionAdmin;

    public function __construct(EntityManager $entityManager = null, $name = 'stock-check-goods-form', $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager    = $entityManager;
        $this->sessionAdmin     = new Container('admin');

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'goodsId',
            'attributes'    => [
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'stockCheckAftGoodsNum',
            'attributes'    => [
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'stockCheckGoodsAmount',
            'attributes'    => [
                'class'         => 'form-control'
            ]
        ]);
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'goodsId',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ],
            'validators'=> [
                [
                    'name' => StockCheckGoodsArrayValidator::class,
                    'options' => [
                        'entityManager' => $this->entityManager,
                        'goodsField'    => 'goodsId',
                        'managerId'     => $this->sessionAdmin->offsetGet('manager_id')
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'stockCheckAftGoodsNum',
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
            'name'      => 'stockCheckGoodsAmount',
            'required'  => true
        ]);
    }
}