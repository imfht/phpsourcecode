<?php
namespace app\common\validate;
//接口验证器
use think\Validate;

class Apilist extends Validate
{
    protected $rule = [
        'apiName|接口名称' => 'require',
        'status|状态' => 'in:0,1',
        'method|请求方式' => 'require|in:0,1,2',
        'accessToken' => 'require|in:0,1',
        'needLogin|验证登录' => 'require|in:0,1',
        'isTest|测试模式' => 'require|in:0,1',
    ];

    protected $scene = [
        'add'   => ['apiName','status','method','accessToken','needLogin','isTest'],
        'edit'  => ['apiName','status','method','accessToken','needLogin','isTest'],
        'apiName' => ['apiName'],
        'status' => ['status'],
        'info' => ['info'],
    ];
}