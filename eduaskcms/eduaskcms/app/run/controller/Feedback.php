<?php
namespace app\run\controller;

use app\common\controller\Run;

class Feedback extends Run
{
    public function initialize(){
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    public function lists(){
        if (!$this->local['filter']) {
            $this->local['filter'] = [
                'title',
                'truename',
                'mobile'
            ];
        }
        if(!$this->local['list_fields'])
        $this->local['list_fields'] = array(
            'title',
            'user_id',
            'truename',
            'mobile',
            'reply_user_id',
            'menu_id',
            'created',            
            'is_verify',
            'is_finish',
        );
        $this->local['actions']['create'] = false ;
        $this->local['order'] = ['is_finish' => 'ASC', 'list_order' => 'DESC', 'id' => 'DESC'];
        call_user_func(array('parent',__FUNCTION__));
    } 
    
    public function create(){
        return $this->message('error','该模块不允许添加');	
    }   
    
    public function modify(){
        if($this->Form->data[$this->m]['reply_content'] && !$this->Form->data[$this->m]['reply_user_id']) {
            $this->Form->data[$this->m]['reply_user_id'] = $this->Auth->user('id');
        }            
        call_user_func(array('parent',__FUNCTION__));
        if ($this->Form->data[$this->m]['reply_user_id']) {            
            $this->mdl->form['reply_user_id']['elem'] = 'format';
            //$this->assignDefault('reply_user_id',$this->login['id'],NULL,true);
        }
        if ($this->Form->data[$this->m]['user_id']) {            
            $this->mdl->form['user_id']['elem'] = 'format';
        }
    }
    
    
         
}