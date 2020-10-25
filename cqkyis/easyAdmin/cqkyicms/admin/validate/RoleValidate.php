<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/9 16:33
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\validate;


use think\Validate;



class RoleValidate extends Validate
{

    protected $rule=[

        'role_name' =>  'require',
        'rules'=>'require',
        '__token__' => 'token',

    ];
    protected $message  =   [
        'role_name.require' => '角色名称不能为空',
        'rules.require' => '角色权限不能为空',
 ];

    protected $scene = [
        'edit'  =>  ['rules'],
    ];

}