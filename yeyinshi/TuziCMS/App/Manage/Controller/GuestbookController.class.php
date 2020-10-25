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
class GuestbookController extends CommonController {
	/**
	 * 显示留言本
	 */
    public function index(){
    	$m=D('Guestbook');
    	//**分页实现代码
    	$count=$m->count();// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
    	$arr=$m->where("gb_dell=0")->order('gb_addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
    	//dump($arr);
    	//exit;
    	//循环截取字符 substr_ext函数写在commonaction.class.php中
    	foreach($arr as $k2 => $v2){
    		$arr[$k2]['gb_name'] = Common::substr_ext($v2['gb_name'], 0, 10, 'utf-8',"");
    	}
    	foreach($arr as $k2 => $v2){
    		$arr[$k2]['gb_title'] = Common::substr_ext($v2['gb_title'], 0, 25, 'utf-8',"");
    	}
    	foreach($arr as $k2 => $v2){
    		$arr[$k2]['gb_content'] = Common::substr_ext($v2['gb_content'], 0, 100, 'utf-8',"");
    	}
    	foreach($arr as $k2 => $v2){
    		$arr[$k2]['gb_recontent'] = Common::substr_ext($v2['gb_recontent'], 0, 100, 'utf-8',"");
    	}
    	//     	dump($arr);
    	//     	exit;
    	
    	$this->assign('page',$show);
    	$this->assign('vlist',$arr);
    	$this->assign('count',$count);
    	$this->display();
    }
    
    /**
     * 删除留言处理
     */
    public function do_delect(){
        $m=M('Guestbook');
    	$id=I('get.id');
    	$count=$m->delete($id);
    	if ($count>0){
    		$this->success('删除成功！',U('Guestbook/index'));
    	}
    	else {
    		$this->error('删除失败！');
    	}
    }
    
    /**
     * 显示留言回复
     */
    public function reply(){
    	$id=I('get.id');
    	$m=D('Guestbook');//读取数据库模型model文件，关联模型。
    	$arr=$m->relation(true)->find($id);
    	//dump($arr);
    	//exit;
    	
    	$this->assign('v',$arr);
		$this->display();
    }
    
    /**
     * 处理栏目修改
     */
    public function do_reply(){
    	//dump($_POST);
    	//exit;
        $m=D('Guestbook'); //先读取News数据库表模型文件
    	if (!$m->create()){
	    	$this->error($m->geterror());
	    }
    	//**需要另外添加到数据库的在这里填写
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
    	$m->gb_replytime=time();
    	$m->gb_dell=0;
    	$m->gb_reply=1;
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//**需要另外添加到数据库的在这里填写
    	 
    	$arr=$m->save(); //自动修改 不需要定义id 因为post表单中已经有
    	if ($arr){
    		$this->success('回复成功',U('Guestbook/index'));
    	}else {
    		$this->error('回复失败');
    		//$this->error($m->geterror());
    	}
    }
    
    /**
     * 显示添加留言页面
     */
    public function add() {
    	$this->display();
    }
    
    /**
     * 处理留言添加
     */
    public function do_add() {
    	//dump($_POST);
    	//exit;
    	$m=D('Guestbook'); //先读取News数据库表模型文件
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    
    	//**需要另外添加到数据库的在这里填写
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
    	$m->gb_addtime=time();
    	$m->gb_ip=get_client_ip();
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//**需要另外添加到数据库的在这里填写
    
    	$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
    	if ($arr){
    		$this->success('添加成功',U('Guestbook/index'));
    	}else {
    		$this->error('添加失败');
    		//$this->error($m->geterror());
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
    		
    	$m=D('Guestbook');
    	$data['gb_title']=array('like',"%{$keyword}%");
    	$arr=$m->where($data)->relation(true)->select();
    	//**分页实现代码
    	$count=count($arr);// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,3);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
    	$data['gb_title']=array('like',"%{$keyword}%");
    	$arr=$m->where($data)->relation(true)->limit($Page->firstRow.','.$Page->listRows)->select();
    	//dump($arr);
    	//exit;
    	if ($arr==null){
    		$this->error('没有数据');
    
    	}else {
    		//**分页实现代码
    		$this->assign('page',$show);// 赋值分页输出
    		//**分页实现代码
    		$this->assign('vlist',$arr); //在新查询到的数据再分配给前台模板显示
    		$this->assign('count',$count);
    		$this->display('index'); //指定模板
    	}
    
    }
    
    
    /**
     * 批量删除新闻处理
     */
    public function delall(){
    	//dump($_POST);
    	//exit;
    	$m=D('Guestbook'); //数据库表，配置文件中定义了表前缀，这里则不需要写
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
