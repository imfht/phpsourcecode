<?php
namespace app\cms\traits;

trait Content
{
    
    protected function add_check($mid=0,$fid=0,&$data=[]){
        $this->check_limit_view($data);
        return parent::add_check($mid,$fid,$data);
    }
    
    protected function edit_check($id=0,$info=[],&$data=[]){
        $this->check_limit_view($data);
        return parent::edit_check($id,$info,$data);
    }
    
    protected function check_limit_view(&$data){
        if ($this -> request -> isPost()) {
            if ($data['price']>0 && !strstr($data['content'],'[/paymoney]')) {
                //设置访问权限的内容不暴露简介
                $data['content'] = '<!--[paymoney='.intval($data['price']).']-->'.$data['content'].'<!--[/paymoney]-->';
            }elseif($data['price']<0.01 && strstr($data['content'],'[/paymoney]')){
                $data['content'] = str_replace('<!--[/paymoney]-->', '', $data['content']);
                $data['content'] = preg_replace('/<!--\[paymoney=([\d\.]+)\]-->/', '', $data['content']);
            }
        }
    }
    /**
     * 同时适用于前台与后台 新增加后做个性拓展
     * @param number $id 内容ID
     * @param number $data 内容数据
     */
//     protected function end_add($id=0,$data=[]){
//     }
    
    /**
     * 同时适用于前台与后台 修改后做个性拓展
     * @param number $id 内容ID
     * @param array $data 内容数据
     */
//     protected function end_edit($id=0,$data=[]){
//     }
    
    /**
     * 同时适用于前台与后台 删除后做个性拓展
     * @param number $id 内容ID
     * @param array $info 内容数据
     */
//     protected function end_delete($id=0,$info=[]){
//     }
}