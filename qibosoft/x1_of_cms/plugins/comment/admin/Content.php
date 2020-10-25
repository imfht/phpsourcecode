<?php
namespace plugins\comment\admin;

use app\common\controller\AdminBase; 
use plugins\comment\model\Content AS contentModel;
use app\common\traits\AddEditList;

class Content extends AdminBase
{
    use AddEditList;
    protected $validate = '';

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new contentModel();
        
        //顶部菜单
        $this->tab_ext['top_button'] = [
                ['type'=>'delete'],
        ];
        
        //右边菜单
        $this->tab_ext['right_button'] = [
                ['type'=>'delete'],
        ];
        
        $this->tab_ext['page_title'] = '评论管理';
        
        //搜索字段
        $this->tab_ext['search'] = [
                'content'=>'评论内容',
                'uid'=>'发布者UID',
                'sysid'=>'频道ID',
        ];
        $array = [];
        foreach(modules_config() AS $rs){
            if(in_array($rs['keywords'], ['bbs','search','tongji'])){
                continue;
            }
            $array[$rs['id']] = $rs['name'];
        }
        //筛选字段
        $this -> tab_ext['filter_search'] = [
            'sysid'=>$array,
            'status'=>['未审核','已审核'],
        ];
        
        $this->list_items = [
                    ['content','评论内容','callback',function($value){
                        return get_word(del_html($value), 70);
                    }],
                    ['status','审核与否','switch'],               
                    ['uid','发布者','username'],
                    ['sysid','所属模块','callback',function($value){
                        return $value>0?modules_config($value)['name']:plugins_config(abs($value))['name'];
                    }],
                    ['list','排序值','text.edit'],
                    ['id','来源','callback',function($k,$rs){
                        if ($rs['sysid']>0) {
                            $dirname = modules_config($rs['sysid'])['keywords'];
                            $url = iurl("$dirname/content/show",['id'=>$rs['aid']]);
                        }elseif($rs['sysid']<0){
                            $dirname = plugins_config(abs($rs['sysid']))['keywords'];
                            $url = purl("$dirname/content/show",['id'=>$rs['aid']],'index');
                        }
                        return "<a href='{$url}' target='_blank' class='si si-link' title='查看来源于哪个主题'></a>";
                    }],
                ];
    }
}
