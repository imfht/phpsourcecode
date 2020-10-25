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
 * 友链-验证类
 * @author 牧羊人
 * @date 2019/4/29
 * Class Link
 * @package app\admin\validate
 */
class Link extends BaseValidate
{
    // 验证规则
    protected $rule = [
        'name|友链名称' => 'require|length:1,20',
        'sort|排序' => 'number',
    ];

    // 验证提示语
    protected $message = [
        'name.require' => '友链名称不能为空',
        'name.length' => '友链名称长度介于1~20个字符',
        'sort.number' => '排序必须是数组',
    ];

    // 验证场景
    protected $scene = [
        'edit' => 'name,sort',
    ];
}
