<?php
namespace app\run\controller;

use app\common\controller\Run;

class Page extends Run
{
    public function initialize(){
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    public function lists(){
        if (!$this->local['filter']) {
            $this->local['filter'] = [
                'title',
                'menu_id'
            ];
        }
        if(!$this->local['list_fields'])
        $this->local['list_fields'] = array(
            'title',
            'image',
            'menu_id',
            'is_verify'
        );
        call_user_func(array('parent',__FUNCTION__));
    } 
    
    
    public function create(){
        $this->assignDefault('list_order',0);
        if($this->args['parent_id']) {
            $this->assignDefault('title', menu(intval($this->args['parent_id']), 'title'));
        }
        return call_user_func(array('parent',__FUNCTION__));
    } 
    
    public function modify()
    {
        $this->mdl->form['created']['elem'] = 'format';
        $this->mdl->form['modified']['elem'] = 'format';
        return call_user_func(array('parent', __FUNCTION__));
    } 
         
}