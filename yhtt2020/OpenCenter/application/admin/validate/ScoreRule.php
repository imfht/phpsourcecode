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

class ScoreRule extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'title' =>  'require',
        'rule_id' => 'number',
        'change_type' => 'number',
        'change_num' => 'number',
        'score_type' => 'number',
        'frequency' => 'number',
        'time_unit' => 'number',
    ];

    /**
     * 提示消息
     */
    protected $message  =   [
        'title.require' => '规则名称必填',
        'rule_id.number' => '绑定权限必选',
        'change_type.number' => '变动方式必选',
        'change_num.number' => '变动数量必填',
        'score_type.number' => '积分类型必选',
        'frequency.number' => '频次必填',
        'time_unit.number' => '时间周期必选',
    ];
}