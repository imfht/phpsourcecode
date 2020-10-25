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

namespace Finance\Form;

use Admin\Data\Config;
use Finance\Validator\PayAmountValidator;
use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class PayableLogForm extends Form
{
    private $payAmount;
    private $translator;

    public function __construct($payAmount, $name = 'payable-log-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'form-horizontal');

        $this->payAmount = $payAmount;
        $this->translator= new Translator();

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'payLogAmount',
            'attributes'    => [
                'id'            => 'payLogAmount',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'file',
            'name'  => 'payFile',
            'attributes' => [
                'id' => 'payFile'
            ],
            'options' => [
                'label' => $this->translator->translate('上传凭证'),
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'payLogUser',
            'attributes'    => [
                'id'            => 'payLogUser',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'payLogPaytime',
            'attributes'    => [
                'id'            => 'payLogPaytime',
                'class'         => 'form-control'
            ]
        ]);

        $this->add([
            'type'  => 'textarea',
            'name'  => 'payLogInfo',
            'attributes'    => [
                'id'            => 'payLogInfo',
                'class'         => 'form-control',
                'cols'          => 5
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
            'name'      => 'payLogAmount',
            'required'  => true,
            'validators'=> [
                [
                    'name'      => 'GreaterThan',
                    'options'   => [
                        'min'   => 0
                    ]
                ],
                [
                    'name'      => PayAmountValidator::class,
                    'options'   => [
                        'payAmount' => $this->payAmount
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'type'      => 'Zend\InputFilter\FileInput',
            'name'      => 'payFile',
            'required'  => false,
            'validators'=> [
                ['name' => 'FileUploadFile'],
                [
                    'name' => 'FileMimeType',
                    'options' => [
                        'mimeType' => ['image/jpeg', 'image/png']
                    ]
                ],
                ['name' => 'FileIsImage'],
                [
                    'name' => 'FileSize',
                    'options' => [
                        'min' => '1kB',
                        'max' => '8MB'
                    ]
                ]
            ],
            'filters'  => [
                [
                    'name' => 'FileRenameUpload',
                    'options' => [
                        'target' => getcwd() . '/public/upload/payable',
                        'useUploadName'=>true,
                        'useUploadExtension'=>true,
                        'overwrite'=>true,
                        'randomize'=>true
                    ]
                ]
            ]
        ]);

        $inputFilter->add([
            'name'      => 'payLogUser',
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
            'name'      => 'payLogPaytime',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'payLogInfo',
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
                        'max'   => 255
                    ]
                ]
            ]
        ]);
    }
}