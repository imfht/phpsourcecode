<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品分类
 */
namespace app\green\validate;
use think\Validate;

class Staff extends Validate{

    protected $rule = [
        'id'         => 'require|number',
        'uid'        => 'require|number',
        'title'      => 'require',
        'about'      => 'require',
        'operate_id' => 'require',
        'sort'       => 'require',
    ];

    protected $message = [
        'id'         => 'ID丢失',
        'uid'        => '必须选择绑定UID',
        'title'      => '姓名必须填写',
        'about'      => '特长必须填写',
        'operate_id' => '运营商必须选择',
        'sort'       => '序号必须填写',
    ];

    protected $scene = [
        'edit'  => ['uid','title','operate_id','about'],
        'sort'  => ['id', 'sort'],
    ];
}