<?php
namespace Modules\Taxonomy\Forms;

use Modules\Taxonomy\Library\Form;

class Field{
    public static function termInit(&$name,&$element){
        $settings = $element['settings'];
        if($element['widget'] == 'Select' || $element['widget'] == 'Selects'){
            $element = Form::formTermOptions($element);
        }
        if($element['widget'] == 'Tags'){
            $element['filter'] = array_merge($element['filter'],array('commaExplode'));
        }
    }
}