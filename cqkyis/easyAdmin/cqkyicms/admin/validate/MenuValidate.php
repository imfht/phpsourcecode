<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/7 16:48
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\validate;


use think\Validate;

class MenuValidate extends Validate
{

    protected $rule=[
        'menu_name'  =>  'require',
        'menu_role' =>  'require',
        'type'=>'require',
        '__token__' => 'token',

    ];
    protected $message  =   [
        'menu_name.require' => '菜单名称不能为空',
      
        'type.require'   => '属性不能为空',
        'menu_role.require'=>'权限标识不能为空'


    ];

}