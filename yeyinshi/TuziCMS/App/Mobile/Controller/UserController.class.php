<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Mobile\Controller;
use Think\Controller;
use Common\Lib\String; //引入类函数
use Common\Lib\Category; //引入类函数
use Common\Lib\Common; //引入类函数
class UserController extends Controller {
	/**
	 * 登录用户信息页面
	 */
    public function index(){
    	//****SEO信息
    	$mi=M('Config');
    	$data=$mi->field('config_webname,config_webkw,config_cp')->find();
    	//dump($data);
    	//exit;
    	$title= '会员中心'.' - '.$data['config_webname'];
    	$keywords='会员中心'.','.$data['config_webkw'];
    	$description='会员中心'.','.$data['config_cp'];
    	$this->assign('title',$title);
    	$this->assign('keywords',$keywords);
    	$this->assign('description',$description);
    	

    	//显示通知公告	
    	$m=D('Notice');
    	$arr=$m->order('notice_time')->select();
        foreach($arr as $k2 => $v2){
    		$arr[$k2]['notice_title'] = Common::substr_ext($v2['notice_title'], 0, 25, 'utf-8',"");
    	}
//     	dump($arr);
//     	exit;
    	$this->assign('vlist',$arr);
    	
    	//**判断是否登录，否则强制到登录页面
        session_start();
    	if (!isset($_SESSION['user_email']) || $_SESSION['user_email']==''){
    		//$this->redirect('user/login');
    		$this->error('请先登录','login');
    	}
    	//**显示登录用户信息
    	$user_email=$_SESSION["user_email"];
    	//dump($user_email);
    	//exit;
    	$m=D('User');
    	$data['user_email']=$user_email;
    	//$data['username']='gege';
    	$arr=$m->where($data)->select();
    	$arr=$arr[0];
    	//dump($arr);
    	//exit;
    	
    	$this->assign('v',$arr);
    	$this->display();
    }
    
    /**
     * 显示登录页面
     */
    public function login(){
    	//****SEO信息
    	$mi=M('Config');
    	$data=$mi->field('config_webname,config_webkw,config_cp')->find();
    	//dump($data);
    	//exit;
    	$title= '会员登录'.' - '.$data['config_webname'];
    	$keywords='会员登录'.','.$data['config_webkw'];
    	$description='会员登录'.','.$data['config_cp'];
    	$this->assign('title',$title);
    	$this->assign('keywords',$keywords);
    	$this->assign('description',$description);

    	$this->display();
    }
    
    /**
     * 处理会员登录
     */
    public function do_login(){
//     	dump($_POST);
//     	exit;
    	$email=I('post.user_email');
    	$pass=I('post.user_pass');
    	$verify=I('post.verify');
    	
//     	dump($pass);
//     	exit;
    	
    	if (!$email){
    		$this->error('请填写登录邮箱');
    	}
    	if(!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)){
    		$this->error('请填写正确登录邮箱');
    	}
    	if (!$pass){
    		$this->error('请填写登录密码');
    	}
    	if (!$verify){
    		$this->error('请填写验证码');
    	}
    	//判断验证码是否正确
        if (!check_verify($verify)) {
			$this->error('验证码不正确');
		}
    	//判断用户是否存在和密码是否正确
    	$m=M('User');
    	$where['user_email']=$email;
    	$where['user_pass']=md5($pass);
    	$arr=$m->field('id,user_name,user_date,user_ip,user_ok')->where($where)->find();
//     	dump($arr);
//     	exit;
    	if ($arr) {
    		if (!$arr['user_ok']==1){
    			$this->error('用户未激活！');
    		}
    		session_start();
    		//$_SESSION('user_name',$arr['user_name']);
    		$_SESSION['user_name']=$arr['user_name'];
    		$_SESSION['user_email']=I('post.user_email');
    		$_SESSION['uid']=$arr['id'];//用户登录用uid标识，跟后台管理员区别。
    		
    		//$_SESSION['user_date']=$arr['user_date'];
    		//$_SESSION['user_ip']=$arr['user_ip'];

    		//**将登录的时间和ip插入数据库中
    		$m=M('User'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    		$data['id']=$_SESSION['uid'];//注明id
    		$data['user_date']=time();//登录时间
    		$data['user_ip']=get_client_ip();//登录ip
    		$data['user_login']=array('exp', 'user_login+1');
    		$data['user_olddate']=$arr['user_date'];//将本次
    		$data['user_oldip']=$arr['user_ip'];//将本次
    		$count=$m->save($data); //修改表单用save函数
    		//dump($count);
    		//exit;
    		if ($count>0){
    			$this->success('登录成功',U('User/index'));
    		}
    		
    	}else {
    		$this->error('用户名或者密码错误');
    	}
    	
    	//var_dump($_SESSION);
    }
    
