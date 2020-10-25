<?php

namespace app\page\validate;
use think\Validate;
/**
 *  模型属性验证
 */
class CategoryPage extends Validate{
    // 验证规则
    protected $rule = [
        ['name', 'require|max:25', '页面名称不能为空|页面名称不能超过25个字符'],
        ['content', 'require', '页面内容不能为空'],
    ];

    protected $scene = array(
        'add'     => 'name,content',//新增数据时验证
        'edit'     => 'name,content',//修改数据时验证
    );
}