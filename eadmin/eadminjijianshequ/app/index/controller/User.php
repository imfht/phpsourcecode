<?php

namespace app\index\controller;

use app\common\controller\HomeBase;

use app\common\logic\User as LogicUser;


class User extends HomeBase
{

    // 用户逻辑
    private static $logicUser = null;


    public function _initialize()
    {

        parent::_initialize();

        self::$logicUser = get_sington_object('logicUser', LogicUser::class);

        $uid = is_login();
        $this->assign('nowuid', $uid);
        if ($uid > 0) {
            $nowuserinfo = self::$datalogic->setname('user')->getDataInfo(['id' => $uid]);

            $rzinfo = self::$datalogic->setname('rzuser')->getDataInfo(['uid' => $uid]);
            if ($rzinfo) {
                if ($rzinfo['status'] == 1) {
                    $nowuserinfo['statusdes'] = $rzinfo['statusdes'];
                    $nowuserinfo['hasrz']     = 1;
                    if ($rzinfo['type'] == 1) {

                        $nowuserinfo['icon'] = 'icon-myvip';
                        $nowuserinfo['type'] = '个人认证';
                    } else {
                        $nowuserinfo['icon'] = 'icon-myvip i-ve';
                        $nowuserinfo['type'] = '企业认证';
                    }

                }

            } else {
                $nowuserinfo['hasrz'] = 0;
            }
            $this->assign('nowuserinfo', $nowuserinfo);
        }

    }

    public function setverify()
    {
        $uid = is_login();
        if ($uid == 0) {
            $this->jump(([RESULT_ERROR, '请登录后操作', es_url('index/index')]));
        }
        $rzinfo = self::$datalogic->setname('rzuser')->getDataInfo(['uid' => $uid]);
        if ($rzinfo) {
            if ($rzinfo['status'] == 1) {
                $hasrz = 2;
            } else {
                $hasrz = 1;
            }

        } else {
            $hasrz = 0;
        }
        $this->assign('info', $rzinfo);
        $this->assign('hasrz', $hasrz);
        return $this->fetch();

    }

    public function setavatar()
    {

        $uid = is_login();
        if ($uid == 0) {
            $this->jump(([RESULT_ERROR, '请登录后操作', es_url('index/index')]));
        }

        return $this->fetch();
    }

    public function setpass()
    {

        $uid = is_login();
        if ($uid == 0) {
            $this->jump(([RESULT_ERROR, '请登录后操作', es_url('index/index')]));
        }

        return $this->fetch();
    }


    public function focususer()
    {


        $uid = is_login();
        if ($uid == 0) {
            $this->jump(([RESULT_ERROR, '请登录后操作']));
        } else {

            $where['type'] = 3;
            $where['uid']  = $uid;
            $where['sid']  = $this->param['useruid'];

            if (self::$datalogic->setname('user_focus')->getStat($where) > 0) {
                homeaction_log($uid, 8, $this->param['useruid']);
                $this->jump(self::$datalogic->setname('user_focus')->dataDel(['type' => 0, 'sid' => $this->param['useruid'], 'uid' => $uid], '取消关注', true));

            } else {
                $data['type'] = 3;
                $data['sid']  = $this->param['useruid'];
                $data['uid']  = $uid;
                homeaction_log($uid, 7, $this->param['useruid']);
                $this->jump(self::$datalogic->setname('user_focus')->dataAdd($data, false, '', '关注成功'));
            }


        }


    }

