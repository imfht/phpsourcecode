<?php
namespace Modules\Node\Forms;

use Modules\Node\Library\Form;

class Field{
    public static function nodeInit(&$name,&$element){
        global $di;
        if(!isset($element['contentModel'])){
            $element['contentModel'] = 'book';
        }
        $nodeEntity = $di->getShared('entityManager')->get('node');
        $data = $nodeEntity->find(array(
            'andWhere' => array(
                array(
                    'conditions' => '%contentModel% = :contentModel:',
                    'bind' => array(
                        'contentModel' => 'book'
                    )
                )
            )
        ));
        $options = array();
        if($element['widget'] == 'Select' || $element['widget'] == 'Selects'){
            $options[0] = '不加入书本';
            foreach ($data as $node){
                $options[$node->id] = $node->title->value;
            }
            $element['options'] = $options;
        }
    }
}