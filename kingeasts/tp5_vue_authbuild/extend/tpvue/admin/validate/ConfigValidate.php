<?php
// 配置验证       
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\validate;

use think\Validate;

class ConfigValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'name' => 'require',
        'title' => 'require',
        'type' => 'require',
        'sort' => 'number',
        'group' => 'require',
        'status' => 'require',
        'value' => 'require',
        'remark' => 'require',
    ];

    protected $message = [
    	'name.require' => '配置标识不得为空',
    	'title.require' => '标识说明不得为空',
    	'sort.number' => '排序必须为数字',
    	'type.require' => '配置类型不得为空',
    	'type.number' => '配置类型必须为数字',
    	'group.require' => '配置分组不得为空',
    	'group.number' => '配置分组必须为数字',
    	'status.require' => '是否显示不得为空',
    	'status.number' => '是否显示必须为数字',
    	'value.require' => '配置参数不得为空',
    	'remark.require' => '配置说明不得为空',
    ];

    protected $scene=[
        'addConfig' => ['name','title','sort','type','group','status','value','remark'],
    	'editConfig' => ['name','title','sort','type','group','status','value','remark'],
    ];
}