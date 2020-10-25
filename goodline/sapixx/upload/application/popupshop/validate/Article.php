<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 内容
 */
namespace app\popupshop\validate;
use think\Validate;

class Article extends Validate{

    protected $rule = [
        'types'  => 'require|number',
        'title'   => 'require',
        'content' => 'require',
    ];

    protected $message = [
        'types'  => '必须选择内容类型',
        'title'   => '标题必须选择',
        'content' => '内容必须输入',
    ];

    protected $scene = [
        'save'  => ['types','title','content'],
    ];
}