    /**
     * 显示注册页面
     */
    public function register(){
    	//****SEO信息
    	$mi=M('Config');
    	$data=$mi->field('config_webname,config_webkw,config_cp')->find();
    	//dump($data);
    	//exit;
    	$title= '会员注册'.' - '.$data['config_webname'];
    	$keywords='会员注册'.','.$data['config_webkw'];
    	$description='会员注册'.','.$data['config_cp'];
    	$this->assign('title',$title);
    	$this->assign('keywords',$keywords);
    	$this->assign('description',$description);
    	$this->display();
    }
    
    /**
     * 处理用户注册
     */
    public function do_register(){
//     	dump($_POST);
//     	exit;
    	
    	$m=D('User');
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	//防止外界恶意输入
    	$email=I('post.user_email');
    	$verify=I('post.verify');
    	if (!$email || !$verify){
    		$this->error('表单信息错误！');
    	}
    	
    	//**需要另外添加到数据库的在这里填写
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
    	$m->user_rsdate=time();
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//**需要另外添加到数据库的在这里填写
    	
    	$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
    	if ($arr){
    		$this->success('注册成功',U('User/login'));
    	}else {
    		$this->error('注册失败');
    		//$this->error($m->geterror());
    	}
    }
    
    /**
     * 退出登录
     */
    public function do_out(){
    	$furl = $_SERVER['HTTP_REFERER'];
    	//echo $furl;
    	//exit;
    	
    	if (empty($furl) || strpos($furl, 'register') || strpos($furl, 'login')) {
    		$furl = U(GROUP_NAME. '/user/login');
    	}
    	
    	//**判断是否登录，否则强制到登录页面
    	session_start();
    	if (!isset($_SESSION['user_email']) || $_SESSION['user_email']==''){
    		//$this->redirect('user/login');
    		$this->error('请先登录','login');
    	}
    	
    	//session_start();
    	//session_unset();//删除所有session变量
    	//session_destroy();//删除session文件
    	
    	session('user_name',null);
    	session('user_email',null);
    	session('uid',null);
    	
    	$this->success('成功退出',U('user/login'));
    	
    }
    
    /**
     * 显示修改用户名
     */
    public function name(){
    	//****SEO信息
    	$mi=M('Config');
    	$data=$mi->field('config_webname,config_webkw,config_cp')->find();
    	//dump($data);
    	//exit;
    	$title= '用户名修改'.' - '.$data['config_webname'];
    	$keywords='用户名修改'.','.$data['config_webkw'];
    	$description='用户名修改'.','.$data['config_cp'];
    	$this->assign('title',$title);
    	$this->assign('keywords',$keywords);
    	$this->assign('description',$description);
    	
    	//**判断是否登录，否则强制到登录页面
    	session_start();
    	if (!isset($_SESSION['user_email']) || $_SESSION['user_email']==''){
    		//$this->redirect('user/login');
    		$this->error('请先登录','login');
    	}
    	
    	$id=$_SESSION['uid'];
    	//dump($id);
    	//exit;
    	$m=D('User');
    	$arr=$m->find($id);
//     	dump($arr);
//     	exit;
    	
    	$this->assign('v',$arr);
    	$this->display();

    }
    /**
     * 处理用户名修改
     */
    public function do_name(){
    	//判断修改的用户id是不是自己的，防止修改错误
    	$id=I('post.id');
    	$email=I('post.user_email');
    	// 		dump($email);
    	// 		exit;
    	$arr=D('User')->find($id);
    	$arr=$arr['user_email'];
    	// 		dump($arr);
    	// 		exit;
    	if ($arr!=$email){
    		$this->error('你没有限权修改别人的信息');
    	}
    	
    	//**判断是否登录，否则强制到登录页面
    	session_start();
    	if (!isset($_SESSION['user_email']) || $_SESSION['user_email']==''){
    		//$this->redirect('user/login');
    		$this->error('请先登录','login');
    	}
//     	dump($_POST);
//     	exit;
    	$id=I('post.id');
        $m=D('User'); //先读取News数据库表模型文件
    	if (!$m->create()){
	    	$this->error($m->geterror());
	    }
	    $data['id']=$id;
	    $data['user_name']=htmlspecialchars($_POST['user_name']);
	    $count=$m->save($data); //修改表单用save函数
	    if ($count>0){
	    	$this->success('修改成功,下次登录见效！');
	    }
	    else {
	    	$this->error('修改失败！');
	    }
    
    }
    
