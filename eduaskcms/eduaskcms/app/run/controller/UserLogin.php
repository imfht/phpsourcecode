<?php
namespace app\run\controller;

use app\common\controller\Run;

class UserLogin extends Run
{
    //初始化 需要调父级方法
    public function initialize()
    {        
        call_user_func(['parent',__FUNCTION__]); 
    }
    
    //列表 
    public function lists()
    {
        if(!$this->local['list_fields']){            
        
            $this->local['list_fields'] = [
                'user_id',
                'ip',
                'created'
            ];
        }
        $this->local['actions']['create'] = false ;
        $this->local['item_actions']['modify']= false;
        return call_user_func(['parent',__FUNCTION__]);
    }
    
    //添加
    public function create(){        
        return $this->message('error', '该模型不支持添加');
    }
    
    //修改
    public function modify(){        
        return $this->message('error', '该模型不支持修改');
    } 
    
    //删除
    public function delete(){        
        return call_user_func(['parent', __FUNCTION__]);
    }  
}