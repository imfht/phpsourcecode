<?php

namespace app\index\controller;

use app\common\controller\HomeBase;


class Index extends HomeBase
{


    public function _initialize()
    {

        parent::_initialize();

    }

    public function index($order = 1)
    {

        $openslider = webconfig('OPEN_SLIDER');
        $this->assign('openslider', $openslider);//是否开启轮播图


        $slidearr = self::$datalogic->setname('slideimg')->getDataList(['status' => 1, 'type' => 2], true, 'create_time desc', false);

        $this->assign('slidearr', $slidearr);

        //全站用户数
        $usercount = db('user')->where(['status|>' => 0])->count();
        //全站帖子数
        $topiccount = db('topic')->where(['status|>' => 0])->count();
        //全站话题数
        $groupcount = db('group')->where(['status|>' => 0])->count();

        $this->assign('groupcount', $groupcount);

        $this->assign('topiccount', $topiccount);

        $this->assign('usercount', $usercount);

        $uid = is_login();

        //按照近期发帖排序前23个用户

        $userhy = self::$datalogic->setname('homeaction_log')->getDataList(['m.status' => 1], 'max(m.create_time) as maxtime,user.nickname,user.id,user.point,user.expoint1,user.userhead', 'maxtime desc', false, [['user|user', 'user.id=m.uid', 'INNER']], 'm.uid', 30);

        $this->assign('userhy', $userhy);

        if ($order == 1) {
            $orderstr = 'settop desc,create_time desc';
        } elseif ($order == 2) {
            $orderstr = 'settop desc,update_time desc,reply desc,view desc';
        } else {
            $orderstr = 'settop desc,choice desc,update_time desc';
        }

        $this->assign('order', $order);


        $topiclist = self::$datalogic->setname('topic')->getDataList(['m.status' => 1], 'user.nickname,user.point,user.expoint1,user.userhead,rzuser.type as rztype,rzuser.status as rzstatus,m.*', $orderstr, 10, [['user|user', 'user.id=m.uid'], ['rzuser|rzuser', 'rzuser.uid=m.uid']]);

        foreach ($topiclist['data'] as $key => $vo) {

            if ($vo['rzstatus'] && $vo['rzstatus'] == 1) {

                if ($vo['rztype'] == 1) {
                    $topiclist['data'][$key]['rzicon'] = 'icon-vimeo';
                } else {
                    $topiclist['data'][$key]['rzicon'] = 'icon-vimeo i-ve';
                }

            }

            $focuscount = db('user_focus')->where(['sid' => $vo['id'], 'type' => 1])->count();

            $cinfo = db('comment')->where(['fid' => $vo['id'], 'pid' => 0])->order('create_time desc')->limit(1)->getList();


            if ($cinfo) {

                $lsnickname = getusernamebyid($cinfo[0]['uid']);
                //'<span class="aw-user-name" data-id="'.$cinfo[0]['uid'].'">'.$lsnickname.'</span>&nbsp;'.
                $topiclist['data'][$key]['replystr']   = '回复&nbsp;' . friendlyDate($cinfo[0]['create_time']) . '&nbsp;(' . $focuscount . '人关注)';
                $topiclist['data'][$key]['replytime']  = friendlyDate($cinfo[0]['create_time']);
                $topiclist['data'][$key]['replyuser']  = $lsnickname;
                $topiclist['data'][$key]['replyuid']   = $cinfo[0]['uid'];
                $topiclist['data'][$key]['focuscount'] = $focuscount;
                $topiclist['data'][$key]['actionname'] = '回复';
            } else {
                $lsnickname                            = getusernamebyid($vo['uid']);
                $topiclist['data'][$key]['replystr']   = '发布&nbsp;' . friendlyDate($vo['create_time']) . '&nbsp;(' . $focuscount . '人关注)';
                $topiclist['data'][$key]['replytime']  = friendlyDate($vo['create_time']);
                $topiclist['data'][$key]['replyuser']  = $lsnickname;
                $topiclist['data'][$key]['replyuid']   = $vo['uid'];
                $topiclist['data'][$key]['focuscount'] = $focuscount;
                $topiclist['data'][$key]['actionname'] = '发布';
            }

            if ($vo['gidtext']) {

                $topiclist['data'][$key]['htlist'] = explode(',', $vo['gidtext']);

            }


            if (!$vo['description']) {

                $topiclist['data'][$key]['description'] = msubstr(clearHtml(htmlspecialchars_decode($vo['content'])), 0, 60);
            }
            $arr                                    = getcontentimage(htmlspecialchars_decode($vo['content']), false)[1];
            $topiclist['data'][$key]['imagescount'] = count($arr);

            if (count($arr) > 3) {
                $arr = array_slice($arr, 0, 3);
            }
            $topiclist['data'][$key]['imagesarr'] = $arr;

        }

        $this->assign('topiclist', $topiclist['data']);
        $this->assign('page', $topiclist['page']);


        //热门话题列表
        $hotgrouplist = self::$datalogic->setname('group')->getDataList(['status' => 1], true, 'sort desc,membercount desc,topiccount desc', false, '', '', 2);

        $this->assign('hotgrouplist', $hotgrouplist);


        $rzuserlist = self::$datalogic->setname('rzuser')->getDataList(['m.status' => 1], 'user.nickname,user.point,user.expoint1,user.expoint2,user.userhead,m.*', 'm.create_time desc', false, [['user|user', 'user.id=m.uid']], '', 2);

        foreach ($rzuserlist as $key => $vo) {

            $rzuserlist[$key]['zancount'] = db('homeaction_log')->where(['type' => [1, 9], 'uid' => $vo['uid']])->count();

            $rzuserlist[$key]['topiccount'] = db('topic')->where(['status' => 1, 'uid' => $vo['uid']])->count();

            $rzuserlist[$key]['fscount'] = db('user_focus')->where(['sid' => $vo['uid'], 'type' => 3])->count();

        }
        $this->assign('rzuserlist', $rzuserlist);

        $hotuserlist = self::$datalogic->setname('user')->getDataList(['m.status' => 1], 'count(topic.id) as topiccount,m.*,rzuser.type,rzuser.statusdes as rzstatusdes', 'topiccount desc', false, [['topic|topic', 'topic.uid=m.id'], ['rzuser|rzuser', 'rzuser.uid=m.id and rzuser.status=1']], 'topic.uid', 5);

        foreach ($hotuserlist as $key => $vo) {

            $hotuserlist[$key]['zancount'] = db('homeaction_log')->where(['type' => [1, 9], 'uid' => $vo['id']])->count();
        }

        $this->assign('hotuserlist', $hotuserlist);

        $topapp = self::$datalogic->setname('app')->getDataList(['status' => 1, 'istop' => 1], true, 'aid desc', false, '', '', 5);

        $newapp = self::$datalogic->setname('app')->getDataList(['status' => 1, 'istop' => 0], true, 'aid desc', false, '', '', 5);

        $this->assign('newapp', $newapp);

        $this->assign('topapp', $topapp);

        return $this->fetch();

    }

