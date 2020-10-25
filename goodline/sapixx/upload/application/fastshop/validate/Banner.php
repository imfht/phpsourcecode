<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 首页广告管理
 */
namespace app\fastshop\validate;
use think\Validate;

class Banner extends Validate{

    protected $rule = [
        'id'       => 'require|number',
        'picture'  => 'require',
        'sort'     => 'require|number',
        'title'    => 'require',
        'link'     => 'require',
        'group_id' => 'require',
    ];

    protected $message = [
        'id'       => '{%id_error}',
        'sort'     => '排序序号必须填写',
        'picture'  => '图片必须选择',
        'title'    => '标题必须填写',
        'group_id' => '广告位必须选择',
        'link'     => '链接地址必须填写',
    ];

    protected $scene = [
        'sort' => ['id','sort'],
        'add'  => ['title','group_id','picture','link'],
        'edit' => ['id','group_id','title','picture','link'],
    ];
}