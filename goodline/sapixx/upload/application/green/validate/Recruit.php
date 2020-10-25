<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 栏目管理
 */
namespace app\green\validate;
use think\Validate;
class Recruit extends Validate{

    protected $rule = [
        'title'   => 'require',
        'name'    => 'require',
        'state'   => 'require|number',
        'sort'    => 'require|number',
        'news_id' => 'require',
    ];
    protected $message = [
        'title'   => '名称必须填写',
        'name'    => '介绍必须填写',
        'state'   => '状态必须选择',
        'news_id' => '文章必须选择',
        'sort'    => '排序序号必须填写',
    ];

    protected $scene = [
        'edit' => ['sort','title','name','state','news_id'],
    ];
}