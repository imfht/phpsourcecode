<?php

/**
 * 审核管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class MemberRealAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MemberReal';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '实名制审核',
                'description' => '审核用户实名制申请',
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
            'username' => 'username',
            'status' => 'status',
        ];
    }

    public function _indexWhere($where) {
        if ($where['username']) {
            $where['_sql'] = "B.nickname = '{$where['username']}' OR B.tel= '{$where['username']}' OR B.email= '{$where['username']}' ";
            unset($where['username']);
        }
        switch ($where['status']) {
            case 1:
                $where['status'] = 0;
                break;
            case 2:
                $where['status'] = 1;
                break;
            case 3:
                $where['status'] = 2;
                break;
        }
        return $where;
    }

    public function _indexOrder() {
        return 'A.time desc, A.real_id desc';
    }


    public function check() {
        $id = request('', 'id');
        if (empty($id)) {
            if (isAjax()) {
                $this->error('ID不能为空！');
            } else {
                $this->systemDialogError('ID不能为空！');
            }
        }
        $info = target($this->_model)->getInfo($id);
        if ($info['status'] == 2) {
            if (isAjax()) {
                $this->error('该用户已进行审核！');
            } else {
                $this->systemDialogError('该用户已进行审核！');
            }
        }
        if (!isPost()) {
            $this->assign('info', $info);
            $this->dialogDisplay();
        } else {
            $status = $_POST['status'];
            $data = [
                'real_id' => $id,
                'status' => $status,
                'remark' => $_POST['remark'],
                'auth_admin' => $this->userInfo['user_id'],
                'auth_time' => time()
            ];
            target($this->_model)->beginTransaction();
            if (!target($this->_model)->edit($data)) {
                $this->error('用户审核失败！');
            }
            target($this->_model)->commit();
            $this->success('用户审核成功！');

        }

    }

}