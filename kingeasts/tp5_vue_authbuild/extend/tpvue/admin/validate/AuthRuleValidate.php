<?php
// 权限验证       
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\validate;

use think\Validate;

class AuthRuleValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'title|节点名称'=>'require',
        'name|节点地址'=>'require|regex:/\w+\/\w+\/\w+/i',
        'level|节点类型'=>'require|gt:0',
        'status'=>'require',
        'sort|节点排序'=>'require|number'
    ];

    protected $message = [
        'name.regex'=>'节点地址不符合规范',
        'level.gt'=>'请选择节点类型',
        'status'=>'请选择节点状态'
    ];
}
