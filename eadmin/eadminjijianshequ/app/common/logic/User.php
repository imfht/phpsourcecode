<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\logic;

/**
 * 会员逻辑
 */
class User extends Common
{

    // 会员模型
    public static $memberModel = null;


    // 初始化
    protected function _initialize()
    {

    }

    /**
     * 获取会员信息
     */
    public function getMemberInfo($where = [], $field = true)
    {


        return $this->getDataInfo($where, $field);
    }

    /**
     * 获取会员列表
     */
    public function getMemberList($where = [], $field = true, $order = '')
    {


        return $this->getDataList($where, $field, $order, 0, '', '', '', true);
    }

    /**
     * 获取会员列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];

        if (!empty($data['status']) && $data['status'] != 'all') {

            $where['status'] = $data['status'];
        }


        !empty($data['search_data']) && $where['OR'] = ['nickname|~' => '%' . $data['search_data'] . '%', 'username|~' => '%' . $data['search_data'] . '%', 'usermail|~' => '%' . $data['search_data'] . '%', 'mobile|~' => '%' . $data['search_data'] . '%'];

        if (!is_administrator()) {

        }

        return $where;
    }

    /**
     * 获取存在继承关系的会员ids
     */
    public function getInheritMemberIds($id = 0, $data = [])
    {

        $member_id = $this->getDataValue(['id' => $id, 'is_share_member' => DATA_NORMAL], 'leader_id');

        if (empty($member_id)) {

            return $data;
        } else {

            $data[] = $member_id;

            return $this->getInheritMemberIds($member_id, $data);
        }
    }

    /**
     * 会员添加到用户组
     */
    public function addToGroup($data = [])
    {

        if (SYS_ADMINISTRATOR_ID == $data['id']) : return [RESULT_ERROR, '管理员不能授权哦~']; endif;

        $where['member_id'] = $data['id'];

        $this->setname('AuthGroupAccess')->dataDel($where, '删除成功', true);

        $url = es_url('memberList');

        if (empty($data['group_id'])) : return [RESULT_SUCCESS, '会员授权成功', $url]; endif;


        $add_data = [];

        foreach ($data['group_id'] as $group_id) {

            $add_data[] = ['member_id' => $data['id'], 'group_id' => $group_id, 'create_time' => TIME_NOW, 'update_time' => TIME_NOW];


        }


        return $this->setname('AuthGroupAccess')->dataAdd($add_data, false, $url, '会员授权成功');
    }

    /**
     * 会员添加
     */
    public function memberAdd($data = [])
    {
        $validate = validate('user');

        $validate_result = $validate->scene('add')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;

        $url = es_url('memberList');

        $data['nickname']  = $data['username'];
        $data['leader_id'] = MEMBER_ID;
        $data['is_inside'] = DATA_NORMAL;
        $data['regtime']   = TIME_NOW;
        $data['userip']    = CLIENT_IP;

        $salt             = generate_password(18);
        $data['salt']     = $salt;
        $data['password'] = md5($data['password'] . $salt);


        return $this->setname('User')->dataAdd($data, false, $url, '会员添加成功');
    }

    /**
     * 会员添加
     */
    public function memberEdit($data = [], $info)
    {

        $validate = validate('user');

        $validate_result = $validate->scene('edit')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;

        $url = es_url('memberList');


        if (!empty($data['password'])) {
            $password   = $data['password'];
            $repassword = $data['password_confirm'];
            if ($password != $repassword) {
                return [RESULT_ERROR, '两次密码输入不一致'];
            }
            $md5pass = md5($password . $info['salt']);
            if ($md5pass == $info['password']) {
                return [RESULT_ERROR, '密码未更改'];
            } else {
                $data['password'] = $md5pass;
            }

        } else {
            unset($data['password']);
        }
        unset($data['password_confirm']);

        //$data['nickname']  = $data['username'];
        $data['leader_id'] = MEMBER_ID;
        $data['is_inside'] = DATA_NORMAL;
        $where['id']       = $info['id'];
        return $this->setname('User')->dataEdit($data, $where, false, $url, '会员编辑成功');

    }

    /**
     * 会员认证
     */
    public function memberRz($data = [], $info)
    {

        if (!empty($data['statusdes'])) {

            $data['grades'] = -1;
            $name           = '认证';
        } else {

            $data['grades'] = 0;
            $name           = '取消认证';
        }

        $where['id'] = $data['id'];

        return $this->setname('User')->dataEdit($data, $where, false, '', '会员' . $name . '成功');
    }

    /**
     * 设置会员信息
     */
    public function setMemberValue($where = [], $field = '', $value = '')
    {
        return $this->setDataValue($where, $field, $value);

    }

    /**
     * 修改会员密码
     */
    public function setMemberPassword($data = [], $info)
    {

        $oldpass          = $data['old_password'];
        $password         = $data['password'];
        $confirm_password = $data['confirm_password'];

        if ($info['password'] != md5($oldpass . $info['salt'])) {
            return [RESULT_ERROR, '原密码不正确'];
        }
        if ($password == '') {
            return [RESULT_ERROR, '新密码为空'];
        }
        if ($password != $confirm_password) {
            return [RESULT_ERROR, '两次新密码输入不一致'];
        }
        if ($info['password'] == md5($password . $info['salt'])) {
            return [RESULT_ERROR, '未更改密码'];
        }

        return $this->setDataValue(['id' => $info['id']], 'password', md5($password . $info['salt']), '', '密码修改成功');
    }

