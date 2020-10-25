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

namespace Customer\Form;

use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class SearchCustomerForm extends Form
{
    private $translator;

    public function __construct($name = 'search-customer-form', array $options = [])
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
            'name'  => 'customer_name',
            'attributes'    => [
                'id'            => 'customer_name',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('客户名称')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'customer_code',
            'attributes'    => [
                'id'            => 'customer_code',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('客户编码')
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'customer_category_id',
            'attributes'    => [
                'id'            => 'customer_category_id',
                'class'         => 'form-control input-sm'
            ]
        ]);
    }

    public function addInputFilter()
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
            'name'      => 'customer_name',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customer_code',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'customer_category_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);
    }
}