<?php
namespace app\run\controller;

use app\common\controller\Run;

class QueryData extends Run
{
    //初始化 需要调父级方法
    public function initialize()
    {        
        call_user_func(['parent', __FUNCTION__]); 
    }
    
    //列表 
    public function lists()
    {
        if (!$this->local['list_fields']) {
            $this->local['list_fields'] = [
                'title',
                'query',
                'menu_id',
                'type',
                'is_family',
                'is_verify',
                'get' => ['name' => '获取方式', 'list' => 'query_get']
            ];
        }
        return call_user_func(['parent', __FUNCTION__]);
    }
    
    //添加
    public function create()
    {
        $this->assignDefault('list_count',1);
        return call_user_func(['parent', __FUNCTION__]);
    }
    
    //修改
    public function modify()
    {        
        return call_user_func(['parent', __FUNCTION__]);
    } 
    
    //删除
    public function delete()
    {        
        return call_user_func(['parent', __FUNCTION__]);
    }  
}
