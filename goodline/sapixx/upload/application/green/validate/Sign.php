<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 新闻头条
 */
namespace app\green\validate;
use think\Validate;

class Sign extends Validate{

    protected $rule = [
        'config_id' => 'require|number',
        'point'     => 'require|number',
    ];

    protected $message = [
        'config_id' => '签到天数丢失',
        'point'     => '签到积分必须填写',
    ];

    protected $scene = [
        'save'    => ['config_id','point'],
    ];
}