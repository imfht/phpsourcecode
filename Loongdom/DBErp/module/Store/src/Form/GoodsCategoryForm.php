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

namespace Store\Form;

use Admin\Data\Config;
use Store\Validator\GoodsCategoryCodeValidator;
use Store\Validator\GoodsCategoryValidator;
use Zend\Form\Form;

class GoodsCategoryForm extends Form
{
    private $entityManager;
    private $category;

    public function __construct($entityManager = null, $category = null, $name = 'goods-category-form', array $options = [])
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->category = $category;
        $this->entityManager = $entityManager;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'select',
            'name'  => 'goodsCategoryTopId',
            'attributes'    => [
                'id'            => 'goodsCategoryTopId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goodsCategoryName',
            'attributes'    => [
                'id'            => 'goodsCategoryName',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goodsCategoryCode',
            'attributes'    => [
                'id'            => 'goodsCategoryCode',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goodsCategorySort',
            'attributes'    => [
                'id'            => 'goodsCategorySort',
                'class'         => 'form-control',
                'value'         => 255
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
            'name'      => 'goodsCategoryTopId',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ],
            'validators'=> [
                [
                    'name'      => GoodsCategoryValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager,
                        'category'      => $this->category
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goodsCategoryCode',
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
                ],
                [
                    'name'      => GoodsCategoryCodeValidator::class,
                    'options'    => [
                        'entityManager' => $this->entityManager,
                        'category'      => $this->category
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goodsCategoryName',
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
            'name'      => 'goodsCategorySort',
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

    }
}