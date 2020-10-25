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
use Zend\Validator\Hostname;

class SearchAdminForm extends Form
{
    private $translator;

    public function __construct($name = 'search-admin-form', array $options = [])
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
            'name'  => 'admin_name',
            'attributes'    => [
                'id'            => 'admin_name',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('管理员名称')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'admin_email',
            'attributes'    => [
                'id'            => 'admin_email',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('电子邮箱')
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'admin_group_id',
            'attributes'    => [
                'id'            => 'admin_group_id',
                'class'         => 'form-control input-sm'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'admin_state',
            /*'options' => [
                'value_options' => [
                    1 => $this->translator->translate('启用'),
                    2 => $this->translator->translate('禁用'),
                ]
            ],*/
            'attributes'    => [
                'id'            => 'admin_state',
                'class'         => 'form-control input-sm'
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
            'name'      => 'admin_name',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'admin_email',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ],
            'validators'=> [
                [
                    'name'      => 'EmailAddress',
                    'options'   => [
                        'allow'         => Hostname::ALLOW_DNS,
                        'useMxCheck'    => false
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'admin_group_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'admin_state',
            'required'  => false,
            'validators'=> [
                [
                    'name'      => 'InArray',
                    'options'   => [
                        'haystack'  => [0, 1]
                    ]
                ]
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