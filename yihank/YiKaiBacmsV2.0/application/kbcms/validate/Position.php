<?php

namespace app\kbcms\validate;
use think\Validate;
/**
 *  模型属性验证
 */
class Position extends Validate{
    // 验证规则
    protected $rule = [
        ['name', 'require|max:25', '推荐位名称不能为空|推荐位名称不能超过25个字符'],
        ['sequence', 'require|max:25', '推荐位顺序不能为空|推荐位顺序不能超过25个字符'],
    ];

    protected $scene = array(
        'add'     => 'name',//新增数据时验证
        'edit'     => 'name',//修改数据时验证
    );
}