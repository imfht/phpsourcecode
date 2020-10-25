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

class ActionLimit extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'title' => 'require',
        'rule_id' => 'require|integer',
        'frequency' => 'require|integer',
        'time_number' => 'require|integer',
        'time_unit' => 'require',
        'punish_type' => 'require'
    ];

    /**
     * 提示消息
     */
    protected $message  =   [
        'title.require' => '标题必填',
        'rule_id.require' => '绑定权限必选',
        'rule_id.integer' => '无效的权限ID',
        'frequency.require' => '频次必填',
        'frequency.integer' => '频次必须是整数',
        'time_number.require' => '时间量必填',
        'time_number.integer' => '时间量必须是整数',
        'time_unit.require' => '时间单位必选',
        'punish_type.require' => '处罚方式必选'
    ];
}