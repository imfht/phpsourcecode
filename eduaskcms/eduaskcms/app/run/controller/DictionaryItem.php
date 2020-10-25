<?php
namespace app\run\controller;

use app\common\controller\Run;

class DictionaryItem extends Run
{
    public function initialize(){
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    public function lists(){
        if(!$this->local['list_fields'])
        $this->local['list_fields'] = array(
			'dictionary_id',
			'key',
			'value',			
			'created',
        );
        
        call_user_func(array('parent',__FUNCTION__));
    } 
    
    
    public function create(){
        return call_user_func(array('parent',__FUNCTION__));
    }   
         
}