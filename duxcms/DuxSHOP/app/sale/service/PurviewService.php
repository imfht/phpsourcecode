<?php
namespace app\sale\service;
/**
 * 权限接口
 */
class PurviewService {
    /**
     * 获取模块权限
     */
    public function getSystemPurview() {
        return array(
            'Config' => array(
                'name' => '推广设置',
                'auth' => array(
                    'index' => '设置',
                )
            ),
            'ConfigNotice' => array(
                'name' => '通知设置',
                'auth' => array(
                    'index' => '设置',
                )
            ),

            'User' => array(
                'name' => '推广用户',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'UserApply' => array(
                'name' => '用户审核',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'UserLevel' => array(
                'name' => '推广等级',
                'auth' => array(
                    'index' => '列表',
                )
            ),

            'Order' => array(
                'name' => '订单管理',
                'auth' => array(
                    'index' => '列表',
                )
            ),

            'Cash' => array(
                'name' => '提现管理',
                'auth' => array(
                    'index' => '列表',
                )
            ),

        );
    }


}
