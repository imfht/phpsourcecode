<?php
namespace app\run\controller;

use app\common\controller\Run;

class Download extends Run
{
    public function initialize(){
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    public function lists(){
         if (!$this->local['filter']) {
            $this->local['filter'] = [
                'title',
                'menu_id',
                'file_name'
            ];
        }
        if(!$this->local['list_fields'])
        $this->local['list_fields'] = array(
            'title',
            'file_name',
            'size',
            'menu_id',
            //'user_id',
            'created',
            'is_verify',
            'is_index'
        );
        call_user_func(array('parent',__FUNCTION__));
    } 
    
    
    public function create(){
        $this->assignDefault('date',date('Y-m-d'));
        $this->assignDefault('list_order',0);
        $this->mdl->form['file_name']['elem'] = 0 ;
        return call_user_func(array('parent',__FUNCTION__));
    }   
         
}