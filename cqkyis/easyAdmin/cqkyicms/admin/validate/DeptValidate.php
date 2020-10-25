<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/10 12:38
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\validate;


use think\Validate;

class DeptValidate extends Validate
{

    protected $name="system_dept";

    /**
     *
     */
    protected $rule=[

        'dept_name' =>  'require',
        'parent_id'=>'require',
        '__token__' => 'token',

    ];
    protected $message  =   [
        'dept_name.require' => '部门名称不能为空',
        'parent_id.require' => '上级部门不能为空',
    ];

    protected $scene = [
        'edit'  =>  ['dept_name'],
    ];
}