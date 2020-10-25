<?php
namespace app\run\controller;

use app\common\controller\Run;

class Link extends Run
{
    public function initialize(){
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    public function lists(){
        if(!$this->local['list_fields'])
        $this->local['list_fields'] = array(
            'title',
            'menu_id',
            'link',
            'image',
            //'user_id',
            'created',
            'is_verify',
            'is_index'
        );
        call_user_func(array('parent',__FUNCTION__));
    } 
    
    
    public function create(){
        $this->assignDefault('list_order',0);
        return call_user_func(array('parent',__FUNCTION__));
    }   
}
