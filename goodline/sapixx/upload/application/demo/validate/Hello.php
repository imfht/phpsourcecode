<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 演示验证
 */
namespace app\shop\validate;
use think\Validate;

class Adwords extends Validate{

    protected $rule = [
        'id' => 'require',
    ];

    protected $message = [
        'id' => 'ID不存在',
    ];

    protected $scene = [
        'edit' => ['id'],
    ];
}