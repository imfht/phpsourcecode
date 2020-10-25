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

class RegionForm extends Form
{
    public function __construct($name = 'region-form', array $options = [])
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
            'type'  => 'textarea',
            'name'  => 'regionName',
            'attributes'    => [
                'id'            => 'regionName',
                'class'         => 'form-control',
                'rows'          => 6
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'regionSort',
            'attributes'    => [
                'id'            => 'regionSort',
                'class'         => 'form-control',
                'value'         => 255
            ]
        ]);

        $this->add([
            'type'  => 'hidden',
            'name'  => 'regionTopId',
            'attributes'    => [
                'id'            => 'regionTopId',
                'value'         => 0
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
            'name'      => 'regionName',
            'required'  => true,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'regionSort',
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