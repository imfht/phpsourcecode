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
use Common\Lib\Category; //引入类函数
use Common\Lib\Common; //引入类函数
use Common\Lib\String; //引入类函数
class ModelController extends CommonController {
	/**
	 * 显示链接
	 */
	public function index() {
		$m=D('Model');
		$arr=$m->order('id desc')->select();
		//只显示未被删除news_dell=0的数据
// 		dump($arr);
// 		exit;
		$this->assign('vlist',$arr);
		$this->assign('module',MODULE_NAME);
		$this->display();
	}
	/**
	 * 显示添加页面
	 */
	public function add() {
		$this->display();
	}

	/**
	 * 处理链接添加
	 */
	public function do_add() {
//     	dump($_POST);
// 		exit;
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
		$m=D('Model'); //先读取News数据库表模型文件
		if (!$m->create()){
			$this->error($m->geterror());
		}
		
		//**需要另外添加到数据库的在这里填写
		//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
		$m->model_addtime=time();
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//**需要另外添加到数据库的在这里填写
		$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
		if ($arr){
			$this->success('添加成功',U('Model/index'));
		}else {
			$this->error('添加失败');
			//$this->error($m->geterror());
		}
	}

	/**
	 * 编辑链接页面
	 */
	public function edit() {
		$id=I('get.id');
		//dump($id);
		//exit;
    	$m=D('Model');
    	$arr=$m->find($id);
//     	dump($arr);
//     	exit;

    	$this->assign('v',$arr);
    	$this->display();
	}
	
	/**
	 * 处理编辑
	 */
	public function do_edit() {
// 		dump($_POST);
// 		exit;
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
		$m=D('Model');
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
			$this->success('修改成功',U('Model/index'));
		}else {
			$this->error('修改失败');
			//$this->error($m->geterror());
		}
		
	}



	//彻底删除链接
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
		
		$m=M('Model');
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
	 * 查询数据表单处理类文件
	 */
	public function search(){
		//判断存在id
		if ($id==null){
			$this->assign('ifid',not);
		}
		if (I('post.link_name')==null){
			$this->error('请输入搜索关键字！');
		}
			
		$m=D('Link');
    	//**分页实现代码
    	$count=$m->count();// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
		if (isset($_POST['link_name'])){
			$data['link_name']=array('like',"%{I('post.link_name')}%");
		}
	
		$arr=$m->where($data)->relation(true)->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($arr);
		//exit;
		if ($arr==null){
			$this->error('不存在该用户');
	
		}else {
			//**分页实现代码
			$this->assign('show',$show);// 赋值分页输出
			//**分页实现代码
			$this->assign('vlist',$arr); //在新查询到的数据再分配给前台模板显示
			$this->display('index'); //指定模板
		}
	
	}
	
	
	/**
	 * 批量删除链接处理
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
		
		$m=D('Link'); //数据库表，配置文件中定义了表前缀，这里则不需要写
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




}



?>