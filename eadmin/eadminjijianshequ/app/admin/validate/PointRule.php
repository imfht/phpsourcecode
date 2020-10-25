<?php

namespace app\admin\validate;

use app\common\logic\Common as LogicCommon;

/**
 * 验证器
 */
class PointRule extends AdminBase
{

    // 验证规则
    protected $rule = [

        'controller' => 'require|checkUnique',

    ];

    // 验证提示
    protected $message = [

        'controller.require'     => '动作不能为空',
        'controller.checkUnique' => '该规则已存在',


    ];

    // 自定义验证规则
    protected function checkUnique($value, $rule, $data)
    {
        $commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'PointRule');
        if (!empty($data['id'])) {

            $count = $commonLogic->getStat(['controller' => $data["controller"], 'scoretype' => $data["scoretype"], 'id|!' => $data['id']]);

            if ($count > 0) {
                return false;
            } else {
                return true;
            }

        } else {

            $count = $commonLogic->getStat(['controller' => $data["controller"], 'scoretype' => $data["scoretype"]]);

            if ($count > 0) {
                return false;
            } else {
                return true;
            }
        }


    }

    // 应用场景
    protected $scene = [
        'edit' => ['controller'],
        'add'  => ['controller'],
    ];

}
