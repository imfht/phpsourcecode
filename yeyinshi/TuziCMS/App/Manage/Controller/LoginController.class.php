<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Manage\Controller;
use Think\Controller;
class LoginController extends Controller {
    /**
     * do_login方法
     * 处理后台登录
     */
    public function do_login(){
       	//dump($_POST);
    	//exit;
    	if (I('post.admin_name')==''){
    		$this->error('请填写用户名');
    	}
    	if (I('post.admin_pass')==''){
    		$this->error('请填写登录密码');
    	}
    	if (I('post.verify')==''){
    		$this->error('请填写验证码');
    	}
    	// 检查验证码
    	$verify = I('post.verify');
//     	dump($verify);
//     	exit;
    	if (!check_verify($verify)) {
			$this->error('验证码不正确');
		}
    	
    	//echo 111;
    	//exit;
    	//判断用户是否存在和密码是否正确
    	$m=M('Admin');
    	$admin_name=I('post.admin_name');
    	$admin_pass=I('post.admin_pass');
    	
    	$where['admin_name']=$admin_name;
    	$where['admin_pass']=md5($admin_pass);
    	$arr=$m->field('id,admin_name,admin_date,admin_ip,admin_ok')->where($where)->find();
    	//dump($arr);
    	//exit;
    	
    	
    	if ($arr) {
    		//**判断管理员是否被锁定
    		if ($arr['admin_ok']==1){
    			$this->error('该管理用户已被锁定');
    		}
    		session_start();
    		$_SESSION['admin_name']=$admin_name;
    		$_SESSION['id']=$arr['id'];
    		$_SESSION['admin_date']=$arr['admin_date'];
    		$_SESSION['admin_ip']=$arr['admin_ip'];
    		

    		//**将登录的时间和ip插入数据库中
    		$m=M('Admin'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    		$data['id']=$_SESSION['id'];//注明id
    		$data['admin_date']=time();//登录时间
    		$data['admin_ip']=get_client_ip();//登录ip
    		$data['admin_login']=array('exp', 'admin_login+1');
    		
    		$data['admin_olddate']=$_SESSION['admin_date'];//将本次
    		$data['admin_oldip']=$_SESSION['admin_ip'];//将本次
    		
    		$count=$m->save($data); //修改表单用save函数
    		//dump($count);
    		//exit;
    		if ($count>0){
    			$this->success('登录成功',U('admin/index'));
    		}
    		
    	}else {
    		$this->error('用户名或者密码错误');
    	}
    	
    }
}