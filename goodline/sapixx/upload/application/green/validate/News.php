<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 新闻头条
 */
namespace app\green\validate;
use think\Validate;

class News extends Validate{

    protected $rule = [
        'types'   => 'require|number',
        'cate_id' => 'require',
        'title'   => 'require',
        'content' => 'require',
    ];

    protected $message = [
        'cate_id' => '栏目必须选择',
        'types'   => '必须选择内容类型',
        'title'   => '标题必须选择',
        'content' => '内容必须输入',
    ];

    protected $scene = [
        'save'    => ['cate_id','types','title','content'],
        'article' => ['types','title','content'],
    ];
}