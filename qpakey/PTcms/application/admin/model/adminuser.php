<?php

class AdminUserModel extends PT_Model {


    protected $table = 'admin_user';

    /**
     * 插入数据
     *
     * @param $param
     * @return mixed
     */
    public function add($param) {
        $param['create_user_id'] = $_SESSION['admin']['userid'];
        $param['create_time']    = NOW_TIME;
        return $this->insert($param);
    }

    /**
     * 修改
     *
     * @param $param
     * @return mixed
     */
    public function edit($param) {
        $param['update_user_id'] = $_SESSION['admin']['userid'];
        $param['update_time']    = NOW_TIME;
        return $this->update($param);
    }

    /**
     * 删除数据
     *
     * @param $where
     * @return mixed
     */
    public function del($where) {
        return $this->where($where)->delete();
    }


    // 检查用户是否具有可登录状态
    public function checkUserStatus($userid) {
        return $this->where(array('user_id' => $userid))->getfield('status');
    }

    public function login() {
        $username = $this->input->post('username', 'str', '');
        if(!$username) return '请输入登陆账号';
        $password = $this->input->post('password', 'str', '');
        if(!$username) return '请输入登陆密码';
        $verify   = $this->input->post('verifycode', 'str', '');
        if ($verify === $this->session->get('verify')) {
            if ($userid = $this->model('user')->checkInfo($username, $password)) {
                if ($this->checkUserStatus($userid)) {
                    $this->setLoginStatus($userid);
                    return true;
                } else {
                    return '您没有权限进入后台！';
                }
            } else {
                return '帐号和密码输入错误';
            }
        } else {
            return '验证码输入错误';
        }
    }

    // 设置用户登录状态
    public function setLoginStatus($userid) {
        //设置登录信息
        $this->session->admin = array(
            'userid'    => $userid,
            'username'  => $this->db('user')->where(array('id' => $userid))->getField('name'),
            'groupid'   => $this->db('admin_user')->where(array('user_id' => $userid))->getField('group_id'),
            'groupname' => $this->model->get('admin_group', $userid, 'name'),
        );
        // 更新通行证登录时间
        $this->db('user')->where(array('id' => $userid))->update(array(
            'login_ip'   => $this->request->getIp(),
            'login_time' => NOW_TIME
        ));
        // 更新后台表信息
        $data['login_num'] = array('exp', '`login_num`+1');
        $this->db('admin_user')->where(array('user_id' => $userid))->update($data);
    }

    // 删除用户登录信息
    public function delLoginStatus() {
        $this->session->rm('admin');
    }

    // 获取列表
    public function getlist() {
        $list = $this->db('admin_user')->select();
        foreach ($list as &$v) {
            $v['username']        = $this->model->get('user', $v['user_id'], 'name');
            $v['groupname']       = $this->model->get('admin_group', $v['group_id'], 'name');
            $v['create_username'] = $this->model->get('user', $v['create_user_id'], 'name');
            $v['update_username'] = $this->model->get('user', $v['update_user_id'], 'name');
            $v['url_edit']        = U('admin.user.edit', array('id' => $v['id']));
            $v['create_time']     = $v['create_time'] ? date('Y-m-d H:i', $v['create_time']) : '';
            $v['update_time']     = $v['update_time'] ? date('Y-m-d H:i', $v['update_time']) : '';
            $v['login_time']      = $v['login_time'] ? date('Y-m-d H:i', $v['login_time']) : '';
        }
        return $list;
    }
}