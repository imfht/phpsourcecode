<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 自动回复关键字
 */
namespace app\system\validate;
use think\Validate;

class Keyword extends Validate{

    protected $rule = [
        'keyword'  => 'require',
        'content'  => 'require',
        'url'      => 'require',
        'title'    => 'require',
        'image'    => 'require',
        'media_id'    => 'require',
        
    ];

    protected $message = [
        'keyword'  => '关键字必须添加',
        'content'  => '应答文本必须填写',
        'url'      => '链接必须填写',
        'title'    => '标题必须必须填写',
        'image'    => '图片必须选择',
        'media_id' => 'media_id信息必须填写'
    ];

    protected $scene = [
        'text'  => ['keyword','content'],
        'image' => ['keyword','image'],
        'link'  => ['keyword','title','image','url','content'],
        'media' => ['keyword','media_id'],
    ];
}