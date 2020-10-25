<?php

namespace app\admin\validate;

use think\Validate;

/**
 * Description of Document
 * 文章验证器
 * @author static7 <static7@qq.com>
 */
class Document extends Validate {

    protected $rule = [
        'title' => "require|max:80",
        'name' => "alphaDash|unique:document,name",
        'description' => 'max:200',
        'category_id' => 'require|checkCategory',
        'type' => 'number',
        'content' => "require",
    ];
    protected $message = [
        'title.require' => '标题不能为空',
        'title.max' => '标题不能超过80个字符',
        'name.unique' => '标识已经存在',
        'name.alphaDash' => '标识只能为字母和数字，下划线_及破折号-',
        'description.max' => '描述不能超过200个字符',
        'category_id.require' => '分类不能为空',
        'type.number' => '内容类型不正确',
        'content.require' => '内容不能为空',
    ];

}
