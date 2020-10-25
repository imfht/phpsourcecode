<?php

class userAction extends userbaseAction {
    /**
     * 用户登陆
     */
    public function login() {
        if ($this->is_weixin()) {
            $param['refer'] = $this->_request('refer','trim');
            $param['mod'] = 'wechat';
            $param['type'] = 'login';
            $this->redirect('oauth/index', $param);
        }
        $this->visitor->is_login && $this->redirect('ucenter/index');
        if (IS_POST) {
            $username = $this->_post('username', 'trim');
            $password = $this->_post('password', 'trim');
            $remember = $this->_post('remember');
            if (empty($username)) {
                IS_AJAX && $this->ajaxReturn(0, '请输入账号！');
                $this->error('请输入账号！');
            }
            if (empty($password)) {
                IS_AJAX && $this->ajaxReturn(0, '请输入密码！');
                $this->error('请输入密码！');
            }
            //连接用户中心
            $passport = $this->_user_server();
            $uid = $passport->auth($username, $password);
            if (!$uid) {
                IS_AJAX && $this->ajaxReturn(0, '账号不存在或密码错误！');
                $this->error('账号不存在或密码错误！');
            }
            $map['uid']=$uid;
            if(D('userinfo')->where($map)->find()==null)
            {
            	
            $map['intro']=C('wkcms_user_intro_default');
     		D('userinfo')->add($map);
            }
            
            //登陆
            $this->visitor->login($uid, $remember);
            //登陆完成钩子
            $tag_arg = array('uid'=>$uid, 'uname'=>$username, 'action'=>'login');
            tag('login_end', $tag_arg);
            //同步登陆
            $synlogin = $passport->synlogin($uid);
            if (IS_AJAX) {
                $this->ajaxReturn(1, L('login_successe').$synlogin);
            } else {
                //跳转到登陆前页面（执行同步操作）
                $ret_url = $this->_post('ret_url', 'trim');
                $refer = $this->_post('refer', 'trim');
                if ($refer) {
                    $refer = base64_decode($refer);
                    $ret_url = $refer;
                }
                $this->success(L('login_successe').$synlogin, $ret_url);
            }
        } else {
            /* 同步退出外部系统 */
            if (!empty($_GET['synlogout'])) {
                $passport = $this->_user_server();
                $synlogout = $passport->synlogout();
            }
            if (IS_AJAX) {
                $resp = $this->fetch('dialog:login');
                $this->ajaxReturn(1, '', $resp);
            } else {
                //来路
                $ret_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : __APP__;
                $refer = $this->_request('refer','trim');
                $this->assign('refer', $refer);

                $this->assign('ret_url', $ret_url);
                $this->assign('synlogout', $synlogout);
                $this->_config_seo();
                $this->display();
            }
        }
    }

    /**
     * 用户退出
     */
    public function logout() {
        $this->visitor->logout();
        //同步退出
        $passport = $this->_user_server();
        $synlogout = $passport->synlogout();
        $ret_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : __APP__;
        $refer = $this->_post('refer', 'trim');
        if ($refer) {
            $refer = base64_decode($refer);
            $ret_url = $refer;
        }
        //跳转到退出前页面（执行同步操作）
        $this->success(L('logout_success').$synlogout, $ret_url);
    }

