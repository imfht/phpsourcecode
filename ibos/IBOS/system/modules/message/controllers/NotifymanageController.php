<?php
/**
 * 提醒管理控制器
 * User: liuzimu
 * Date: 2017/10/21
 * Time: 9:29
 */
namespace application\modules\message\controllers;

class NotifymanageController extends BaseController
{
    /**
     * 该控制器的action
     * @return array
     */
    public function actions()
    {
        $actions = array(
            'index' => 'application\modules\message\actions\notifymanage\Index', // 列表
        );
        return $actions;
    }
}