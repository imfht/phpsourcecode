<?php
namespace app\run\controller;

use app\common\controller\Run;

class SettingGroup extends Run
{
    public function initialize()
    {
        
        call_user_func(array('parent',__FUNCTION__));
    }
    
    
    
    
    public function lists()
    {
        
        if(!$this->local['list_fields'])
        $this->local['list_fields'] = array(
            'id',
            'title'
        );
        $this->local['order'] = array('id' => 'ASC');
        $this->addItemAction('查看设置', array('Setting/lists',['parent_id'=>'id'],'parse'=>['parent_id']), '&#xe60a;');
        return call_user_func(array('parent',__FUNCTION__));
    }
    
    
    
}
