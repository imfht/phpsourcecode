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
use Zend\I18n\Translator\Translator;

class AppForm extends Form
{
    private $translator;

    public function __construct($name = 'app-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->translator = new Translator();

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'appName',
            'attributes'    => [
                'id'            => 'appName',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'appAccessId',
            'attributes'    => [
                'id'            => 'appAccessId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'appAccessSecret',
            'attributes'    => [
                'id'            => 'appAccessSecret',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'appUrl',
            'attributes'    => [
                'id'            => 'appUrl',
                'class'         => 'form-control'
            ]
        ]);
        $this->add([
            'type'  => 'text',
            'name'  => 'appUrlPort',
            'attributes'    => [
                'id'    => 'appUrlPort',
                'class' => 'form-control',
                'value' => 80
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'appType',
            'attributes'    => [
                'id'            => 'appType',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'checkbox',
            'name'  => 'appState',
            'attributes' => [
                'value' => 1
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'appGoodsBindType',
            'attributes' => [
                'id'            => 'appGoodsBindType',
                'class'         => 'form-control'
            ],
            'options' => [
                'value_options' => [
                    'goodsNumber'   => '商品编号',
                    'goodsBarcode'  => '商品条形码'
                ]
            ]
        ]);

        $this->add([
            'type'  => 'checkbox',
            'name'  => 'appGoodsBind',
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

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'appName',
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
            'name'      => 'appAccessId',
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
                        'max'   => 30
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'appAccessSecret',
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
                        'max'   => 50
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'appUrl',
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
                    'name'  => 'Uri',
                    'options'   => [
                        'allowRelative' => false
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'appUrlPort',
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
            'name'      => 'appType',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'adminState',
            'required'  => false,
            'validators'=> [
                [
                    'name'      => 'InArray',
                    'options'   => [
                        'haystack'  => [0, 1] //这里之所以写 0 ，是因为空的状态下是0（其实禁用状态为 -1）
                    ]
                ]
            ]
        ]);
    }
}