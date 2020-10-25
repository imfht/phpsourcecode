<?php

namespace app\index\controller;

use app\common\controller\HomeBase;

use app\common\logic\User as LogicUser;


class Ologin extends HomeBase
{

    // 用户逻辑
    private static $logicUser = null;

    private static $loginmodel = null;


    public function _initialize()
    {

        parent::_initialize();

        self::$logicUser = get_sington_object('logicUser', LogicUser::class);
    }

    public function login($name)
    {

        self::$loginmodel = model('Ologin', 'service');

        self::$loginmodel->setDriver(ucfirst($name));

        self::$loginmodel->login();


    }

    public function call_back($name)
    {

        $data = $this->param;

        self::$loginmodel = model('Ologin', 'service');

        self::$loginmodel->setDriver(ucfirst($name));

        $info = self::$loginmodel->call_back($data);

        if ($info) {
            //进行登录
            $editdata['last_login_ip'] = CLIENT_IP;

            $editdata['last_login_time'] = TIME_NOW;

            $where['id'] = $info;

            db('user')->update($editdata, $where);

            $member = db('user')->where($where)->getRow();

            $auth = ['member_id' => $info, 'last_login_time' => TIME_NOW];

            point_controll($info, 'login', 0);//登录增加经验值

            //$auth = ['member_id' => $member['id'], 'last_login_time' => $member['last_login_time']];
            session('member_info', $member);
            session('member_auth', $auth);
            session('member_auth_sign', data_auth_sign($auth));

            $this->success('登录成功', es_url('Index/index'));


        } else {

            $this->error('获取用户信息失败');
        }

    }

}
