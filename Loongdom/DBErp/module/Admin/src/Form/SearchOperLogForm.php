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

namespace Admin\Form;

use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class SearchOperLogForm extends Form
{
    private $translator;

    public function __construct($name = 'search-oper-log-form', array $options = [])
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
            'name'  => 'log_oper_user',
            'attributes'    => [
                'id'            => 'log_oper_user',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('操作者')
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'log_oper_user_group',
            'attributes'    => [
                'id'            => 'log_oper_user_group',
                'class'         => 'form-control input-sm'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'log_ip',
            'attributes'    => [
                'id'            => 'log_ip',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('ip地址')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'log_body',
            'attributes'    => [
                'id'            => 'log_body',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('操作描述')
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
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'log_oper_user',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'log_oper_user_group',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'log_ip',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'log_body',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
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
    }
}