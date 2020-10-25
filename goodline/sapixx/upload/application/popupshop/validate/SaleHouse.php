<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 活动管理
 */
namespace app\popupshop\validate;
use think\Validate;

class SaleHouse extends Validate{

    protected $rule = [
        'name'             => 'require',
        'title'             => 'require',
        'note'             => 'require',
        'category_id'      => 'require|number',
        'img'              => 'require',
        'imgs'             => 'require|array',
        'sell_price'       => 'require|float',
        'cost_price'       => 'require|float',
        'weight'           => 'require|number',
        'content'          => 'require',
    ];

    protected $message = [
        'category_id'          => '商品分类必须输入',
        'title'                => '活动标题必须填写',
        'name'                 => '商品名称必须输入',
        'note'                 => '商品推荐语必须输入',
        'img'                  => '没有设置默认图片',
        'imgs'                 => '没有设置商品图片',
        'sell_price'           => '销售价不能为空',
        'sell_price.number'    => '销售价只能填写数字',
        'cost_price.require'   => '成本价不能为空',
        'cost_price.number'    => '成本价只能填写数字',
        'content'              => '商品描述必须填写',
    ];

    protected $scene = [
        'save'  => ['category_id','name','title','note','img','imgs','sell_price','cost_price','content'],
    ];
}