    /**
     * 显示修改基本信息
     */
    public function detail(){
    	//****SEO信息
    	$mi=M('Config');
    	$data=$mi->field('config_webname,config_webkw,config_cp')->find();
    	//dump($data);
    	//exit;
    	$title= '修改基本信息'.' - '.$data['config_webname'];
    	$keywords='修改基本信息'.','.$data['config_webkw'];
    	$description='修改基本信息'.','.$data['config_cp'];
    	$this->assign('title',$title);
    	$this->assign('keywords',$keywords);
    	$this->assign('description',$description);
    	
    	//**判断是否登录，否则强制到登录页面
    	session_start();
    	if (!isset($_SESSION['user_email']) || $_SESSION['user_email']==''){
    		//$this->redirect('user/login');
    		$this->error('请先登录','login');
    	}
    	$id=$_SESSION['uid'];
    	//dump($id);
    	//exit;
    	$m=D('User');
    	$arr=$m->find($id);
    	//dump($arr);
    	
    	$this->assign('v',$arr);
    	$this->display();
    }
    
    /**
     * 处理会员信息
     */
    public function do_detail() {
//     	dump($_POST);
//     	exit;
		//判断修改的用户id是不是自己的，防止修改错误
		$id=I('post.id');
		$email=I('post.user_email');
// 		dump($email);
// 		exit;
		$arr=D('User')->find($id);
		$arr=$arr['user_email'];
// 		dump($arr);
// 		exit;
		if ($arr!=$email){
			$this->error('你没有限权修改别人的信息');
		}
		//**判断是否登录，否则强制到登录页面
		session_start();
		if (!isset($_SESSION['user_email']) || $_SESSION['user_email']==''){
			//$this->redirect('user/login');
			$this->error('请先登录','login');
		}
		
// 		dump($arr);
// 		exit;
    	
    	$m=D('User'); //先读取News数据库表模型文件
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	//**需要另外添加到数据库的在这里填写
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
    	//$m->uid=$_SESSION['id'];
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//**需要另外添加到数据库的在这里填写
    
    	$arr=$m->save(); //自动修改 不需要定义id 因为post表单中已经有
    	if ($arr){
    		$this->success('修改成功');
    	}else {
    		$this->error('修改失败');
    		//$this->error($m->geterror());
    	}
    }
    
    /**
     * 显示修改密码
     */
    public function pass(){
    	//****SEO信息
    	$mi=M('Config');
    	$data=$mi->field('config_webname,config_webkw,config_cp')->find();
    	//dump($data);
    	//exit;
    	$title= '修改密码'.' - '.$data['config_webname'];
    	$keywords='修改密码'.','.$data['config_webkw'];
    	$description='修改密码'.','.$data['config_cp'];
    	$this->assign('title',$title);
    	$this->assign('keywords',$keywords);
    	$this->assign('description',$description);
    	
    	//**判断是否登录，否则强制到登录页面
    	session_start();
    	if (!isset($_SESSION['user_email']) || $_SESSION['user_email']==''){
    		//$this->redirect('user/login');
    		$this->error('请先登录','login');
    	}
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('User');
    	$arr=$m->find($id);
    	//dump($arr);
    	$this->assign('v',$arr);
    	$this->display();

    }
    
    /**
     * 处理修改密码
     */
    public function do_pass(){
    	//判断修改的用户id是不是自己的，防止修改错误
    	$id=I('post.id');
    	$email=I('post.user_email');
    	// 		dump($email);
    	// 		exit;
    	$arr=D('User')->find($id);
    	$arr=$arr['user_email'];
    	// 		dump($arr);
    	// 		exit;
    	if ($arr!=$email){
    		$this->error('你没有限权修改别人的信息');
    	}
    	
    	//**判断是否登录，否则强制到登录页面
    	session_start();
    	if (!isset($_SESSION['user_email']) || $_SESSION['user_email']==''){
    		//$this->redirect('user/login');
    		$this->error('请先登录','login');
    	}
    	//dump($_POST);
    	//exit;
    	
    	//**查询用户的信息
    	$m=M('User');
    	$id=$_SESSION['uid'];//获取登录会员的id
    	$arr=$m->select($id);
    	$user_pass=$arr[0]['user_pass'];//直接输出数据库里的字段admin_pass的值
    	
    	//**判断旧密码是否正确
    	if ($user_pass!==md5(I('post.user_nowpass'))){
    		$this->error('当前密码不对');
    	}
    	//**判断新密码和确认密码是否为空
    	if (I('post.user_pass')=='' || I('post.user_okpass')==''){
    		$this->error('请输入新密码和确认密码');
    	}
    	
    	//**判断确认密码是否正确
    	if (I('post.user_pass')!==I('post.user_okpass')){
    		$this->error('确认密码不对');
    	}
    	//****处理新密码的修改
        $m=M('User'); //数据库表，配置文件中定义了表前缀，这里则不需要写
        //配置文件开启了表单令牌验证 防止表单重复提交
        if (!$m->autoCheckToken($_POST)){
        	$this->error('表单重复提交！');
        }
    	$data['id']=$_SESSION['id'];
    	$data['user_pass']=md5(I('post.user_pass'));
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('修改成功！');
    	}
    	else {
    		$this->error('修改失败！');
    	}
    
    }

}
