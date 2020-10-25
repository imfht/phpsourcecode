<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 公众号菜单管理
 */
namespace app\system\validate;
use think\Validate;

class Official extends Validate{

    protected $rule = [
        'id'        => 'require|number',
        'parent_id' => 'require|number',
        'sort'      => 'require|number',
        'name'      => 'require',
        'types'     => 'require',
    ];

    protected $message = [
        'id'    => 'ID资源',
        'sort'  => '排序序号必须填写',
        'name'  => '菜单名称必须填写',
        'types' => '链接类型必须填写',
    ];

    protected $scene = [
        'sort' => ['id','sort'],
        'add'  => ['parent_id','name','types'],
        'edit' => ['id','name','types'],
    ];
}