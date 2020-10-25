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
use Common\Lib\String; //引入类函数
use Common\Lib\Common; //引入类函数
class AttributeController extends CommonController {
	/**
	 * 鉴定属性列表
	 */
    public function index(){
    	$m=M('Attr');
    	//**分页实现代码
    	$count=$m->count();// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
    	$arr=$m->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
    	//dump($arr);
    	//exit;

		$this->assign('vlist',$arr);
		$this->assign('count',$count);
    	$this->assign('page',$show);
    	$this->display();
    }
    
    
    /**
     * 显示鉴定添加页面
     */
    public function add(){
    	$this->display();
    }
    
    /**
     * 处理添加鉴定
     */
    public function do_add(){
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
    	if (I('post.id')==0){
    		$this->error('0编号属性不能添加');
    	}
    	$m=D('Attr'); //先读取News数据库表模型文件
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	
    	//**需要另外添加到数据库的在这里填写
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//**需要另外添加到数据库的在这里填写
    	
    	$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
    	if ($arr){
    		$this->success('添加成功',U('Attribute/index'));
    	}else {
    		$this->error('该编号已经存在');
    		//$this->error($m->geterror());
    	}
    	
    }
    /**
     * 显示鉴定属性修改
     */
    public function edit(){
    	
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
    	
    	$id=I('get.id');
    	$m=M('Attr');
    	$arr=$m->find($id);
    	//var_dump($arr);
    	$this->assign('cate',$arr);
    	$this->display();
    }
    /**
     * 处理鉴定属性修改
     */
    public function do_edit(){
    	//     	dump($_POST);
    	//     	exit;
    	$m=D('Attr');
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	
    	$data['id']=I('post.id');
    	$data['attr_name']=I('post.attr_name');
    	$data['attr_color']=I('post.attr_color');
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('修改成功！');
    	}
    	else {
    		$this->error('修改失败！');
    	}
    	
    }

    /**
     * 删除鉴定属性处理
     */
    public function del(){
    	
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
    	
    	$m=M('Attr');
    	$id=I('get.id');
    	$count=$m->delete($id);
    	if ($count>0){
    		$this->success('删除成功');
    	}
    	else {
    		$this->error('删除失败');
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
    	 
    	$m=M('Attr');
    	$data['attr_name']=array('like',"%{$keyword}%");
    	$arr=$m->where($data)->select();
    	//dump($arr);
    	//exit;
    	//**分页实现代码
    	$count=count($arr);// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
    	$m=M('Attr');
    	$data['attr_name']=array('like',"%{$keyword}%");
    	$arr=$m->where($data)->limit($Page->firstRow.','.$Page->listRows)->select();
    	//dump($arr);
    	//exit;

    	if ($arr==null){
    		$this->error('没有数据');
    
    	}else {
    		//**分页实现代码
    		$this->assign('page',$show);// 赋值分页输出
    		//**分页实现代码
    		$this->assign('vlist',$arr); //在新查询到的数据再分配给前台模板显示
    		$this->assign('count',$count); //在新查询到的数据再分配给前台模板显示
    		$this->display('index'); //指定模板
    	}
    
    }
    
    
    /**
     * 批量删除新闻处理
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
    	
    	$m=D('Attr'); //数据库表，配置文件中定义了表前缀，这里则不需要写
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
     * 显示鉴定属性id下的文章
     */
    public function news(){
    	//查询指定id的栏目信息
    	$id=I('get.id');//类别ID
    	$m=D('Attr');
    	
    	$data['g.id']= $id;
    	$field='g.id,g.attr_name,g.attr_color,i.news_id,i.attr_id,r.nv_id,r.news_title,r.news_hits,r.news_author,r.news_addtime,r.news_updatetime,r.news_sort,f.column_name';
    	$result=$m->alias('g')->join('LEFT JOIN tuzi_attr_news i ON i.attr_id = g.id')->join('LEFT JOIN tuzi_news r ON r.id = i.news_id')->join('LEFT JOIN tuzi_column f ON f.id = r.nv_id')->field($field)->order('r.news_sort desc')->where($data)->select();

//     	dump($result);
//     	exit;
    	//**分页实现代码
    	$count = count($result);// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码

    	$data['g.id']= $id;
    	$field='g.id,g.attr_name,g.attr_color,i.news_id,i.attr_id,r.nv_id,r.news_title,r.news_hits,r.news_author,r.news_addtime,r.news_updatetime,r.news_sort,f.column_name';
    	$result=$m->alias('g')->join('LEFT JOIN tuzi_attr_news i ON i.attr_id = g.id')->join('LEFT JOIN tuzi_news r ON r.id = i.news_id')->join('LEFT JOIN tuzi_column f ON f.id = r.nv_id')->field($field)->order('r.news_sort desc')->where($data)->limit($Page->firstRow.','.$Page->listRows)->select();

//     	    	dump($result);
//     	    	exit;
    	//循环截取字符 substr_ext函数写在commonaction.class.php中
    	foreach($result as $k2 => $v2){
    		$result[$k2]['news_title'] = Common::substr_ext($v2['news_title'], 0, 16, 'utf-8',"");
    	}
    	
    	//**分页实现代码
    	$this->assign('page',$show);// 赋值分页输出
    	$this->assign('count',$count);// 赋值分页输出
    	$this->assign('module',MODULE_NAME);// 赋值分页输出
    	//**分页实现代码
    	
    	$this->assign('vcolumn',$topcate);
    	$this->assign('vlist',$result);
    	$this->assign('nav',$id);
    	$this->display();	
    }
    
    
    
}
?>
