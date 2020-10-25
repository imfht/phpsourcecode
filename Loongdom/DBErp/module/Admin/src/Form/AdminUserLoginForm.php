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
use Zend\InputFilter\InputFilter;
use Admin\Entity\AdminUser;

class AdminUserLoginForm extends Form
{
    public function __construct($name = 'admin-login', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     *
     */
    protected function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'admin_name',
            'attributes'    => [
                'id'            => 'admin_name',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'password',
            'name'  => 'admin_passwd',
            'attributes'    => [
                'id'            => 'admin_passwd',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'checkbox',
            'name'  => 'remember_me'
        ]);

        $this->add([
            'type'  => 'captcha',
            'name' => 'login_captcha',
            'attributes'    => [
                'id'            => 'login_captcha',
                'class'         => 'form-control'
            ],
            'options' => [
                'captcha' => [
                    'class' => 'Figlet',
                    'wordLen' => 5,
                    'expiration' => 180
                ],
            ],
        ]);

        $this->add([
            'type'  => 'csrf',
            'name'  => 'login_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout'  => 180
                ]
            ]
        ]);

        $this->add([
            'type'  => 'submit',
            'name'  => 'submit-login',
            'attributes'    => [
                'id'    => 'submit-login',
                'class' => 'btn btn-primary btn-block btn-flat'
            ],
        ]);
    }

    protected function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'admin_name',
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
                        'max'   => 60
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'admin_passwd',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'remember_me',
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
    }
}