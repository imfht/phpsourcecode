<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 新闻头条
 */
namespace app\green\validate;
use think\Validate;

class Job extends Validate{

    protected $rule = [
        'uid'        => 'require|number',
        'name'       => 'require',
        'city'       => 'require',
        'occupation' => 'require',
        'card'       => 'require',
        'front'      => 'require',
        'back'       => 'require',
    ];

    protected $message = [
        'uid'        => '缺少用户ID',
        'name'       => '姓名必须填写',
        'city'       => '意向城市必须选择',
        'occupation' => '目前职业必须填写',
        'card'       => '身份证号必须填写',
        'front'      => '手持身份证正面必须上传',
        'back'       => '手持身份证反面必须上传',
    ];

    protected $scene = [
        'edit'    => ['uid','name','city','occupation','card','front','back'],
    ];
}