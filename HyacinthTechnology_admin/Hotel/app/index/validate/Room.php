<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Room extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'room_num'  =>  'require|unique:room',
        'room_name'  =>  'require|unique:room',
        'bed'  =>  'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'room_num.require' => '房间号码不能为空',
        'room_num.unique' => '房间号码已存在',
        'room_name.require' => '房间名称不能为空',
        'room_name.unique' => '房间名称已存在',
        'bed.require' => '酒店床位不能为空',
    ];
}
