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

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Zend\Form\Form;
use Zend\I18n\Translator\Translator;

class SearchRegionForm extends Form
{
    private $translator;

    public function __construct($name = 'search-region-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-horizontal');

        $this->translator = new Translator();

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements()
    {
        $this->add([
            'type'  => 'text',
            'name'  => 'start_id',
            'attributes'    => [
                'id'            => 'start_id',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('起始ID')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'end_id',
            'attributes'    => [
                'id'            => 'end_id',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('结束ID')
            ]
        ]);

        $this->add([
            'type'  => 'text',
            'name'  => 'region_name',
            'attributes'    => [
                'id'            => 'region_name',
                'class'         => 'form-control input-sm',
                'placeholder'   => $this->translator->translate('地区名称')
            ]
        ]);
    }

    public function addInputFilter()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'      => 'start_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'end_id',
            'required'  => false,
            'filters'   => [
                ['name' => 'ToInt']
            ]
        ]);

        $inputFilter->add([
            'name'      => 'region_name',
            'required'  => false,
            'filters'   => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'HtmlEntities']
            ]
        ]);
    }


    public function criteriaSearchData(Criteria $criteria, ExpressionBuilder $expr)
    {
        $search = $this->getData();

        if(isset($search['start_id']) && $search['start_id'] > 0) {
            $criteria->andWhere($expr->gte('regionId', $search['start_id']));
        }
        if(isset($search['end_id']) && $search['end_id'] > 0) {
            $criteria->andWhere($expr->lte('regionId', $search['end_id']));
        }
        if(isset($search['region_name']) && !empty($search['region_name'])) {
            $criteria->andWhere($expr->contains('regionName', $search['region_name']));
        }

        return $criteria;
    }
}