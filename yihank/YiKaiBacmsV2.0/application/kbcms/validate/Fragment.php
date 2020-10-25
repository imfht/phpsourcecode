<?php

namespace app\kbcms\validate;
use think\Validate;
/**
 *  模型属性验证
 */
class Fragment extends Validate{
    // 验证规则
    protected $rule = [
        ['name', 'require|max:25', '碎片名称不能为空|碎片名称不能超过25个字符'],
        ['label', 'require|max:25', '碎片标识不能为空|碎片标识不能超过25个字符'],
        ['content', 'require', '碎片内容不能为空'],
    ];

    protected $scene = array(
        'add'     => 'name,content',//新增数据时验证
        'edit'     => 'name,content',//修改数据时验证
    );
}