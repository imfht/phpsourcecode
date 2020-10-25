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

use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class OtherOrderSearchForm extends Form
{
    private $translator;

    public function __construct($name = 'search-other-order-form', array $options = [])
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
            'name'  => 'start_amount',
            'attributes'    => [
                'id'            => 'start_amount',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('起始金额')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'end_amount',
            'attributes'    => [
                'id'            => 'end_amount',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('结束金额')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'start_time',
            'attributes'    => [
                'id'            => 'start_time',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('起始时间')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'end_time',
            'attributes'    => [
                'id'            => 'end_time',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('结束时间')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'warehouse_order_sn',
            'attributes'    => [
                'id'            => 'warehouse_order_sn',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('入库单号')
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'warehouse_id',
            'attributes'    => [
                'id'            => 'warehouse_id',
                'class'         => 'form-control input-sm'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'warehouse_order_info',
            'attributes'    => [
                'id'            => 'warehouse_order_info',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('备注信息')
            ]
        ]);
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'start_amount',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToFloat']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'end_amount',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToFloat']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'start_time',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'end_time',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'warehouse_order_sn',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
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
            'name'      => 'warehouse_order_info',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);
    }
}