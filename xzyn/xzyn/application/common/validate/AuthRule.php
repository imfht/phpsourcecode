<?php
namespace app\common\validate;

use think\Validate;

class AuthRule extends Validate
{
    protected $rule = [
        'id|ID' => 'require|number',
        'pid|父级ID' => 'require|integer|is_pid',
        'title|节点名称' => 'require',
        'name|节点地址' => 'require|unique:auth_rule|/^[a-zA-Z0-9\/\-\_]+$/',
        'level|节点类型' => 'require|in:1,2,3',
        'status|状态' => 'require|in:0,1',
        'ismenu|是否菜单' => 'require|in:0,1',
        'sorts|排序' => 'require|integer|>=:1',
    ];

    protected $scene = [
        'add'   => ['pid', 'title', 'name', 'level', 'status', 'ismenu', 'sorts'],
        'edit'  => ['pid', 'title', 'name', 'level', 'status', 'ismenu', 'sorts','id'],
        'status' => ['status','id'],
        'ismenu' => ['ismenu','id'],
        'title' => ['title','id'],
        'name' => ['name','id'],
    ];

    //自定义验证规则
    protected function is_pid($value,$rule,$data=[]) {
        $AuthRule = new \app\common\model\AuthRule;
        $ruleinfo = $AuthRule->where(['id'=>$data['pid']])->find();
        if( $ruleinfo['level'] == 3 ){
            return "父级节点不能是操作类型，请重新选择";
        }
        if( !empty($data['id']) ){
            if( $data['id'] == $data['pid'] ){
                return '父级节点不正确，请重新选择';
            }
        }
        return true;        
    }


}