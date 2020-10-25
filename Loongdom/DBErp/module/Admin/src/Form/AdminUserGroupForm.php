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

use Admin\Data\Config;
use Zend\Form\Form;

class AdminUserGroupForm extends Form
{
    public function __construct($name = 'admin-group-form', array $options = [])
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'adminGroupName',
            'attributes'    => [
                'id'            => 'adminGroupName',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'multiCheckbox',
            'name'  => 'adminGroupPermission'
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

    protected function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'adminGroupName',
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
                        'max'   => 100
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'adminGroupPermission',
            'required'  => false
        ]);
    }
}