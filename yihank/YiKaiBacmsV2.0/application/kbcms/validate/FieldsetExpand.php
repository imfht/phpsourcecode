<?php

namespace app\kbcms\validate;
use think\Validate;
/**
 *  模型属性验证
 */
class FieldsetExpand extends Validate{
    // 验证规则
    protected $rule = [
        ['name', 'require|max:25', '模型名称不能为空|模型名称不能超过25个字符'],
        ['table', 'unique:fieldset|require|max:25', '已存在相同的数据表|表名不能为空|表名不能超过25个字符'],
    ];

    protected $scene = array(
        'add'     => 'name',//新增数据时验证
        'edit'     => 'name',//修改数据时验证
    );
}