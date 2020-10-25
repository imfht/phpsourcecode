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

use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class SearchWarehouseOrderForm extends Form
{
    private $translator;

    public function __construct($name = 'search-warehouse-order-form', array $options = [])
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
            'name'  => 'warehouse_order_sn',
            'attributes'    => [
                'id'            => 'warehouse_order_sn',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('入库单号')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplier_contacts',
            'attributes'    => [
                'id'            => 'supplier_contacts',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('供应商联系人')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplier_phone',
            'attributes'    => [
                'id'            => 'supplier_phone',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('供应商电话')
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'supplier_id',
            'attributes'    => [
                'id'            => 'supplier_id',
                'class'         => 'form-control input-sm'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'payment_code',
            'attributes'    => [
                'id'            => 'payment_code',
                'class'         => 'form-control input-sm'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'p_order_state',
            'attributes'    => [
                'id'            => 'p_order_state',
                'class'         => 'form-control input-sm'
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
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'end_amount',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
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
            'name'      => 'supplier_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'supplier_contacts',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'supplier_phone',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'payment_code',
            'required'  => false,
            'filters'   => [
                ['name' => 'alpha']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'p_order_state',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);
    }
}