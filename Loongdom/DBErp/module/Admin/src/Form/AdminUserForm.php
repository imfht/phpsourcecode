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
use Admin\Validator\EmailExistsValidator;
use Admin\Validator\UserExistsValidator;
use Zend\Form\Form;
use Zend\Validator\Hostname;

class AdminUserForm extends Form
{

    private $name;
    private $entityManager;
    private $user;

    public function __construct($name = 'add', array $options = [], $entityManager = null, $user = null)
    {
        parent::__construct('admin-user-add-form', $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->name = $name;
        $this->user = $user;
        $this->entityManager = $entityManager;

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements()
    {
        $this->add([
            'type'  => 'select',
            'name'  => 'adminGroupId',
            'attributes'    => [
                'id'            => 'adminGroupId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'adminName',
            'attributes'    => [
                'id'            => 'adminName',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'email',
            'name'  => 'adminEmail',
            'attributes'    => [
                'id'            => 'adminEmail',
                'class'         => 'form-control'
            ]
        ]);

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
            'type'  => 'checkbox',
            'name'  => 'adminState',
            'attributes' => [
                'value' => 1
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
            'name'      => 'adminGroupId',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ],
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => 0
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'adminEmail',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim']
            ],
            'validators'=> [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 1,
                        'max'   => 100
                    ]
                ],
                [
                    'name'      => 'EmailAddress',
                    'options'   => [
                        'allow'         => Hostname::ALLOW_DNS,
                        'useMxCheck'    => false
                    ]
                ],
                [
                    'name'      => EmailExistsValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager,
                        'user'          => $this->user
                    ]
                ]
            ]
        ]);

        if($this->name == 'add') {
            $inputFilter->add([
                'name'      => 'adminName',
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
                    ],
                    [
                        'name'      => UserExistsValidator::class,
                        'options'    => [
                            'entityManager' => $this->entityManager,
                            'user'          => $this->user
                        ]
                    ]
                ]
            ]);

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

        if(!$this->user || $this->user->getAdminId() != 1) {
            $inputFilter->add([
                'name'      => 'adminState',
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
}