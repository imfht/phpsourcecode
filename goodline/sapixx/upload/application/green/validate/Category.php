<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品分类
 */
namespace app\green\validate;
use think\Validate;

class Category extends Validate{

    protected $rule = [
        'token'     => 'require|max: 25|token',
        'id'        => 'require|number',
        'parent_id' => 'require|number',
        'sort'      => 'require|number',
        'title'     => 'require',
        'name'      => 'require',
    ];
    protected $message = [
        'id'        => '未找到栏目资源',
        'parent_id' => '上级栏目不存在',
        'title'     => '分类名称必须填写',
        'name'      => '分类别名必须填写',
        'sort'      => '排序序号必须填写',
    ];

    protected $scene = [
        'sort' => ['id','sort'],
        'add'  => ['parent_id','sort','title'],
        'edit' => ['id','sort','title'],
        'news' => ['sort','title','name'],
    ];
}