    /**
     * 会员批量删除
     */
    public function memberAlldel($ids)
    {

        if (in_array(SYS_ADMINISTRATOR_ID, $ids) || in_array(MEMBER_ID, $ids)) {
            return [RESULT_ERROR, '不能删除自己和管理员~'];
        }
        return $this->dataDel(['id' => $ids], '会员删除成功', true);
    }

    /**
     * 会员删除
     */
    public function memberDel($where = [])
    {

        if (SYS_ADMINISTRATOR_ID == $where['id'] || MEMBER_ID == $where['id']) : return [RESULT_ERROR, '天神和自己不能删除哦~']; endif;

        return $this->dataDel($where, '会员删除成功', true);

    }

    /**
     * 前台登录处理
     */
    public function loginHandle($username = '', $password = '', $verify = '')
    {

        if (empty($username) || empty($password)) : return [RESULT_ERROR, '账号或密码不能为空']; endif;
        $yzm_list = parse_config_array('yzm_list');//1\注册2\登录3\忘记密码4\后台登录
        if (in_array(2, $yzm_list)) {

            if (empty($verify)) : return [RESULT_ERROR, '验证码不能为空']; endif;
            if (!captcha_check($verify)) : return [RESULT_ERROR, '验证码输入错误']; endif;

        }

        $member = $this->getMemberInfo(['username' => $username]);

        if (empty($member)) : return [RESULT_ERROR, '用户不存在']; endif;

        if ($member['status'] == 6) : return [RESULT_ERROR, '用户已被锁定']; endif;

        // 验证用户密码
        if (md5($password . $member['salt']) === $member['password']) {

            $data['last_login_ip'] = CLIENT_IP;

            $data['last_login_time'] = TIME_NOW;

            $where['id'] = $member['id'];

            $this->dataEdit($data, $where);


            $auth = ['member_id' => $member['id'], 'last_login_time' => TIME_NOW];


            //$auth = ['member_id' => $member['id'], 'last_login_time' => $member['last_login_time']];
            session('member_info', $member);
            session('member_auth', $auth);
            session('member_auth_sign', data_auth_sign($auth));
            homeaction_log($member['id'], 18, $member['id']);
            return [RESULT_SUCCESS, '登录成功', url('Index/index')];

        } else {

            return [RESULT_ERROR, '密码输入错误'];
        }
    }

    /**
     * 注销当前用户
     */
    public function logout()
    {
        session('member_info', null);
        session('member_auth', null);
        session('member_auth_sign', null);
        session('[destroy]');
        cookie('sys_key', null);
        return [RESULT_SUCCESS, '注销成功', es_url('user/login')];
    }


    /**
     * 前台注册处理
     */
    public function regHandle($username = '', $password = '', $repassword = '', $usermail = '', $verify = '', $leader_id = '')
    {

        if (empty($username) || empty($password) || empty($usermail)) : return [RESULT_ERROR, '注册信息不能为空']; endif;
        $yzm_list = parse_config_array('yzm_list');//1\注册2\登录3\忘记密码4\后台登录
        if (in_array(1, $yzm_list)) {

            if (empty($verify)) : return [RESULT_ERROR, '验证码不能为空']; endif;
            if (!captcha_check($verify)) : return [RESULT_ERROR, '验证码输入错误']; endif;

        }
        if ($password != $repassword) : return [RESULT_ERROR, '两次密码输入不一致']; endif;


        $data['username'] = $username;
        $data['usermail'] = $usermail;
        $data['password'] = $password;


        // 用户密码
        $data['nickname'] = $username;

        $data['leader_id'] = $leader_id;


        $data['is_inside'] = 0;
        $data['regtime']   = TIME_NOW;
        $data['userip']    = CLIENT_IP;
        $salt              = generate_password(18);
        $data['salt']      = $salt;

        $validate = validate('user');

        $validate_result = $validate->scene('add')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;


        $data['password'] = md5($password . $salt);

        return $this->dataAdd($data, false, '', '注册成功', '', function ($result, $data) {

            //判斷是否為邀请注册
            if($data['leader_id']>0){
                homeaction_log($data['leader_id'], 16, $result);
            }else{
                homeaction_log($result, 17, $result);//注册行为
            }
            //注册同时登录
            $d_data['last_login_ip'] = CLIENT_IP;

            $d_data['last_login_time'] = TIME_NOW;

            $where['id'] = $data['id'] = $result;

            $this->dataEdit($d_data,$where);

            $auth = ['member_id' => $result, 'last_login_time' => TIME_NOW];

            session('member_info', $data);
            session('member_auth', $auth);
            session('member_auth_sign', data_auth_sign($auth));
            homeaction_log($result, 18, $result);//登录行为
        });


    }

}
