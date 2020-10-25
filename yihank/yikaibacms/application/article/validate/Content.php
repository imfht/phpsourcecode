<?php

namespace app\article\validate;
use think\Validate;
/**
 *  模型属性验证
 */
class Content extends Validate{
    // 验证规则
    protected $rule = [
        ['class_id', 'require', '文章栏目不能为空'],
        ['title', 'require|max:80', '文章标题不能为空|文章标题不能超过80个字符'],
        ['content', 'require', '文章内容不能为空'],
    ];

    protected $scene = array(
        'add'     => 'name',//新增数据时验证
        'edit'     => 'name',//修改数据时验证
    );
}