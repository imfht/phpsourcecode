<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品管理
 */
namespace app\fastshop\validate;
use think\Validate;

class Item extends Validate{

    protected $rule = [
        'name'             => 'require',
        'category_id'      => 'require|number',
        'category_path_id' => 'require',
        'img'              => 'require',
        'imgs'             => 'require|array',
        'price'            => 'require|float',
        'sell_price'       => 'require|float',
        'market_price'     => 'require|float',
        'cost_price'       => 'require|float',
        'weight'           => 'require|number',
        'content'          => 'require',
    ];

    protected $message = [
        'category_id'          => '商品分类必须输入',
        'category_path_id'     => '商品分类必须输入',
        'name'                 => '商品名称必须输入',
        'img'                  => '没有设置默认图片',
        'imgs'                 => '没有设置商品图片',
        'price.require'        => '成本价不能为空',
        'price.number'         => '成本价只能填写数字',
        'sell_price'           => '销售价不能为空',
        'sell_price.number'    => '销售价只能填写数字',
        'market_price.require' => '市场价不能为空入',
        'market_price.number'  => '市场价只能填写数字',
        'cost_price.require'   => '成本价不能为空',
        'cost_price.number'    => '成本价只能填写数字',
        'content'              => '商品描述必须填写',
    ];

    protected $scene = [
        'save'  => ['category_id','category_path_id','name','img','imgs','price','sell_price','market_price','cost_price','weight','content'],
    ];
}