    /**
    * 用户注册
    */
    public function register() {
        $this->visitor->is_login && $this->redirect('ucenter/index');
        if (IS_POST) {
            //方式
            $iscaptcha = $this->_post('iscaptcha', 'trim');
            if ($iscaptcha) {
                //验证
                $captcha = $this->_post('captcha', 'trim');
                if(session('captcha') != md5($captcha)){
                    $this->error(L('captcha_failed'));
                }
            }
            $username = $this->_post('username', 'trim');
            $email = $this->_post('email','trim');
            $map['email']=$email;
            $reemail = D('user')->where($map)->find();
            if($reemail)
            {
                $this->error('该邮箱已经注册');
            }

            $password = $this->_post('password', 'trim');
            if($password == ''){
               $this->error('密码不能为空');
            }
            $repassword = $this->_post('repassword', 'trim');
             
            if ($password != $repassword) {
                $this->error(L('inconsistent_password')); //确认密码
            }
           
            //用户禁止
            $ipban_mod = D('ipban');
            $ipban_mod->clear(); //清除过期数据
            $is_ban = $ipban_mod->where("(type='name' AND name='".$username."') OR (type='email' AND name='".$email."')")->count();
            $is_ban && $this->error(L('register_ban'));
            //连接用户中心
            $passport = $this->_user_server();
            //注册
            
            
            $uid = $passport->register($username, $password, $email);
            !$uid && $this->error($passport->get_error());
            //第三方帐号绑定
            if (session('user_bind_info')) {
                $user_bind_info = object_to_array(session('user_bind_info'));
                $oauth = new oauth($user_bind_info['type']);
                $bind_info = array(
                    'wkcms_uid' => $uid,
                    'keyid' => $user_bind_info['keyid'],
                    'bind_info' => $user_bind_info['bind_info'],
                );
                $oauth->bindByData($bind_info);
                //临时头像转换
                $this->_save_avatar($uid, $user_bind_info['temp_avatar']);
                //清理绑定COOKIE
                session('user_bind_info', NULL);
            }
            //注册完成钩子
            $tag_arg = array('uid'=>$uid, 'uname'=>$username, 'action'=>'register');
            tag('register_end', $tag_arg);
            //登陆
            $this->visitor->login($uid);
            //登陆完成钩子
            $tag_arg = array('uid'=>$uid, 'uname'=>$username, 'action'=>'login');
            tag('login_end', $tag_arg);
            //同步登陆
            $synlogin = $passport->synlogin($uid);
             
            $map['uid']=$uid;
            if(D('userinfo')->where($map)->find()==null)
            {
            $map['intro']=C('wkcms_user_intro_default');
     		D('userinfo')->add($map);
            }
            
            
            //跳转到注册前页面（执行同步操作）
            $ret_url = $this->_post('ret_url', 'trim');
            $refer = $this->_post('refer', 'trim');
            if ($refer) {
                $refer = base64_decode($refer);
                $ret_url = $refer;
            }

            $refer = session('refer');
            if ($refer) {
                $refer = base64_decode($refer);
                $ret_url = $refer;
                session('refer', null);
            }
            $this->success(L('register_successe').$synlogin, $ret_url);
        } else {
            //关闭注册
            if (!C('wkcms_reg_status')) {
                $this->error(C('wkcms_reg_closed_reason'));
            }
            $ret_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : __APP__;
            $refer = $this->_request('refer','trim');
            $this->assign('refer', $refer);

            $this->assign('ret_url', $ret_url);
            $this->_config_seo();
            $this->display();
        }
    }
     /**
     * 用户绑定
     */
    public function binding() {
        $user_bind_info = object_to_array(session('user_bind_info'));
        
        
       
        $this->assign('user_bind_info', $user_bind_info);
     
        $this->display();
    }
    
    /**
     * 帐号绑定
     */
/*    public function bind() {
        //获取已经绑定列表
        $bind_list = M('user_bind')->field('type')->where(array('uid'=>$this->visitor->info['uid']))->select();
        $binds = array();
        if ($bind_list) {
            foreach ($bind_list as $val) {
                $binds[] = $val['type'];
            }
        }
        
        //获取网站支持列表
        $oauth_list = $this->oauth_list;
        foreach ($oauth_list as $type => $_oauth) {
            $oauth_list[$type]['isbind'] = '0';
            if (in_array($type, $binds)) {
                $oauth_list[$type]['isbind'] = '1';
            }
        }
        $this->assign('oauth_list', $oauth_list);
       
        $this->display();
    }*/
    /**
     * 第三方头像保存
     */
    private function _save_avatar($uid, $img) {
        //获取后台头像规格设置
        $avatar_size = explode(',', C('wkcms_avatar_size'));
        //会员头像保存文件夹
        $avatar_dir = C('wkcms_attach_path') . 'avatar/' . avatar_dir($uid);
        !is_dir($avatar_dir) && mkdir($avatar_dir,0777,true);
        //生成缩略图
        $img = C('wkcms_attach_path') . 'avatar/' . $img;
        foreach ($avatar_size as $size) {
            Image::thumb($img, $avatar_dir.md5($uid).'_'.$size.'.jpg', '', $size, $size, true);
        }
        @unlink($img);
    }
    
 


}