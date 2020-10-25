<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品管理
 */
namespace app\green\validate;
use think\Validate;

class Shop extends Validate{

    protected $rule = [
        'name'             => 'require',
        'points'           => 'require|number',
        'img'              => 'require',
        'imgs'             => 'require|array',
        'content'          => 'require',
    ];

    protected $message = [
        'name'                 => '商品名称必须输入',
        'points'               => '需要积分必须输入',
        'img'                  => '没有设置默认图片',
        'imgs'                 => '没有设置商品图片',
        'content'              => '商品描述必须填写',
    ];

    protected $scene = [
        'save'  => ['name','img','imgs','points','content'],
    ];
}