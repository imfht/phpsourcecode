<?php
namespace app\common\validate;

use think\Validate;


class Field extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|字段变量名'   => 'require|regex:^[a-z]\w{0,39}$',
        'title|字段标题'  => 'require|length:2,50',
        'type|字段表单类型'   => 'require|length:2,30',
        'field_type|字段数据库类型' => 'require|length:10,100',
    ];

    //定义验证提示
    protected $message = [
        'name.regex' => '字段变量名只能由小写字母和下划线组成',
    ];

}
