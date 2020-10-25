<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 时间添加
 */
namespace app\fastshop\validate;
use think\Validate;

class Times extends Validate{

    protected $rule = [
        'id'        => 'require|number',
        'sort'      => 'require|number',
        'name'       => 'require',
        'start_time' => 'require|integer|between:0,24',
        'end_time'   => 'require|integer|between:0,24',
    ];

    protected $message = [
        'id'        => 'ID丢失',
        'sort'      => '排序序号必须填写',
        'name'        => '抢购名称必须填写',
        'start_time'  => '抢购开始时间必须填写',
        'end_time'    => '抢购结束时间必须填写',
    ];

    protected $scene = [
        'sort'  => ['id','sort'],
        'save'  => ['name','start_time','end_time'],
    ];
}