    public function userfocus()
    {

        !is_login() && $this->jump(RESULT_REDIRECT, '请先登录', es_url('Index/index'));

        $uid = is_login();
        empty($this->param['type']) ? $type = 1 : $type = $this->param['type'];

        $sidarr  = self::$datalogic->setname('user_focus')->getDataColumn(['uid' => $uid, 'type' => 3], 'sid');//得到所有的我关注的人
        $gzidarr = self::$datalogic->setname('user_focus')->getDataColumn(['sid' => $uid, 'type' => 3], 'uid');//得到所有关注我的人


        if ($type == 1) {//好友

            if (empty($sidarr)) {
                $userlist = ['data' => [], 'page' => [], 'total' => 0];

            } else {
                $userlist = self::$datalogic->setname('user_focus')->getDataList(['uid' => $sidarr, 'sid' => $uid, 'type' => 3], true, 'create_time desc', 7);
                if ($userlist['total'] > 0) {
                    foreach ($userlist['data'] as $key => $v) {
                        $userinfo                              = self::$datalogic->setname('user')->getDataInfo(['id' => $v['uid']]);
                        $userlist['data'][$key]['nickname']    = $userinfo['nickname'];
                        $userlist['data'][$key]['userid']      = $userinfo['id'];
                        $userlist['data'][$key]['description'] = $userinfo['description'];
                        $userlist['data'][$key]['userhead']    = $userinfo['userhead'];
                        $userlist['data'][$key]['grades']      = $userinfo['grades'];
                        $userlist['data'][$key]['description'] = $userinfo['description'];
                        $userlist['data'][$key]['topiccount']  = self::$datalogic->setname('topic')->getStat(['uid' => $v['uid']]);
                    }

                }
            }


        }
        if ($type == 2) {//关注
            if (empty($gzidarr)) {

                $userlist = self::$datalogic->setname('user_focus')->getDataList(['uid' => $uid, 'type' => 3], true, 'create_time desc', 7);


            } else {
                $userlist = self::$datalogic->setname('user_focus')->getDataList(['m.uid' => $uid, 'm.type' => 3, 'm.sid|!' => $gzidarr], true, 'create_time desc', 7);

            }
            if ($userlist['total'] > 0) {
                foreach ($userlist['data'] as $key => $v) {
                    $userinfo                              = self::$datalogic->setname('user')->getDataInfo(['id' => $v['sid']]);
                    $userlist['data'][$key]['nickname']    = $userinfo['nickname'];
                    $userlist['data'][$key]['userid']      = $userinfo['id'];
                    $userlist['data'][$key]['description'] = $userinfo['description'];
                    $userlist['data'][$key]['userhead']    = $userinfo['userhead'];
                    $userlist['data'][$key]['grades']      = $userinfo['grades'];
                    $userlist['data'][$key]['description'] = $userinfo['description'];
                    $userlist['data'][$key]['topiccount']  = self::$datalogic->setname('topic')->getStat(['uid' => $v['sid']]);
                }

            }
        }
        if ($type == 3) {//粉丝

            if (empty($sidarr)) {

                $userlist = self::$datalogic->setname('user_focus')->getDataList(['sid' => $uid, 'type' => 3], true, 'create_time desc', 7);


            } else {
                $userlist = self::$datalogic->setname('user_focus')->getDataList(['sid' => $uid, 'type' => 3, 'uid|!' => $sidarr], true, 'create_time desc', 7);

            }
            if ($userlist['total'] > 0) {
                foreach ($userlist['data'] as $key => $v) {
                    $userinfo                              = self::$datalogic->setname('user')->getDataInfo(['id' => $v['uid']]);
                    $userlist['data'][$key]['nickname']    = $userinfo['nickname'];
                    $userlist['data'][$key]['userid']      = $userinfo['id'];
                    $userlist['data'][$key]['description'] = $userinfo['description'];
                    $userlist['data'][$key]['userhead']    = $userinfo['userhead'];
                    $userlist['data'][$key]['grades']      = $userinfo['grades'];
                    $userlist['data'][$key]['description'] = $userinfo['description'];
                    $userlist['data'][$key]['topiccount']  = self::$datalogic->setname('topic')->getStat(['uid' => $v['uid']]);
                }

            }

        }
        $this->assign('uid', $uid);
        $this->assign('type', $type);
        $this->assign('userlist', $userlist['data']);
        $this->assign('userlistcount', $userlist['total']);
        $this->assign('page', $userlist['page']);
        return $this->fetch();
    }

