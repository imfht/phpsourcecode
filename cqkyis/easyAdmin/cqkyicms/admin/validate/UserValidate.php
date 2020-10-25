<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/11 16:12
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\validate;


use think\Validate;

class UserValidate extends Validate
{


    protected $rule=[

        'username' =>  'require',
        'password'=>'require',
        '__token__' => 'token',
        'role_id'=>'require',

    ];
    protected $message  =   [
        'username.require' => '用户名不能为空',
        'password.require' => '密码不能为空',
        'role_id.require'=>'角色不能为空'
    ];

    protected $scene = [
        'edit'  =>  ['rules'],
    ];

}