<?php
declare (strict_types = 1);

namespace app\home\validate;

use think\Validate;

class Into extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'guest_name'  =>  'require',
        'credentials' =>  'require',
        'guest_number' =>  'require',
        'move_duration'  =>  'require',
        'move_time' =>  'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'guest_name.require' => '客户名称不能为空',
        'credentials.require' => '证件号码不能为空',
        'guest_number.require' => '入住人数不能为空',
        'move_duration.require' => '入住时间不能为空',
        'move_time.require' => '离店时间不能为空',
    ];
}
