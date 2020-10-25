<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 广告管理
 */
namespace app\green\validate;
use think\Validate;

class Adwords extends Validate{

    protected $rule = [
        'id'         => 'require|number',
        'operate_id' => 'require|number|>:0',
        'picture'    => 'require',
        'sort'       => 'require|number',
        'title'      => 'require',
        'link'       => 'require',
        'open_type'  => 'require',
        'group'      => 'require',
        'api'        => 'require|alphaNum',
    ];

    protected $message = [
        'id'         => '编辑资源不存在',
        'operate_id' => '运营商必须选择',
        'sort'       => '排序序号必须填写',
        'picture'    => '图片必须选择',
        'title'      => '标题必须填写',
        'group'      => '广告位必须选择',
        'link'       => '链接地址必须填写',
        'api'        => 'API名称必须填写,切必须是数字或字母',
    ];

    protected $scene = [
        'sort' => ['id', 'sort'],
        'api'  => ['api'],
        'add'  => ['title', 'group_id', 'picture', 'link', 'open_type'],
        'edit' => ['id', 'group_id', 'title', 'picture', 'link', 'open_type'],
    ];
}