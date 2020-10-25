<?php
/**
 * Created by PhpStorm.
 * User: YC
 * Date: 2017/4/30
 * Time: 13:51
 */

namespace app\admin\validate;

use think\Validate;

class AuthRule extends Validate
{
    protected $rule = [
        'title|标题'  => 'require|max:50',
        'name|规则名称' => 'require|length:0,100',
        'sort|排序'   => 'number',
        'is_menu'   => 'accepted',
        'remark|描述' => 'max:255'
    ];

}