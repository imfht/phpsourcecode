<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/8/29
// +----------------------------------------------------------------------
// | Title:  Customers.php
// +----------------------------------------------------------------------
namespace app\index\validate;

use think\Validate;

class Customers extends Validate
{
    protected $rule = [
        'name|企业名称' => 'require',
        'duty|责任人' => 'require',
        'type|企业类型' => 'require',
        'property|企业性质' => 'require',
        'phome|座机' => 'require|alphaDash',
        'fax|传真' => 'require|alphaDash',
        'email|邮箱' => 'require|email',
        'evaluate|评估等级' => 'require',
        'prov|省份' => 'require',
        'city|城市' => 'require',
        '__token__|数据'    =>  'require|token'
    ];

    protected $message = [
        'name.require' => ':attribute不能为空',
        'duty.require' => ':attribute不能为空',
        'type.require' => ':attribute不能为空',
        'property.require' => ':attribute不能为空',
        'phome.require' => ':attribute不能为空',
        'phome.alphaDash' => ':attribute格式不对',
        'fax.require' => ':attribute不能为空',
        'fax.alphaDash' => ':attribute格式不对',
        'email.require' => ':attribute不能为空',
        'email.email' => ':attribute格式不对',
        'evaluate|评估等级' => ':attribute不能为空',
        'prov|省份' => 'require',
        'city|城市' => 'require',
        '__token__.require' => ':attribute不能为空',
        '__token__.token' => ':attribute无效'
    ];

    protected $scene = [
        //'add'      => ['name','duty','type','property','phome','fax','email','evaluate','prov','city','__token__'],
        //'edit'     => ['type','property','phome','fax','email','evaluate','prov','city','__token__']
        'add'      => [],
        'edit'     => []
    ];
}