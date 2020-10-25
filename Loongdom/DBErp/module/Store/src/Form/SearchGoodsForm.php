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

use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class SearchGoodsForm extends Form
{
    private $translator;

    public function __construct($name = 'search-goods-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-horizontal');

        $this->translator = new Translator();

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'start_id',
            'attributes'    => [
                'id'            => 'start_id',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('起始ID')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'end_id',
            'attributes'    => [
                'id'            => 'end_id',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('结束ID')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'start_price',
            'attributes'    => [
                'id'            => 'start_price',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('起始价格')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'end_price',
            'attributes'    => [
                'id'            => 'end_price',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('结束价格')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'start_sales_price',
            'attributes'    => [
                'id'            => 'start_sales_price',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('起始售价')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'end_sales_price',
            'attributes'    => [
                'id'            => 'end_sales_price',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('结束售价')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'start_stock',
            'attributes'    => [
                'id'            => 'start_stock',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('起始库存')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'end_stock',
            'attributes'    => [
                'id'            => 'end_stock',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('结束库存')
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
            'name'  => 'goods_number',
            'attributes'    => [
                'id'            => 'goods_number',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('商品编号')
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
            'type'  => 'select',
            'name'  => 'goods_category_id',
            'attributes'    => [
                'id'            => 'goods_category_id',
                'class'         => 'form-control input-sm'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'brand_id',
            'attributes'    => [
                'id'            => 'brand_id',
                'class'         => 'form-control input-sm'
            ]
        ]);
    }

    protected function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'start_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'end_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'start_price',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'end_price',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'start_stock',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'end_stock',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
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
            'name'      => 'goods_number',
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
            'name'      => 'goods_category_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'brand_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);
    }
}