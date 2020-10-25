<?php

namespace Admin\Controller;

use Think\Controller;

class RbacController extends CommonController {

    //用户列表
    public function index() {
        $this->title = '用户列表';
        $User = D("UserRelation");
        $this->user = $User->relation(true)->select();
        $this->display();
    }

    //角色列表
    public function role() {
        $this->title = '角色列表';
        $res = M('role')->select();
        $this->assign('res', $res);
        $this->display();
    }

    //节点列表
    public function node() {
        $this->title = '节点列表';
        $field = array('id', 'name', 'title', 'pid');
        $res = M('node')->field($field)->order('sort')->select();
        $res = node_merge($res); //p($res);die;
        $this->assign('res', $res);
        $this->display();
    }

    //添加用户
    public function addUser() {
        $this->title = '添加用户';
        $this->role = M('role')->select();
        $this->display();
    }

    //添加用户处理
    public function addUserHandle() {
        $user = array(
            'user' => trim(I('user')),
            'pwd' => trim(I('pwd'))
        );
        if (!clmao_ctype_alnum($user)) {
            $this->error('帐户和密码都只能输入数字和字母', U('Admin/Rbac/addUser'), 2);
        }
        $is_diff = M('user')->field('id')->where(array('user' => $user['user']))->find();
        if (!empty($is_diff)) {
            $this->error('该用户已经存在', U('Admin/Rbac/addUser'), 2);
        }
        $user['pwd'] = clmao_md5_half($user['pwd']);
        $user['identifier'] = clmao_md5_half($user['user']);
        if ($uid = M('user')->add($user)) {
            foreach ($_POST['role_id'] as $v) {
                $role[] = array(
                    'role_id' => $v,
                    'user_id' => $uid
                );
                M('role_user')->addAll($role);
                $this->success('添加成功', U('Admin/Rbac/addUser'));
            }
        }
    }

    //添加角色
    public function addRole() {
        $this->title = '添加角色';
        $this->display();
    }

    //添加角色表单处理
    public function addRoleHandle() {
        if (M('role')->add($_POST)) {
            $this->success('添加成功', U('/Admin/Rbac/addRole'));
        }
    }

    //添加节点
    public function addNode() {
        $this->title = '添加节点';
        $this->pid = I('pid', 0, 'intval');
        $this->level = I('level', 1, 'intval');

        switch ($this->level) {
            case 1:
                $this->type = '应用';
                break;
            case 2:
                $this->type = '控制器';
                break;
            case 3:
                $this->type = '动作方法';
                break;
        }

        $this->display();
    }

    //添加节点的表单处理
    public function addNodeHandle() {
        if (M('node')->add($_POST)) {
            $this->success('添加成功', U('/Admin/Rbac/node'));
        }
    }

    //配置权限
    public function access() {
        $this->title = '权限配置';
        $rid = I('rid', 0, 'intval');
        $field = array('id', 'name', 'title', 'pid');
        $res = M('node')->field($field)->order('sort')->select();
        $access = M('access')->where(array('role_id' => $rid))->getField('node_id', true);
        $this->res = node_merge($res, $access);
        $this->rid = $rid;

        //原有权限

        $this->display();
    }

    //权限保存表单
    public function setAccess() {
        $rid = I('rid', 0, 'intval');
        $db = M('access');
        $db->where(array('role_id' => $rid))->delete();
        $data1 = array();

        foreach (I('access') as $k => $v) {
            $tmp = explode('_', $v);

            $data1[] = array(
                'role_id' => $rid,
                'node_id' => $tmp[0],
                'level' => $tmp[1]
            );
            if ($tmp[1] == 2) {
                $data2 = M('node')->where('pid=' . $tmp[0])->getField('id', true);
                foreach ($data2 as $key => $value) {
                    $data1[] = array(
                        'role_id' => $rid,
                        'node_id' => $value,
                        'level' => 3
                    );
                }
            }
        }


        if ($db->addAll($data1)) {
            $this->success('修改成功', U('Admin/Rbac/role'));
        }
    }

}
