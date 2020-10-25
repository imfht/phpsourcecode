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

use Admin\Data\Config;
use Customer\Validator\SupplierCategoryCodeValidator;
use Zend\Form\Form;

class SupplierCategoryForm extends Form
{
    private $entityManager;
    private $supplierCategory;

    public function __construct($entityManager = null, $supplierCategory = null, $name = 'supplier-category-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->entityManager    = $entityManager;
        $this->supplierCategory = $supplierCategory;

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'supplierCategoryCode',
            'attributes'    => [
                'id'            => 'supplierCategoryCode',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplierCategoryName',
            'attributes'    => [
                'id'            => 'supplierCategoryName',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'supplierCategorySort',
            'attributes'    => [
                'id'            => 'supplierCategorySort',
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
            'name'      => 'supplierCategoryCode',
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
                    'name'      => SupplierCategoryCodeValidator::class,
                    'options'    => [
                        'entityManager'     => $this->entityManager,
                        'supplierCategory'  => $this->supplierCategory
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'supplierCategoryName',
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
            'name'      => 'supplierCategorySort',
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