    public function home()
    {


        if (empty($this->param['id'])) {
            $this->error('参数错误', es_url('index/index'));
        } else {
            $useruid      = $this->param['id'];
            $homeuserinfo = self::$datalogic->setname('user')->getDataInfo(['id' => $useruid]);
            if (!$homeuserinfo) {
                $this->error('该用户已被删除', es_url('index/index'));
            }

            $uid = is_login();
            if ($uid != $useruid && $uid > 0) {
                homeaction_log($uid, 15, $useruid);
            }

            $homeuserinfo['topiccount'] = self::$datalogic->setname('topic')->getStat(['uid' => $useruid]);
            $homeuserinfo['fscount']    = self::$datalogic->setname('user_focus')->getStat(['sid' => $useruid, 'type' => 3]);

            $fslist = self::$datalogic->setname('user_focus')->getDataList(['m.sid' => $useruid, 'm.type' => 3], 'm.uid,user.nickname,user.userhead', 'm.create_time desc', false, [['user|user', 'user.id=m.uid']], '', 5);
            $this->assign('fslist', $fslist);

            $gzlist = self::$datalogic->setname('user_focus')->getDataList(['m.uid' => $useruid, 'm.type' => 3], 'm.sid,user.nickname,user.userhead', 'm.create_time desc', false, [['user|user', 'user.id=m.sid']], '', 5);
            $this->assign('gzlist', $gzlist);

            $gzhtlist = self::$datalogic->setname('user_focus')->getDataList(['m.uid' => $useruid, 'm.type' => 2], 'm.sid,group.name', 'm.create_time desc', false, [['group|group', 'group.id=m.sid']]);
            $this->assign('gzhtlist', $gzhtlist);

            $homeuserinfo['gzcount']      = self::$datalogic->setname('user_focus')->getStat(['type' => 3, 'uid' => $useruid]);
            $homeuserinfo['gzhtcount']    = self::$datalogic->setname('user_focus')->getStat(['type' => 2, 'uid' => $useruid]);
            $homeuserinfo['visitorcount'] = self::$datalogic->setname('homeaction_log')->getStat(['type' => 15, 'uid' => $useruid]);


            $rzinfo = self::$datalogic->setname('rzuser')->getDataInfo(['status' => 1, 'uid' => $useruid]);
            if ($rzinfo) {
                $homeuserinfo['rzuser'] = $rzinfo;
            } else {
                $homeuserinfo['rzuser'] = 0;
            }


            $gidtext     = db('topic')->where(['uid' => $useruid])->column('gidtext');
            $schtlistarr = [];
            $schtlist    = [];
            $ztcount     = 0;
            if ($gidtext) {

                foreach ($gidtext as $k => $v) {

                    if ($v) {
                        $arr         = explode(',', $v);
                        $schtlistarr = array_merge($schtlistarr, $arr);
                    }


                }


                $gidarr = array_count_values($schtlistarr);

                arsort($gidarr);


                foreach ($gidarr as $k => $v) {

                    $schtlist[$k]['praise'] = db('topic')->where(['gidtext|~' => $k, 'uid' => $useruid])->sum('praise');

                    $schtlist[$k]['name'] = $k;

                    $ztcount = $ztcount + $v;

                }
            }


            $homeuserinfo['ztcount'] = $ztcount;
            $this->assign('homeuserinfo', $homeuserinfo);

            $this->assign('schtlist', $schtlist);

            if ($uid == $useruid) {
                $hasfocus = 2;
            } else {
                if (user_has_focus($uid, $useruid)) {
                    $hasfocus = 1;
                } else {
                    $hasfocus = 0;
                }
            }
            $this->assign('hasfocus', $hasfocus);

            $lasttime = db('homeaction_log')->where(['uid' => $useruid])->max('create_time');
            $this->assign('lasttime', $lasttime);


        }
        $scheme = is_ssl() ? 'https://' : 'http://'; //协议类型;

        $yqlink = $scheme . $_SERVER['HTTP_HOST'] . es_url('user/register', ['id' => encrypt($homeuserinfo['id'])]);
        $this->assign('yqlink', $yqlink);
        return $this->fetch();

    }

    public function index()
    {

        !is_login() && $this->jump(RESULT_REDIRECT, '请先登录', es_url('Index/index'));


        return $this->fetch();

    }

    /**
     * 修改个人头像处理
     */
    public function setavatarHandle()
    {

        $info        = session('member_info');
        $data        = $this->param;
        $uid         = is_login();
        $where['id'] = $uid;
        $data['id']  = $uid;
        $obj         = new User();
        $this->jump(self::$datalogic->setname('user')->dataEdit($data, $where, false, '', $info = '信息编辑成功', $obj, 'callback_setinfo'));
    }

    /**
     * 修改个人信息处理
     */
    public function setinfoHandle()
    {


        $info = session('member_info');

        $data             = $this->param;
        $data['username'] = $info['username'];
        $uid              = is_login();
        $where['id']      = $uid;
        $data['id']       = $uid;

        $obj = new User();

        $this->jump(self::$datalogic->setname('user')->dataEdit($data, $where, true, '', $info = '信息编辑成功', $obj, 'callback_setinfo'));

    }

    public function callback_setinfo($result, $data)
    {

        $member = self::$datalogic->setname('user')->getDataInfo(['id' => $data['id']]);
        session('member_info', $member);
        return;
    }

