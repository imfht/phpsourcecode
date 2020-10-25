<?php

/**
 * 角色管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class MemberRoleAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MemberRole';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '角色管理',
                'description' => '管理会员角色信息',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name'
        ];
    }

    protected function _delBefore($id) {
        if ($id == 1) {
            $this->error('保留角色无法删除！');
        }
        $countUser = target('system/SystemUser')->query("SELECT COUNT(*) FROM {pre}member_user WHERE FIND_IN_SET({$id}, role_id); ");
        $countUser = $countUser[0];
        if ($countUser > 0) {
            $this->error('请先删除该角色下的用户！');
        }
    }

}