<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {
    public function _initialize() {
        loadTPL();
    }
    /*
     * 网站首页
     */

    public function index() {
        
        $this->actionName = ACTION_NAME;
        $this->categoryName = '';
        $this->contentName = '';
        $data = D('content')->getPage();
        $this->assign('list', $data['list']); // 赋值数据集
        $this->assign('page', $data['page']); // 赋值分页输出
        $this->display();
    }

    /*
     * 显示文章
     */

    public function Content() {
        $id = I('get.id') + 0;
        $this->actionName = ACTION_NAME;
        $this->prev = D('content')->getPrevNext($id, 'prev');
        $this->next = D('content')->getPrevNext($id, 'next');
        $time = time();
        $data = M('content')->where(array('id' => $id, 'status' => 1, "time < $time"))->limit(1)->field('id,title,content,c_id,time,author')->find();
        $categoryRes = M('category')->where('id=' . $data['c_id'])->field('id,title')->find();
        $this->categoryName = $categoryRes['title'];
        $this->categoryId = $categoryRes['id'];
        $this->contentName = $data['title'];
        if (!empty($data)) {
            $this->total = M('comment')->where(array('content_id' => $id))->count();
            $this->comment = M('comment')->where(array('content_id' => $id))->field('id,author,url,content,good_num,bad_num')->select();
            $this->assign('contentDec', substr(strip_tags($data['content']), 0, 200));
            $this->assign('content', $data);
        } else {
            $this->redirect('/');
        }
        $this->display();
    }

    /*
     * 分类
     * 
     */

    Public function category() {
        
        $this->actionName = ACTION_NAME;
        $c_id = I('get.id', 0, 'intval');
        $this->categoryName = M('category')->where("id='$c_id'")->getField('title');
        $this->contentName = '';
        $data = D('content')->getPage($c_id);
        $this->assign('list', $data['list']); // 赋值数据集
        $this->assign('page', $data['page']); // 赋值分页输出
        $this->display('index');
    }

    /*
     * 显示登录
     */

    public function login() {

        $checkLogin = session('admin');
        if (!empty($checkLogin)) {
            redirect(U('Admin/Admin/adminIndex'), 0);
        }
        /* 验证自动登陆 */
        if (cookie('identifier')) {
            $identifier = cookie('identifier');
            $token = cookie('token');
            $auth = M('user')->where(array('identifier' => $identifier))->field('identifier,token,timeout,user,id')->find();
            $now = time();
            if (($identifier == $auth['identifier']) && ($now < $auth['timeout']) && ($token == $auth['token'])) {
                session('admin', $auth['user']);
                session(C('USER_AUTH_KEY'), $auth['id']);
                if ($auth['user'] == C('RBAC_SUPERADMIN')) {
                    session('ADMIN_AUTH_KEY', 1);
                }
                $rbac = new \Org\Util\Rbac();
                $rbac::saveAccessList();
                $data = array();
                $data['token'] = md5(uniqid(rand(), TRUE));
                $data['timeout'] = time() + (3600 * 24 * 7);
                cookie('identifier', $identifier, 3600 * 24 * 7);
                cookie('token', $data['token'], 3600 * 24 * 7);
                M('user')->where(array('identifier' => $identifier))->save($data);
                redirect(U('Admin/Admin/adminIndex'), 0);
                die;
            }
        }
        if (cookie('user')) {
            $user = cookie('user');
            $cook_key = C('COOKIE_KEY');
            $username = clmao_crypt($user, $cook_key, 0);
            $this->username = $username;
        }
        $this->actionName = ACTION_NAME;
        $this->title = '用户登陆';
        $this->display();
    }

    /*
     * 验证登录
     */

    public function checkLogin() {
        $Verify = new \Think\Verify();
        $code = I('post.code');
        $key = C('VERITY_KEY');
        if (!$Verify->check($code) && $key != $code) {
            $this->error('验证码错误', U('Home/Index/login'), 3);
        }

        $c_user = I('post.user');
        $cook_key = C('COOKIE_KEY');
        $c_user = clmao_crypt($c_user, $cook_key, 1);
        cookie('user', $c_user, 60 * 60);
        //setcookie('user', $c_user, 30);
        $user = I('post.user');
        $pwd = clmao_md5_half(I('post.pwd'));
        $uid = M('user')->where(array('user' => $user, 'pwd' => $pwd))->getField('id');
        if (!empty($uid)) {
            session('admin', I('post.user'));
            session(C('USER_AUTH_KEY'), $uid);
            if (I('post.user') == C('RBAC_SUPERADMIN')) {
                session('ADMIN_AUTH_KEY', 1);
            }

            $rbac = new \Org\Util\Rbac();
            $rbac::saveAccessList();
            /* 7天内自动登陆 */
            $data = array();
            $data['token'] = md5(uniqid(rand(), TRUE));
            $data['timeout'] = time() + (3600 * 24 * 7);
            $data['identifier'] = clmao_md5_half($user);
            cookie('identifier', $data['identifier'], 3600 * 24 * 7);
            cookie('token', $data['token'], 3600 * 24 * 7);
            M('user')->where(array('user' => $user))->save($data);
            $this->success('登陆成功', U('Admin/Admin/adminIndex'), 0);
        } else {
            $this->error('用户名或密码错误', U('Home/Index/login'), 3);
        }
    }

    /*
     * 生成验证码
     */

    public function verify() {
        $config = array(
            'fontSize' => 25, // 验证码字体大小
            'length' => 4, // 验证码位数
            'imageH' => 40,
            'imageW' => 300,
            'codeSet' => '0123456789',
        );
        if (is_mobile()) {

            $config['useNoise'] = false;
            $config['useCurve'] = false;
        }
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

    /*
     * 生成验证码，数字验证码
     */

    public function verify2() {
        import("Home.Class.Clmao_Veriy");
        $verify = new \Clmao_Veriy();
        $verify->width = 220;
        $verify->height = 30;
        $verify->create();
    }

    //404
    public function nofound() {
        $this->display('Public/404');
    }

}
