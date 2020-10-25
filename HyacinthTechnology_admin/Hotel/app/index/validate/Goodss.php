<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Goodss extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name'  =>  'require',
        'price'  =>  'require|integer',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'name.require' => '商品不能为空',
//        'name.unique' => '商品已存在',
        'price.require' => '商品价格不能为空',
        'price.integer' => '商品价格格式不对',
    ];
}
