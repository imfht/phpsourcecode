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
use Doctrine\ORM\EntityManager;
use Store\Validator\GoodsBarCodeValidator;
use Store\Validator\GoodsCodeValidator;
use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class GoodsForm extends Form
{
    private $translator;
    private $entityManager;
    private $goods;

    public function __construct(EntityManager $entityManager = null, $goods = null, $name = 'goods-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->translator   = new Translator();
        $this->entityManager= $entityManager;
        $this->goods        = $goods;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'goodsNumber',
            'attributes'    => [
                'id'            => 'goodsNumber',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goodsName',
            'attributes'    => [
                'id'            => 'goodsName',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'goodsCategoryId',
            'attributes'    => [
                'id'            => 'goodsCategoryId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'brandId',
            'attributes'    => [
                'id'            => 'brandId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'select',
            'name'  => 'unitId',
            'attributes'    => [
                'id'            => 'unitId',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goodsSpec',
            'attributes'    => [
                'id'            => 'goodsSpec',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goodsStock',
            'attributes'    => [
                'id'            => 'goodsStock',
                'class'         => 'form-control',
                'readonly'      => 'readonly',
                'value'         => 0
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goodsBarcode',
            'attributes'    => [
                'id'            => 'goodsBarcode',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'textarea',
            'name'  => 'goodsInfo',
            'attributes'    => [
                'id'            => 'goodsInfo',
                'class'         => 'form-control',
                'cols'          => 5
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goodsRecommendPrice',
            'attributes'    => [
                'id'    => 'goodsRecommendPrice',
                'class' => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'goodsSort',
            'attributes'    => [
                'id'            => 'goodsSort',
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
            'name'      => 'goodsNumber',
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
                        'max'   => 20
                    ]
                ],
                [
                    'name'      => GoodsCodeValidator::class,
                    'options'   => [
                        'entityManager' => $this->entityManager,
                        'goods'         => $this->goods
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goodsName',
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
            'name'      => 'goodsBarcode',
            'required'  => false,
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
                    'name'      => GoodsBarCodeValidator::class,
                    'options'   => [
                        'entityManager' => $this->entityManager,
                        'goods'         => $this->goods
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goodsSpec',
            'required'  => false,
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
            'name'      => 'goodsInfo',
            'required'  => false,
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
                        'max'   => 500
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'goodsCategoryId',
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
            'name'      => 'unitId',
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
            'name'      => 'goodsRecommendPrice',
            'required'  => true,
            'validators'=> [
                [
                    'name' => 'GreaterThan',
                    'options' => [
                        'min' => 0
                    ]
                ]
            ],
            'error_message' => $this->translator->translate('建议售价必须大于0')
        ]);

        $inputFilter->add([
            'name'      => 'brandId',
            'required'  => true,
            'filters'   => [
                ['name' => 'ToInt']
            ],
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => -1
                    ]
                ]
            ]
        ]);

    }
}