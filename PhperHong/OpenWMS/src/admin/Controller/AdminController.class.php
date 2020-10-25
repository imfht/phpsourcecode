<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Controller;
use Think\Controller;
use Think\Exception;
class AdminController extends Controller {
    /**
     +----------------------------------------------------------
     * 登录页面
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function login(){
    	
    	$cop = C('COPYRIGHT');
    	$INDUSTRY = C('INDUSTRY');
        
        $cop['web_site'] = C('WEB_SITE');
        $cop['group_id'] = session('group_id');
        $cop['industry'] = $INDUSTRY;
        $this->assign($cop);
    
        $is_reg = C('IS_REG');
        $this->assign('title', $cop['pname_cn'].$cop['version_major'].'-登录');
        $this->assign('is_reg', $is_reg);
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * 登录检查
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function check(){
    	$username = I('post.username');
    	$password = I('post.passwd');
    	$admin = D('Admin');
    	$return_data = array();
    	try{
    		$info = $admin->check_user($username, $password);
    		$return_data = array(
    			'ret'	=> 1,
    			'msg'	=> '校验成功',
    			'data'	=> $info,
    		);
    	}catch(Exception $e){
    		$return_data = array(
    			'ret'	=> 0,
    			'msg'	=> $e->getMessage(),
    			'data'	=> '',
    		);
    	}
    	exit(json_encode($return_data));
    }
    /**
     +----------------------------------------------------------
     * 修改密码页面
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function update_password(){
        try {
           
            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-修改密码',
            ));
           

        
	        $cop['web_site'] = C('WEB_SITE');
	        $cop['group_id'] = session('group_id');
	        $this->assign($cop);
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
    }
    /**
     +----------------------------------------------------------
     * 修改密码
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function save_password(){
        $passwd_old = I('post.passwd_old');
        $passwd_new1 = I('post.passwd_new1');
        $passwd_new2 = I('post.passwd_new2');
        $admin = D('Admin');
        $return_data = array();
        try{
            $admin->update_password1($passwd_old, $passwd_new1, $passwd_new2);
            $return_data = array(
                'ret'   => 1,
                'msg'   => '修改成功',
            );
        }catch(Exception $e){
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
        }
        exit(json_encode($return_data));
    }
    /**
     +----------------------------------------------------------
     * 检查是否登录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function check_login(){
        $admin = D('Admin');
        $b = $admin->check_login();
        exit(json_encode(array('ret'=>$b)));
    }
    /**
     +----------------------------------------------------------
     * 退出登录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function logout(){
        $admin = D('Admin');
        $b = $admin->logout();
        $this->success('您已经安全退出', U('Admin/login'));
    }
    /**
     +----------------------------------------------------------
     * 检测账号是否存在
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function check_username(){
    	$username = I('get.username');
        $admin = D('Admin');
        $return_data = array();
        try{
            $admin->check_username($username);
            $return_data = array(
                'ret'   => 1,
                'msg'   => '账号可用',
            );
        }catch(Exception $e){
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
        }
        exit(json_encode($return_data));
    }
    public function _empty(){
           $this->display('Empty:index');
    }
}