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
class KefuController extends CommonController {
	/**
	 * 显示轮播广告
	 */
	public function index() {
    	$m=M('Kefu');
    	$arr=$m->find();
//     	dump($arr);
//     	exit;
    	$this->assign('v',$arr);
		$this->display();
		
	}
	
	
	/**
	 * 处理提交表单
	 */
	public function do_kefu() {
// 		dump($_POST);
// 		exit;
	    //写入到数据库中
        $m=D('Kefu'); //读取Message表的model模型文件MeesageModel.class.php    	
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
    		$this->success('修改成功',U('Kefu/index'));
    	}else {
    		$this->error('修改失败');
    		//$this->error($m->geterror());
    	}
	}

	
	/**
	 * 显示添加轮播广告页面
	 */
	public function add() {
		//文章所属分类
		$m=M('Adnav')->order('id ASC')->select();
		//dump($m);
		//exit;
		$this->assign('cate',$m);
		$this->display();
	}
	
	/**
	 * 显示添加广告分类
	 */
	public function addsort() {
		$this->display();
	}


	/**
	 * 显示轮播广告修改页面
	 */
	public function edit() {
		$id=I('get.id');
		//dump($id);
		//exit;
    	$m=D('Advert');
    	$arr=$m->find($id);
//     	dump($arr);
//     	exit;
    	$this->assign('v',$arr);
    	
    	//文章所属分类
    	$m=M('Adnav')->order('id ASC')->select();
    	//dump($m);
    	//exit;
    	$this->assign('cate',$m);
    	
    	$this->display();
	}
	
	/**
	 * 处理会员密码修改
	 */
	public function do_edit() {
		//dump($_POST);
		//exit;
		//读取模型表看看是否需要过滤 允许提交
	    $m=D('Advert');
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



	//删除轮播广告处理
	public function del() {
		$id=I('get.id');
		//dump($id);
		//exit;
		$m=M('Advert');
		$arr=$m->find($id);
		//dump($arr["advert_image"]);
		//exit;
		//删除本地图片附件 unlink('图片url')
		unlink('./Uploads/Images/'.$arr["advert_image"]);
		
		$count=$m->delete($id);
		if ($count>0){
			$this->success('删除成功！');
		}
		else {
			$this->error('删除失败！');
		}

	}
	
	
	/**
	 * 批量删除轮播广告处理
	 */
	public function delall(){
		//dump($_POST);
		//exit;
		$m=D('Advert'); //数据库表，配置文件中定义了表前缀，这里则不需要写
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

		$m=M('Advert');
		$arr=$m->where($where)->select();
		
		foreach ($arr as $key => $value){
			$images=$value['advert_image'];
			//dump($images);
			//exit;
			unlink('./Uploads/'.$images);
		}
		
		$count=$m->where($where)->delete(); //修改表单用save函数
		if ($count>0){
			$this->success("成功删除{$count}条！");
		}
		else {
			$this->error('批量删除失败！');
		}
	
	}
	
	
	
	/**
	 * 查询数据表单处理类文件
	 */
	public function search(){
		//dump($_POST['advert_name']);
		//exit;
		//判断存在id
		if ($id==null){
			$this->assign('ifid',not);
		}
		
		if (I('post.advert_name')==null){
			$this->error('请输入搜索关键字！');
		}
		 
		$m=D('Advert');
		//**分页实现代码
		$count=$m->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,4);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page->show();// 分页显示输出
		//**分页实现代码
		
		if (isset($_POST['advert_name'])){
			$data['advert_name']=array('like',"%{I('post.advert_name')}%");
		}
		$arr=$m->where($data)->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($arr);
		//exit;
		if ($arr==null){
			$this->error('没有数据');
	
		}else {
			$this->assign('vlist',$arr); //在新查询到的数据再分配给前台模板显示
			$this->display('index'); //指定模板
		}
	
	}
	
	
	/**
	 * 更新排序处理
	 */
	public function sortcate(){
		$m=M('Advert');
		foreach (I('post.') as $id=>$sort){
			$m->where(array('id'=>$id))->setfield('advert_sort',$sort);
		}
		$this->redirect('index');
		 
		//dump($_POST);
	
		$this->display();
	}




}



?>