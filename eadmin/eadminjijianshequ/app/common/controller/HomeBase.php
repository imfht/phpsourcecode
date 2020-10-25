<?php

namespace app\common\controller;

class HomeBase extends ControllerBase
{


    public function _initialize()
    {


        parent::_initialize();
        if (webconfig('OPEN_WAP') == 1) {
            if ($this->request->isMobile() && strtolower(CONTROLLER_NAME) != 'wap') {

                $this->redirect(es_url('Wap/index'));

            }
            if (!$this->request->isMobile() && strtolower(CONTROLLER_NAME) == 'wap') {

                $this->redirect(es_url('Index/index'));

            }
        }
        $cateinfo = ['id' => 0];
        $this->assign('cateinfo', $cateinfo);
        $siteclose = webconfig('WEB_SITE_CLOSE');
        if ($siteclose != 1) {
            $this->error('前台站点已关闭', webconfig('web_url') . '/admin.php');
        }

        $allowregister = webconfig('USER_ALLOW_REGISTER');

        if ($allowregister != 1) {
            if (strtolower(ACTION_NAME) == 'register') {
                $this->error('该网站暂时关闭了用户注册功能');
            }
        }

        $this->assign('actionname', strtolower(CONTROLLER_NAME . '/' . ACTION_NAME));
        $topiccatelist = self::$datalogic->setname('topiccate')->getDataList(['status' => 1, 'pid' => 0], true, 'sort desc', false);
        $this->assign('topiccatelist', $topiccatelist);
        if (!empty(session('member_info'))) {

            $userinfo = self::$datalogic->setname('user')->getDataInfo(['id' => session('member_info')['id']]);

            $this->assign('userinfo', $userinfo);

        }
        $uid       = is_login();
        $messlist  = [];
        $messcount = 0;
        if ($uid > 0) {
            $midarr = self::$datalogic->setname('readmessage')->getDataColumn(['uid' => $uid], 'mid');//得到所有已读消息

            $where['touid']  = [0, $uid];
            $where['status'] = 1;

            if (!empty($midarr)) {

                $where['id|!'] = $midarr;
            }

            $messtime = self::$datalogic->setname('readtime')->getStat(['uid' => $uid], 'max', 'create_time');

            if ($messtime) {
                $where['create_time|>'] = $messtime;
            }

            $messlist = self::$datalogic->setname('message')->getDataList($where, true, 'update_time desc', false, '', '', 2);

            $messcount = db('message')->where($where)->count();

            foreach ($messlist as $key => $vo) {


                if ($vo['uid'] == 0) {
                    $messlist[$key]['messname'] = '系统消息';
                } else {
                    $typename                   = gettypemess($vo['type']);
                    $messlist[$key]['messname'] = $typename['name'];


                }

            }


        }
        $this->assign('messcount', $messcount);
        $this->assign('messlist', $messlist);
        $this->assign('nowuid', $uid);

        $this->getSystem();//获得全站配置信息
        $this->getNav();//获取前台导航
        $this->autologin();


        $pointarr = parse_config_attr(webconfig('scoretype_list'));
        //获得升级积分
        $this->assign('gpointname', $pointarr['expoint1']);
        //获得下载上传的积分名称

        $this->assign('pointname', $pointarr['point']);


    }

    public function autologin()
    {

        if (!is_login()) {

            $user = unserialize(decrypt(cookie('sys_key')));
            if ((empty($user['userinfo']))) {

            } else {

                self::$datalogic->setname('user')->setDataValue(['id' => $user['userinfo']['id']], 'last_login_time', TIME_NOW);


                $auth = ['member_id' => $user['userinfo']['id'], 'last_login_time' => TIME_NOW];
                $cook = ['id' => $user['userinfo']['id'], 'userinfo' => $user['userinfo'], 'auth' => $auth];
                systemSetKey($cook);


                session('member_info', $user['userinfo']);
                session('member_auth', $auth);
                session('member_auth_sign', data_auth_sign($auth));


            }


        }


    }

    /**
     * 获取站点信息
     */
    public function getSystem()
    {

    }

    /**
     * 获取前端导航列表
     */
    public function getNav()
    {

        if (cache('nav')) {
            $nav = cache('nav');

        } else {

            $nav = self::$datalogic->setname('nav')->getDataList(['status' => 1], true, 'sort asc', false);


            if (!empty($nav)) {
                cache('nav', $nav);
            }
        }

        $this->assign('nav', $nav);

        if (cache('headnav')) {
            $headnav = cache('headnav');

        } else {

            $headnav = self::$datalogic->setname('nav')->getDataList(['status' => 1, 'pid' => 1], true, 'sort asc', false);


            if (!empty($headnav)) {
                cache('headnav', $headnav);
            }
        }

        $this->assign('headnav', $headnav);
        if (cache('footnav')) {
            $footnav = cache('footnav');

        } else {

            $footnav = self::$datalogic->setname('nav')->getDataList(['status' => 1, 'pid' => 0], true, 'sort asc', false);


            if (!empty($footnav)) {
                cache('footnav', $footnav);
            }
        }

        $this->assign('footnav', $footnav);

    }


}