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

use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class SearchSalesOrderForm extends Form
{
    private $translator;

    public function __construct($name = 'search-sales-order-form', array $options = [])
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
            'name'  => 'sales_order_sn',
            'attributes'    => [
                'id'            => 'sales_order_sn',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('销售单号')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customer_contacts',
            'attributes'    => [
                'id'            => 'customer_contacts',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('联系人')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customer_phone',
            'attributes'    => [
                'id'            => 'customer_phone',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('客户电话')
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'customer_id',
            'attributes'    => [
                'id'            => 'customer_id',
                'class'         => 'form-control input-sm'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'receivables_code',
            'attributes'    => [
                'id'            => 'receivables_code',
                'class'         => 'form-control input-sm'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'sales_order_state',
            'attributes'    => [
                'id'            => 'sales_order_state',
                'class'         => 'form-control input-sm'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'return_state',
            'attributes'    => [
                'id'            => 'return_state',
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
            'name'      => 'sales_order_sn',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customer_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customer_contacts',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customer_phone',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'receivables_code',
            'required'  => false,
            'filters'   => [
                ['name' => 'alpha']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'sales_order_state',
            'required'  => false
        ]);

        $inputFilter->add([
            'name'      => 'return_state',
            'required'  => false
        ]);
    }
}