    public function setverifyHandle()
    {

        $data                = $this->param;
        $data['status']      = 0;
        $data['create_time'] = time();

        $uid = is_login();

        $info = self::$datalogic->setname('rzuser')->getDataInfo(['uid' => $uid]);
        if ($info) {

            $this->jump(self::$datalogic->setname('rzuser')->dataEdit($data, ['id' => $info['id']], false, '', '等待认证审核'));
        } else {
            $data['uid'] = $uid;
            $this->jump(self::$datalogic->setname('rzuser')->dataAdd($data, false, '', '等待认证审核'));
        }

    }

    /**
     * 修改密码处理
     */
    public function setpasswordHandle()
    {
        $data = $this->param;
        $mem  = session('member_info');
        $info = self::$datalogic->setname('user')->getDataInfo(['id' => $mem['id']]);
        $this->jump(self::$logicUser->setMemberPassword($data, $info));

    }

    public function mess()
    {
        !is_login() && $this->jump(RESULT_REDIRECT, '请先登录', es_url('Index/index'));
        $uid    = is_login();
        $midarr = self::$datalogic->setname('readmessage')->getDataColumn(['uid' => $uid], 'mid');

        $where['touid']  = [0, $uid];
        $where['status'] = 1;

        if (!empty($midarr)) {

            $where['id|!'] = $midarr;
        }

        if (self::$datalogic->setname('readtime')->getStat(['uid' => $uid]) > 0) {
            $data['uid']         = $uid;
            $data['create_time'] = time();

            self::$datalogic->setname('readtime')->dataEdit($data, ['uid' => $uid], false);
        } else {
            $data['uid']         = $uid;
            $data['create_time'] = time();
            $data['status']      = 1;
            self::$datalogic->setname('readtime')->dataAdd($data, false);
        }

        $list = self::$datalogic->setname('message')->getDataList($where, true, 'update_time desc');
        foreach ($list['data'] as $key => $vo) {


            if ($vo['uid'] == 0) {
                $list['data'][$key]['messname'] = '系统消息';
            } else {
                $typename                       = gettypemess($vo['type']);
                $list['data'][$key]['messname'] = $typename['name'];
            }

        }
        $this->assign('list', $list['data']);
        $this->assign('page', $list['page']);
        $this->assign('totalcount', $list['total']);


        return $this->fetch();

    }

    public function ajaxdelmess()
    {
        $myuid = is_login();
        $id    = $this->param['id'];
        $uid   = $this->param['uid'];
        if ($uid > 0) {

            $where['id'] = $id;

            $this->jump(self::$datalogic->setname('message')->dataDel($where, '删除成功', true));
        } else {
            $data['uid'] = $myuid;
            $data['mid'] = $id;
            $this->jump(self::$datalogic->setname('readmessage')->dataAdd($data, false, '', '删除成功'));
        }

    }

    public function ajaxdelallmess()
    {
        $uid            = is_login();
        $midarr         = self::$datalogic->setname('readmessage')->getDataColumn(['uid' => $uid], 'mid');
        $where['touid'] = $uid;
        if (!empty($midarr)) {
            $where['id|!'] = $midarr;
        }

        self::$datalogic->setname('message')->dataDel($where, '', true);//删除私信

        $where1['touid'] = 0;
        if (!empty($midarr)) {
            $where1['id|!'] = $midarr;
        }
        $list = self::$datalogic->setname('message')->getDataList($where1, true, 'update_time desc', false);

        foreach ($list as $k => $v) {
            $data['uid'] = $uid;
            $data['mid'] = $v['id'];
            $n           = self::$datalogic->setname('readmessage')->dataAdd($data, false, '', '删除成功');


        }

        $this->jump([RESULT_SUCCESS, '清空成功']);

    }


    public function shoucang()
    {

        !is_login() && $this->jump(RESULT_REDIRECT, '请先登录', es_url('Index/index'));
        $uid = is_login();

        empty($this->param['type']) ? $type = 1 : $type = $this->param['type'];//1表示帖子
        if ($type == 1) {
            $topiclist = self::$datalogic->setname('user_focus')->getDataList(['m.uid' => $uid, 'm.type' => 1], 'm.*,topic.title,topic.choice,topic.settop,topic.reply,topic.view,topic.create_time as topiccreate_time,user.nickname,user.userhead', 'm.create_time desc', 8, [['topic|topic', 'topic.id=m.sid'], ['user|user', 'user.id=topic.uid']], '', '', false, 'm');

        } else {


        }

        $this->assign('type', $type);
        $this->assign('topiclist', $topiclist['data']);
        $this->assign('page', $topiclist['page']);
        $this->assign('totalcount', $topiclist['total']);
        return $this->fetch();

    }

