<?php
namespace app\run\controller;

use app\common\controller\Run;

class Region extends Run
{
    //初始化 需要调父级方法
    public function initialize()
    {        
        call_user_func(['parent', __FUNCTION__]); 
    }
    
    //列表 
    public function lists()
    {
        if (!$this->mdl->count()) {
            return $this->message('success', '请自行导入地区数据(\data\region.sql)', ['back' => false]);
        }
        
        ## 搜索
        if (!$this->local['filter']) {
            $this->local['filter'] = [
                'title',
                'first_letter'
            ];
        }
        ## 列表
        if (!$this->local['list_fields']) {
            $this->local['list_fields'] = [
                'title',
                'first_letter',
                'list_order'
            ];
        }
        $this->local['order'] = ['parent_id' => 'ASC', 'list_order' => 'DESC', 'id'=>'ASC'];
        $this->local['where']['id'] = ['id', '>', 1]; 
        return call_user_func(['parent', __FUNCTION__]);
    }
    
    //添加
    public function create()
    {        
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
