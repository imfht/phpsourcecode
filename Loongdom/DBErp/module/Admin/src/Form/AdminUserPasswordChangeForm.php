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

class AdminUserPasswordChangeForm extends Form
{

    public function __construct($name = 'admin-user-password-form', array $options = [])
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
            'type'  => 'password',
            'name'  => 'adminPassword',
            'attributes'    => [
                'id'            => 'adminPassword',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'password',
            'name'  => 'adminComPassword',
            'attributes'    => [
                'id'            => 'adminComPassword',
                'class'         => 'form-control'
            ]
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
            'name'      => 'adminPassword',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 6
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'adminComPassword',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim']
            ],
            'validators'=> [
                [
                    'name'      => 'Identical',
                    'options'   => [
                        'token' => 'adminPassword'
                    ]
                ]
            ]
        ]);
    }
}