    public function mytopic()
    {
        !is_login() && $this->jump(RESULT_REDIRECT, '请先登录', es_url('Index/index'));
        $uid = is_login();


        $topiclist = self::$datalogic->setname('topic')->getDataList(['m.uid' => $uid, 'm.status' => 1], 'm.*,user.nickname,user.userhead', 'm.create_time desc', 8, [['user|user', 'user.id=m.uid']], '', '', false, 'm');


        $this->assign('topiclist', $topiclist['data']);
        $this->assign('page', $topiclist['page']);
        $this->assign('totalcount', $topiclist['total']);
        return $this->fetch();

    }

    /**
     * 忘记密码页面
     */
    public function forget()
    {
        session('http_referer', 1);
        if (IS_POST) {


            $datan = $this->request->param();

            $n = self::$datalogic->setname('user')->getDataInfo(['usermail' => $datan['email']]);


            if (empty($n) || ($n['status'] != 2 && $n['status'] != 5)) {
                $this->error(0, '', ['code' => 0, 'msg' => '邮箱未激活或邮箱未注册']);
            } else {

                $data['email'] = $n['usermail'];

                $data['title'] = '找回密码';
                $str           = md5($n['salt'] . $n['id'] . $n['usermail']);

                $data['body'] = 'http://' . $_SERVER['HTTP_HOST'] . es_url('user/resetmima', ['mod' => $n['id'], 'id' => $str]);


                asyn_sendmail($data);

                $this->success(200, '', ['code' => 200, 'msg' => '邮件已发送，请到邮箱进行查收']);


            }


        } else {


        }

        return $this->fetch();

    }

    public function resetmima()
    {

        $data = $this->request->param();
        $n    = self::$datalogic->setname('user')->getDataInfo(['id' => $data['mod']]);

        if (md5($n['salt'] . $n['id'] . $n['usermail']) == $data['id']) {

            $this->assign('userid', $n['id']);
            $this->assign('salt', md5($n['salt']));
            $this->assign('username', $n['username']);

            return $this->fetch();
        } else {
            $this->error('非法操作', es_url('user/forget'));
        }

    }

    public function resetpass()
    {
        $data = $this->request->param();
        $n    = self::$datalogic->setname('user')->getDataInfo(['id' => $data['uid']]);
        if (md5($n['salt']) == $data['salt']) {

            if (md5($data['password'] . $n['salt']) == $n['password']) {

                $this->jump([RESULT_SUCCESS, '密码重置成功']);

            } else {

                $m['password'] = md5($data['password'] . $n['salt']);

                $this->jump(self::$datalogic->setname('user')->dataEdit($m, ['id' => $n['id']], false, '密码重置成功'));
            }


        } else {
            $this->error('非法操作', es_url('index/index'));
        }


    }

    /**
     * 注册页面
     */
    public function register()
    {

        is_login() && $this->jump(RESULT_REDIRECT, '', es_url('Index/index'));

        $yzm_list = parse_config_array('yzm_list');//1\注册2\登录3\忘记密码4\后台登录

        if (in_array(1, $yzm_list)) {

            $yzm = 1;

        } else {

            $yzm = 0;

        }

        $this->assign('yzm', $yzm);
        empty($this->param['id']) ? $lead_id = 0 : $lead_id = decrypt($this->param['id']);

        $this->assign('leader_id', $lead_id);

        return $this->fetch();

    }

    /**
     * 注册处理
     */
    public function regHandle($username = '', $password = '', $repassword = '', $usermail = '', $verify = '', $leader_id = '')
    {

        $this->jump(self::$logicUser->regHandle($username, $password, $repassword, $usermail, $verify, $leader_id));

    }


    /**
     * 登录页面
     */
    public function login()
    {

        is_login() && $this->jump(RESULT_REDIRECT, '', es_url('Index/index'));

        $yzm_list = parse_config_array('yzm_list');//1\注册2\登录3\忘记密码4\后台登录

        if (in_array(2, $yzm_list)) {

            $yzm = 1;

        } else {

            $yzm = 0;

        }

        $this->assign('yzm', $yzm);

        return $this->fetch();

    }

    /**
     * 登录处理
     */
    public function loginHandle($username = '', $password = '', $verify = '')
    {

        $this->jump(self::$logicUser->loginHandle($username, $password, $verify));

    }

    /**
     * 注销处理
     */
    public function logout()
    {

        $this->jump(self::$logicUser->logout());

    }

}
