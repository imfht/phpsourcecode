<?php
namespace Modules\Form\Forms\Element;

use Phalcon\Forms\Element\Select;
use Phalcon\Tag\Select as TagSelect;

class Selects extends Select
{
    public function render($attributes = null)
    {
        if(is_null($attributes)){
            $attributes = array();
        }
        $attributes = array_merge(array(
            'name'=>$this->getName().'[]',
            'multiple' => 'multiple',
            ),$attributes);
        return TagSelect::selectField($this->prepareAttributes($attributes), $this->_optionsValues);
    }
}
