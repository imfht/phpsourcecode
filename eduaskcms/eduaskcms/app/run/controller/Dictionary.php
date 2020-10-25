<?php
namespace app\run\controller;

use app\common\controller\Run;

class Dictionary extends Run
{
    public function initialize(){
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    public function lists(){
        if(!$this->local['list_fields'])
        $this->local['list_fields'] = array(
			'title',
			'model',
			'field',			
			'created',
            'dictionary_item_count',
        );
        $this->addItemAction('添加字典项目' , array('DictionaryItem/create',['parent_id'=>'id'],'parse'=>['parent_id']) , '&#xe654;');
        call_user_func(array('parent',__FUNCTION__));
    } 
    
    
    public function create(){
		if ($this->args['parent_id']) {
			$this->assignDefault('title',menu($this->args['parent_id'],'title'));
		}
        if ($this->args['model'] && $this->args['field']) {
            $id  = $this->mdl->where(['model' => trim($this->args['model']), 'field' => trim($this->args['field'])])->value('id');
            if ($id) {
                $this->redirect('DictionaryItem/create', ['parent_id' => $id]);
            }
        }
        return call_user_func(array('parent',__FUNCTION__));
    }   
}