    public function pointrule()
    {

        $list = db('point_rule')->where(['status' => 1])->order('controller desc')->getList();

        if ($list) {

            $controllerlist = parse_config_attr(webconfig('point_type_list'));
            $scoretypelist  = parse_config_attr(webconfig('scoretype_list'));
            foreach ($list as $k => $v) {

                $list[$k]['scoretypename']  = $scoretypelist[$v['scoretype']];
                $list[$k]['controllername'] = $controllerlist[$v['controller']];
                if ($v['num'] > 0) {

                } else {
                    $list[$k]['num'] = '不限制';
                }

            }


        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function yzemailurl($id)
    {
        if (is_login() == 0) {

            $this->error('亲！请登录', es_url('user/login'));
        } else {
            $uid = is_login();


            $user = self::$datalogic->setname('user')->getDataInfo(['id' => $uid]);

            if ($id == md5($user['salt'] . $uid . $user['usermail'])) {
                if ($user['status'] < 3) {


                    self::$datalogic->setname('user')->setDataValue(['id' => $uid], 'status', 2);
                } else {

                    self::$datalogic->setname('user')->setDataValue(['id' => $uid], 'status', 5);
                }

                $this->success('验证成功', es_url('user/index'));


            } else {
                $this->error('非法验证', es_url('user/index'));
            }

        }


    }

    public function yzemail()
    {

        $mail = $this->request->param();

        $uid = is_login();

        if ($uid == false) func_result(0, '您还未登录,请登录后再操作!', '', es_url('User/login'));

        $user = self::$datalogic->setname('user')->getDataInfo(['id' => $uid]);

        $emailinfo = self::$datalogic->setname('user')->getDataInfo(['usermail' => $mail['email'], 'id|!' => $uid]);
        if ($emailinfo) {
            $this->error('该邮箱已经被其他账号注册');
        } else {
            $n['usermail'] = $mail['email'];
            $where['id']   = $uid;
            self::$datalogic->setname('user')->dataEdit($n, $where, false);


            $data['email'] = $mail['email'];
            $data['title'] = '邮箱验证';
            $str           = md5($user['salt'] . $uid . $data['email']);
            $data['body']  = '您的链接已经生成<br>http://' . $_SERVER['HTTP_HOST'] . es_url('index/yzemailurl', ['id' => $str]);


            asyn_sendmail($data);

            $this->success('邮箱登录已更改为新邮箱，请到邮箱查收验证');

        }


    }

    public function forgetcodebymail()
    {

        $mail = $this->request->param();

        $emailinfo = self::$datalogic->setname('user')->getDataInfo(['usermail' => $mail['email']]);
        //是否能得到


        if ($emailinfo) {


            $data['email'] = $mail['email'];
            $data['title'] = '忘记密码-邮箱验证';


            $code         = generate_code($mail['email']);
            $data['body'] = '您的验证码已经生成<br>' . $code;
            asyn_sendmail($data);
            $this->success('验证码已经发送，请到邮箱查收验证');


        } else {
            $this->error('该邮箱不存在');


        }

    }

    public function reyzemail()
    {

        $mail = $this->request->param();
        $uid  = is_login();

        if ($uid == false) func_result(0, '您还未登录,请登录后再操作!', '', es_url('User/login'));

        $user = self::$datalogic->setname('user')->getDataInfo(['id' => $uid]);


        $emailinfo = self::$datalogic->setname('user')->getDataInfo(['usermail' => $mail['email'], 'id|!' => $uid]);
        echo 21;
        if ($emailinfo) {
            dump($emailinfo);
            $this->error('邮箱已被使用');
        } else {
            $n['usermail'] = $mail['email'];
            if ($user['status'] == 2) {
                $n['status'] = 1;
            } else {
                $n['status'] = 3;
            }
            $n['id'] = $uid;
            self::$datalogic->setname('user')->dataEdit('user', $n, false);


            $data['email'] = $mail['email'];
            $data['title'] = '邮箱验证';
            $str           = md5($user['salt'] . $uid . $data['email']);
            $data['body']  = '您的链接已经生成<br>http://' . $_SERVER['HTTP_HOST'] . es_url('index/yzemailurl', ['id' => $str]);
            asyn_sendmail($data);

            $this->success('邮箱登录已更改为新邮箱，请到邮箱查收验证');


        }

    }

    public function send_mail()
    {


        $mail = $this->request->param();

        $res = send_email($mail['email'], $mail['title'], $mail['body']);

        if ($res == 1) {
            $this->success('邮件已发送，请到邮箱进行查收');

            //	$this->success('邮件已发送，请到邮箱进行查收');
        } else {
            $this->error('邮件发送失败，请检查邮箱设置');

            //$this->error('邮件发送失败，请检查邮箱设置');
        }
    }
}
