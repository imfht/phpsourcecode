<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 运费验证
 */
namespace app\fastshop\validate;
use think\Validate;

class Fare extends Validate{

    protected $rule = [
        'first_weight'  => 'require|number',
        'first_price'   => 'require|number',
        'second_weight' => 'require|number',
        'second_price'  => 'require|number',
    ];

    protected $message = [
        'first_weight'  => '初始重量必须填写',
        'first_price'   => '初始运费必须填写',
        'second_weight' => '每增加重量必须填写',
        'second_price'  => '增加运费必须填写',
    ];

    protected $scene = [
        'save'  => ['first_weight','first_price','second_weight','second_price'],
    ];
}