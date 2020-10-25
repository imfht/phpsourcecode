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
class NoticeController extends CommonController {
	/**
	 * 通知公告首页展示
	 */
    public function index(){
    	$m=D('Notice');
    	$arr=$m->order('id desc')->select();
    	$count=count($arr);
    	//只显示未被删除news_dell=0的数据
    	//dump($arr);
    	//exit;
    	//循环截取字符 substr_ext函数写在commonaction.class.php中
    	foreach($arr as $k2 => $v2){
    		$arr[$k2]['notice_title'] = Common::substr_ext($v2['notice_title'], 0, 30, 'utf-8',"");
    	}
    	foreach($arr as $k2 => $v2){
    		$arr[$k2]['notice_url'] = Common::substr_ext($v2['notice_url'], 0, 50, 'utf-8',"");
    	}
//     	dump($arr);
//     	exit;
    	 
    	$this->assign('vlist',$arr);
    	$this->assign('module',MODULE_NAME);
    	$this->assign('count',$count);
    	$this->display();
    }
    
    /**
     * 通知公告添加
     */
    public function add(){
    	$this->display();
    }
    /**
     * 通知公告添加处理
     */
    public function do_add(){
        //dump($_POST);
		//exit;
		$m=D('Notice'); //先读取News数据库表模型文件
		if (!$m->create()){
			$this->error($m->geterror());
		}
		//**需要另外添加到数据库的在这里填写
		//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
		$m->notice_time=time();
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//**需要另外添加到数据库的在这里填写
		
		$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
		if ($arr){
			$this->success('添加成功',U('Notice/index'));
		}else {
			$this->error('添加失败');
			//$this->error($m->geterror());
		}
    }
    /**
     * 通知公告编辑
     */
    public function edit(){
    	$id=I('get.id');
		//dump($id);
		//exit;
    	$m=D('Notice');
    	$arr=$m->find($id);
    	//dump($arr);

    	$this->assign('v',$arr);
    	$this->display();
    }
    
    /**
     * 处理链接编辑
     */
    public function do_edit() {
    	//dump($_POST);
    	//exit;
    	$m=D('Notice'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	$data['id']=I('post.id');
    	$data['notice_title']=I('post.notice_title');
    	$data['notice_url']=I('post.notice_url');
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('修改成功！',U('Notice/index'));
    	}
    	else {
    		$this->error('修改失败！');
    	}
    
    }
    
    
    /**
     * 彻底删除链接
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
    
    	$m=M('Notice');
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
    
    	$m=D('Notice'); //数据库表，配置文件中定义了表前缀，这里则不需要写
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
    		
    	$m=D('notice');
    	$data['notice_title']=array('like',"%{$keyword}%");
    	$arr=$m->where($data)->select();
    	//**分页实现代码
    	$count=count($arr);// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
    	$data['notice_title']=array('like',"%{$keyword}%");
    	$arr=$m->where($data)->limit($Page->firstRow.','.$Page->listRows)->select();
    	//dump($arr);
    	//exit;
    	foreach($arr as $k2 => $v2){
    		$arr[$k2]['notice_title'] = Common::substr_ext($v2['notice_title'], 0, 30, 'utf-8',"");
    	}
    	if ($arr==null){
    		$this->error('不存在该公告');
    
    	}else {
    		//**分页实现代码
    		$this->assign('page',$show);// 赋值分页输出
    		//**分页实现代码
    		$this->assign('vlist',$arr); //在新查询到的数据再分配给前台模板显示
    		$this->assign('count',$count);
    		$this->display('index'); //指定模板
    	}
    
    }
    
    


}
