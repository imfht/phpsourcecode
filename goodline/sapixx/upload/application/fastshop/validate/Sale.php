<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 活动管理
 */
namespace app\fastshop\validate;
use think\Validate;

class Sale extends Validate{

    protected $rule = [
        'types'        => 'require|number|between: 0,2',
        'title'        => 'require',
        'category_id'  => 'require|number',
        'sale_nums'    => 'require|number',
        'item_id'      => 'require|number',
        'cost_price'   => 'require|float',
        'market_price' => 'require|float',
        'sale_price'   => 'require|float',
        'gift'         => 'require|array',
        'start_time'   => 'require|date',
        'end_time'     => 'require|date',
        'img'          => 'require',
    ];

    protected $message = [
        'types'                => '活动状态必须选择',
        'category_id'          => '抢购时间段必须选择',
        'title'                => '活动名称必须输入',
        'sale_nums'            => '产品数量必须输入',
        'item_id'              => '商品名称必须选择',
        'cost_price.require'   => '成本价不能为空',
        'cost_price.float'     => '成本价只能填写数字',
        'market_price.require' => '市场价不能为空',
        'market_price.float'   => '市场价只能填写数字',
        'sale_price.require'   => '销售价不能为空',
        'sale_price.float'     => '销售价只能填写数字',
        'gift'                 => '赠送产品必须填写',
        'start_time'           => '活动开始时间必须填写,且格式必须正确',
        'end_time'             => '活动结束时间必须填写,且格式必须正确',
        'img'                  => '没有设置默认图片',
    ];

    protected $scene = [
        'save'  => ['category_id','types','title','sale_nums','item_id','cost_price','sale_price','market_price','gift','start_time','end_time'],
    ];
}