<?php
namespace app\common\validate;
//应用验证器
use think\Validate;

class Apiapp extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'app_name|应用名称' => 'require',
        'app_id|应用app_id' => 'require',
        'app_secret|应用app_secret' => 'require',
        'app_status|状态' => 'require|in:0,1',
        'app_limitTime|有效时间' => 'require|number'
    ];

    protected $scene = [
        'add'   => ['app_name','app_limitTime','id'],
        'edit'  => ['app_name','app_limitTime','id'],
        'app_name' => ['app_name','id'],
        'app_status' => ['app_status','id'],
        'app_limitTime' => ['app_limitTime','id'],
    ];
}