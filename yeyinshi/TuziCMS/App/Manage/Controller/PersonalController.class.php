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
class PersonalController extends CommonController {
	/**
	 * 显示管理员信息
	 */
    public function index(){
    	//**显示登录用户信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	//dump($arr);
    	//exit;
    	
    	$this->assign('v',$arr);
    	$this->display();
		
    }
    
    /**
     * 显示后台管理员列表
     */
    public function listadmin(){
    	//**判断是否有限权，显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	$arr=$arr['admin_type'];
    	//dump($arr);
    	//exit;
    	if ($arr==1){// 如果不是超级管理员限权
    		$this->error('你不是超级管理员，没有限权！');
    	}
    	//exit;
    	
		$m=D('Admin');
		//**分页实现代码
		$count=$m->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page->show();// 分页显示输出
		//**分页实现代码
		
		$arr=$m->order('admin_rsdate')->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		//dump($arr);
		//exit;
		
		//**分页实现代码
		$this->assign('page',$show);// 赋值分页输出
		//**分页实现代码
		$this->assign('count',$count);
		$this->assign('vlist',$arr);
		$this->display();
    
    }
    
    /**
     * 显示管理员密码修改页面
     */
    public function listpass() {
    	$id=I('get.id');
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	//dump($arr);
    
    	$this->assign('v',$arr);
    	$this->display();
    }
    
    
    /**
     * 处理管理员密码修改
     */
    public function do_listpass() {
    	//dump($_POST);
    	//exit;
    	
    	//**判断是否有限权，显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	$arr=$arr['admin_type'];
    	//dump($arr);
    	//exit;
    	if ($arr==1){// 如果不是超级管理员限权
    		$this->error('你不是超级管理员，没有限权！');
    	}
    	//exit;
    	
    	$m=M('Admin'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	$data['id']=I('post.id');
    	$data['admin_pass']=md5(I('post.admin_pass'));
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('修改成功！');
    	}
    	else {
    		$this->error('修改失败！');
    	}
    
    }
    
    /**
     * 显示会员详细信息
     */
    public function edit() {
    	$id=I('get.id');
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	//var_dump($arr);
    
    	$this->assign('v',$arr);
    	$this->display();
    }
    
    /**
     * 处理管理员信息修改
     */
    public function do_edit() {
//     	dump($_POST);
//     	exit;
    	
    	//**判断是否有限权，显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	$arr=$arr['admin_type'];
    	//dump($arr);
    	//exit;
    	if ($arr==1){// 如果不是超级管理员限权
    		$this->error('你不是超级管理员，没有限权！');
    	}
    	//exit;
    	
    	$m=D('Admin'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	$data['id']=$id;
    	$data['admin_name']=I('post.admin_name');
    	$data['admin_myname']=I('post.admin_myname');
    	$data['admin_type']=I('post.admin_type');
    	$data['admin_ok']=I('post.admin_ok');

    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('修改成功！');
    	}
    	else {
    		$this->error('修改失败！');
    	}
    }
    
    /**
     * 显示添加管理员页面
     */
    public function add() {
    	$this->display();
    }
    
    /**
     * 处理管理员添加
     */
    public function do_add() {
    	//dump($_POST);
    	//exit;
    	
    	//**判断是否有限权，显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	$arr=$arr['admin_type'];
    	//dump($arr);
    	//exit;
    	if ($arr==1){// 如果不是超级管理员限权
    		$this->error('你不是超级管理员，没有限权！');
    	}
    	//exit;
    	
    	$m=D('Admin'); //先读取News数据库表模型文件
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    
    	//**需要另外添加到数据库的在这里填写
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
    	$m->admin_login=0;
    	$m->admin_rsdate=time();
    	$m->admin_pass=md5(I('post.admin_pass'));
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//**需要另外添加到数据库的在这里填写
    
    	$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
    	if ($arr){
    		$this->success('添加成功',U('Personal/listadmin'));
    	}else {
    		$this->error('添加失败');
    		//$this->error($m->geterror());
    	}
    }
    
    
    /**
     * 删除管理员处理
     */
    public function del() {
    	//**判断是否有限权，显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	$arr=$arr['admin_type'];
    	//dump($arr);
    	//exit;
    	if ($arr==1){// 如果不是超级管理员限权
    		$this->error('你不是超级管理员，没有限权！');
    	}
    	//exit;
    	
    	$m=M('Admin');
    	$id=I('get.id');
    	$count=$m->delete($id);
    	if ($count>0){
    		$this->success('删除成功！');
    	}
    	else {
    		$this->error('删除失败！');
    	}
    
    }
    
    /**
     * 批量删除管理员处理
     */
    public function delall(){
    	//dump($_POST);
    	//exit;
    	
    	//**判断是否有限权，显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	$arr=$arr['admin_type'];
    	//dump($arr);
    	//exit;
    	if ($arr==1){// 如果不是超级管理员限权
    		$this->error('你不是超级管理员，没有限权！');
    	}
    	//exit;
    	
    	$m=D('Admin'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	$id = I('post.id');
    	//dump($id);
    	//exit;
    	if ($id==null){
    		$this->error('请选择删除项！');
    	}
    	//判断id是数组还是一个数值
    	if(is_array($id)){
    		$where = 'id in('.implode(',',$id).')';
    		//implode() 函数返回一个由数组元素组合成的字符串
    	}else{
    		$where = 'id='.$id;
    	}
    	//dump($where);
    	//exit;
    	$count=$m->where($where)->delete(); //修改表单用save函数
    	if ($count>0){
    		$this->success("成功删除{$count}条！");
    	}
    	else {
    		$this->error('批量删除失败！');
    	}
    
    }
    
    /**
     * 修改管理员密码处理
     */
    public function modify_admin(){
//     	dump($_POST);
//     	exit;
    	
    	$m=D('Admin'); //先读取News数据库表模型文件
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	$data['id']=I('post.id');
    	$data['admin_myname']=I('post.admin_myname');
    	$data['admin_email']=I('post.admin_email');
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('修改成功！',U('Personal/index'));
    	}
    	else {
    		$this->error('修改失败！');
    	}
    }
    

    /**
     * 显示管理员密码修改
     */
    public function pass(){
    	//**显示登录用户信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	//dump($arr);
    	//exit;
    	 
    	$this->assign('v',$arr);
    	$this->display();
    }
    
    /**
     * do_pass方法
     * 修改管理员密码
     */
    public function do_pass(){
    	//dump($_POST);
    	//exit;
    	
    	//**查询用户的信息
    	$m=M('Admin');
    	$id=$_SESSION['id'];//获取登录会员的id
    	$arr=$m->select($id);
    	$admin_pass=$arr[0]['admin_pass'];//直接输出数据库里的字段admin_pass的值
    	
    	//**判断旧密码是否正确
    	if ($admin_pass!==md5(I('post.admin_nowpass'))){
    		$this->error('当前密码不对');
    	}
    	
    	//**判断新密码和确认密码是否为空
    	if (I('post.admin_pass')=='' || I('post.admin_okpass')==''){
    		$this->error('请输入新密码和确认密码');
    	}
    	
    	//**判断确认密码是否正确
    	if (I('post.admin_pass')!==I('post.admin_okpass')){
    		$this->error('确认密码不对');
    	}
    	
    	//****处理新密码的修改
        $m=M('Admin'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	$data['id']=$_SESSION['id'];
    	$data['admin_pass']=md5(I('post.admin_pass'));
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('修改成功！');
    	}
    	else {
    		$this->error('修改失败！');
    	}
    }

    
}