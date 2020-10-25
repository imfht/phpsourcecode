<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\validate;

use app\common\validate\BaseValidate;

/**
 * 角色-验证类
 * @author 牧羊人
 * @date 2019/4/22
 * Class AdminRole
 * @package app\admin\validate
 */
class Role extends BaseValidate
{

    // 验证规则
    protected $rule = [
        'name|角色名称' => 'require|length:1,30',
        'sort|排序' => 'number',
    ];

    // 验证提示语
    protected $message = [
        'name.require' => '角色名称不能为空',
        'name.length' => '角色名称介于1~30个字符',
        'sort.number' => '排序必须为数字',
    ];

    // 验证场景
    protected $scene = [
        'edit' => 'name,sort',
    ];

}
