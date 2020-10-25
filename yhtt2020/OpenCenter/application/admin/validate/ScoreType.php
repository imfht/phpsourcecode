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

class ScoreType extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'title' =>  'require',
        'unit' => 'require'
    ];

    /**
     * 提示消息
     */
    protected $message  =   [
        'title.require' => '积分名称必填',
        'unit.require' => '积分单位必填'
    ];
}