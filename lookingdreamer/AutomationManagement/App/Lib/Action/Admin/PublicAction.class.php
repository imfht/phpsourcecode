<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class PublicAction extends Action
{

    public function _initialize()
    {
        C('SHOW_RUN_TIME', false);
        C('SHOW_PAGE_TRACE', false);
    }

    // 检查用户是否登录
    protected function checkUser()
    {
        if (!session(C('USER_AUTH_KEY'))) {
            redirect(__APP__ . C('USER_AUTH_GATEWAY'));
        }
    }

    // 用户登录页面
    public function login()
    {
        if (!session(C('USER_AUTH_KEY'))) {
            $this->admin_userId = cookie('admin_loginId');
            $this->display();
        } else {
            redirect(__APP__ . '/Admin');
        }
    }

    public function index()
    {
        //如果通过认证跳转到首页
        redirect(__APP__ . '/Admin');
    }

    public function urlbytitle()
    {
        $title = $_POST['title'];
        $url = Pinyin($title) . '.' . C('URLREWRITE');
        $this->success($url);
    }

    // 用户登出
    public function logout()
    {
        if (session(C('USER_AUTH_KEY'))) {
            session(C('USER_AUTH_KEY'), null);
            session(C('ADMIN_AUTH_KEY'), null);
            session('admin_loginId', null);
            $this->assign('jumpUrl', __APP__ . C('USER_AUTH_GATEWAY'));
            $this->success('退出成功！');
        } else {
            $this->error('已经退出！');
        }
    }

    // 登录检测
    public function checkLogin()
    {
        if (empty($_POST['account'])) {
            $this->error('帐号错误！');
        } elseif (empty($_POST['password'])) {
            $this->error('密码必须！');
        }
        $map = array();
        $map['account'] = $_POST['account'];
        $map["status"] = array('gt', 0);
        /* if(!$_SESSION['verify'] != md5($_POST['verify'])) {
             $this->error('验证码错误！');
         }*/
        $User = M('User');
        $authInfo = $User->where($map)->find();
        if (!$authInfo) {
            $this->error('帐号不存在或已禁用！');
        } else {
            if ($authInfo['password'] != pwdHash($_POST['password'])) {
                $this->error('密码错误！');
            }
            session(C('USER_AUTH_KEY'), $authInfo['id']);
            cookie('admin_loginId', $authInfo['account'], 8640000);
            cookie('admin_nickname', $authInfo['nickname'] ? $authInfo['nickname'] : $authInfo['account'], 8640000);
            session('admin_loginId', $authInfo['account']);
            if ($authInfo['id'] == 1) {
                session(C('ADMIN_AUTH_KEY'), true);
            }
            //保存登录信息
            $ip = get_client_ip();
            $time = time();
            $data = array();
            $data['id'] = $authInfo['id'];
            $data['last_login_time'] = $time;
            $data['login_count'] = array('exp', '(login_count+1)');
            $data['last_login_ip'] = $ip;
            $User->save($data);
//						$this->success('登录成功！');

            $browser_string = $this->my_get_browser();
            if ($browser_string == "Firefox" or $browser_string == "Chrome" or $browser_string == "Safari" ) {
                U('Admin/Index/index', '', '', true, '');
                exit();

            } else {
                $this->error('请使用谷歌和火狐浏览器访问,以获取最佳用户体验!');
            }


        }
    }

    // 验证码显示
    public function verify()
    {
        import("ORG.Util.Image");
        Image::buildImageVerify(4);
    }

    //判断浏览器类型,暂时只支持firefox和chrome浏览器
    function my_get_browser()
    {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return '命令行，机器人来了！';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0')) {
            return 'Internet Explorer 9.0';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0')) {
            return 'Internet Explorer 8.0';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0')) {
            return 'Internet Explorer 7.0';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
            return 'Internet Explorer 6.0';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
            return 'Firefox';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
            return 'Chrome';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')) {
            return 'Safari';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) {
            return 'Opera';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], '360SE')) {
            return '360SE';
        }
    }


}

?>
