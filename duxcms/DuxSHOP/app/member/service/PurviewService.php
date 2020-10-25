<?php
namespace app\member\service;
/**
 * 权限接口
 */
class PurviewService {
    /**
     * 获取模块权限
     */
    public function getSystemPurview() {
        return array(
            'MemberUser' => array(
                'name' => '用户管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ), 
			'MemberGrade' => array(
                'name' => '会员等级',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'MemberReal' => array(
                'name' => '实名制管理',
                'auth' => array(
                    'index' => '列表',
                    'check' => '审核',
                )
            ),
            'MemberRole' => array(
                'name' => '角色管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'PayAccount' => array(
                'name' => '资金账户',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'PayLog' => array(
                'name' => '收支记录',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'PayRecharge' => array(
                'name' => '收支记录',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'PayCash' => array(
                'name' => '提现记录',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'PayCard' => array(
                'name' => '银行卡管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'PayConf' => array(
                'name' => '支付设置',
                'auth' => array(
                    'index' => '列表',
                    'setting' => '配置',
                )
            ),
            'PayBank' => array(
                'name' => '银行管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'PointsAccount' => array(
                'name' => '积分账户',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'PointsLog' => array(
                'name' => '积分记录',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'MemberConfig' => array(
                'name' => '会员设置',
                'auth' => array(
                    'index' => '基本设置',
                    'reg' => '注册设置',
                )
            ),
            'MemberConfigManage' => array(
                'name' => '配置管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'MemberVerify' => array(
                'name' => '验证码管理',
                'auth' => array(
                    'index' => '列表',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),

        );
    }


}
