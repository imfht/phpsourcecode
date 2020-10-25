<?php
namespace app\Api\controller;
use think\Controller;
use think\Db;

class Api extends Controller{
    
    /**
     * 更新字段
     */
    public function upField(){
        $table=input('table');//表名
        $id_name=input('id_name');//条件字段
        $id_value=input('id_value');//条件值
        $field=input('field');//修改的字段
        $field_value=input('field_value');//修改的值
        if ($field_value=='false'){
            $field_value=2;
        }
        if (empty($table)||empty($id_name)||empty($id_value)||empty($field)||$field_value===false){
            return ajaxReturn(0,'参数不足');
        }
        $where[$id_name]=['eq',$id_value];
        $status=Db::name($table)->where($where)->setField($field,$field_value);
        if ($status){
            return ajaxReturn(200,'操作成功');
        }else{
            return ajaxReturn(0,'操作失败');
        }
    }
    /**
     * 切换语言
     */
    public function change(){
        switch (input('lang')) {
            case 'zh-cn':
                cookie('think_var', 'zh-cn');
                break;
            case 'en-us':
                cookie('think_var', 'en-us');
                break;
            //其它语言
        }
        return ajaxReturn(200,'语言切换成功！您现在操作的是'.input('lang'));
    }
}
