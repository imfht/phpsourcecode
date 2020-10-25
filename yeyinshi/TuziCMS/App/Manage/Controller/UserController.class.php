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
class UserController extends CommonController {
	/**
	 * 显示会员
	 */
	public function index() {
		$m=D('User');
    	//**分页实现代码
    	$count=$m->count();// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
		
		$arr=$m->relation(true)->order('user_rsdate')->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		//dump($arr);
		//exit;
		
		//**分页实现代码
		$this->assign('page',$show);// 赋值分页输出
		//**分页实现代码
		$this->assign('vlist',$arr);
		$this->assign('count',$count);
		$this->display();
	}
	/**
	 * 显示会员详细信息
	 */
	public function detail() {
    	$id=I('get.id');
    	//dump($id);
    	//exit;
    	$m=D('User');
    	$arr=$m->find($id);
    	//var_dump($arr);

    	$this->assign('v',$arr);
    	$this->display();
	}
	
	/**
	 * 处理会员信息
	 */
	public function do_detail() {
// 		dump($_POST);
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
	 * 显示添加会员页面
	 */
	public function add() {
		$this->display();
	}

	/**
	 * 处理会员添加
	 */
	public function do_add() {
    	//dump($_POST);
		//exit;
		$m=M('User'); //先读取News数据库表模型文件
		if (!$m->create()){
			$this->error($m->geterror());
		}
		//**需要另外添加到数据库的在这里填写
		//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
		$m->link_addtime=time();
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//**需要另外添加到数据库的在这里填写
		
		$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
		if ($arr){
			$this->success('添加成功',U('User/index'));
		}else {
			$this->error('添加失败');
			//$this->error($m->geterror());
		}
	}

	/**
	 * 显示会员密码修改页面
	 */
	public function edit() {
		$id=I('get.id');
		//dump($id);
		//exit;
    	$m=D('User');
    	$arr=$m->find($id);
    	//dump($arr);

    	$this->assign('v',$arr);
    	$this->display();
	}
	
	/**
	 * 处理会员密码修改
	 */
	public function do_edit() {
		//dump($_POST);
		//exit;
	    $m=M('User'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	$data['id']=I('post.id');
    	$data['user_pass']=md5(I('post.user_pass'));
    	$data['user_name']=I('post.user_name');
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('修改成功！',U('User/index'));
    	}
    	else {
    		$this->error('修改失败！');
    	}
		
	}



	/**
	 * 删除会员处理
	 */
	public function del() {
		$m=M('User');
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
		$keyword=I('get.keyword');
		//判断存在id
		if ($id==null){
			$this->assign('ifid',not);
		}
		if ($keyword==null){
			$this->error('请输入搜索关键字！');
		}
		$m=D('User');
		$data['user_name']=array('like',"%{$keyword}%");
		$arr=$m->where($data)->relation(true)->select();
    	//**分页实现代码
    	$count=count($arr);// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
		$data['user_name']=array('like',"%{$keyword}%");
		$arr=$m->where($data)->relation(true)->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($arr);
		//exit;
		if ($arr==null){
			$this->error('没有数据');
	
		}else {
			//**分页实现代码
			$this->assign('show',$show);// 赋值分页输出
			//**分页实现代码
			$this->assign('vlist',$arr); //在新查询到的数据再分配给前台模板显示
			$this->assign('count',$count);
			$this->display('index'); //指定模板
		}
	
	}
	
	
	/**
	 * 批量删除会员处理
	 */
	public function delall(){
		//dump($_POST);
		//exit;
		$m=D('User'); //数据库表，配置文件中定义了表前缀，这里则不需要写
		$id = $_POST['id'];
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