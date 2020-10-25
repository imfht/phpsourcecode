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

namespace Shop\Form;

use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class SearchShopOrderGoodsForm extends Form
{
    private $translator;

    public function __construct($name = 'search-shop-order-goods-form', $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-horizontal');

        $this->translator = new Translator();

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'goods_sn',
            'attributes'    => [
                'id'            => 'goods_sn',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('商品编号')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goods_name',
            'attributes'    => [
                'id'            => 'goods_name',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('商品名称')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goods_spec',
            'attributes'    => [
                'id'            => 'goods_spec',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('商品规格')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'start_buy_num',
            'attributes'    => [
                'id'            => 'start_buy_num',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('始')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'end_buy_num',
            'attributes'    => [
                'id'            => 'end_buy_num',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('终')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'shop_order_sn',
            'attributes'    => [
                'id'            => 'shop_order_sn',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('订单号')
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'order_state',
            'attributes'    => [
                'id'            => 'order_state',
                'class'         => 'form-control input-sm',
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'app_id',
            'attributes'    => [
                'id'            => 'app_id',
                'class'         => 'form-control input-sm',
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'warehouse_id',
            'attributes'    => [
                'id'            => 'warehouse_id',
                'class'         => 'form-control input-sm',
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'distribution_state',
            'attributes'    => [
                'id'            => 'distribution_state',
                'class'         => 'form-control input-sm',
            ]
        ]);
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'goods_sn',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goods_name',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goods_spec',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'start_buy_num',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'end_buy_num',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'shop_order_sn',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'order_state',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'app_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'warehouse_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'distribution_state',
            'required'  => false
        ]);
    }
}