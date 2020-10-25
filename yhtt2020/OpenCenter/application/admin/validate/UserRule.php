<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/9/30
 * Time: 14:00
 * ----------------------------------------------------------------------
 */
namespace app\admin\validate;

use think\Validate;

class UserRule extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'module' => 'require',
        'name'  =>  'require|unique:user_rule',
        'pid' =>  'require',
        'sort' =>  'require',
        'title' =>  'require',
    ];

    /**
     * 提示消息
     */
    protected $message  =   [
        'title.require' => '标题必填',
        'module.require' => '模块名必填',
        'name.require' => 'URL必填',
        'name.unique' => 'URL已存在',
        'sort.require' => '排序必填',
        'status.require' => '状态必选'
    ];
}