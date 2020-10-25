<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\validate;

/**
 * 百度数据验证器
 */
class Baidu extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'name'              => 'require|unique:baidu',
        'describe'          => 'require',
        'url'               => 'require',
        'show_url'          => 'require',
        'snapshoot_url'     => 'require',
        'sort'              => 'require|number',
    ];

    // 验证提示
    protected $message  =   [
        
        'name.require'              => '百度数据名称不能为空',
        'name.unique'               => '百度数据名称已存在',
        'url.require'               => '跳转URL不能为空',
        'show_url.require'          => '显示URL不能为空',
        'snapshoot_url.require'     => '快照URL不能为空',
        'sort.require'              => '排序值不能为空',
        'sort.number'               => '排序值必须为数字'
    ];

    // 应用场景
    protected $scene = [
        
        'edit' =>  ['name','describe','url','show_url','snapshoot_url','sort'],
    ];
    
}
