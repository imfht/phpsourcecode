<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Manage\Controller;
//use Think\Controller;
use Common\Lib\Category;
class CategoryController extends CommonController {
	/**
	 * 栏目首页显示
	 */
    public function index(){
    	import('Class.Category',APP_PATH);//该文件放在APP所在目录下的Class目录，当前项目路径 
    	$m=D('Column')->order('column_sort ASC','column_addtime ASC')->relation(true)->select();
    	$m=Category::unlimitedForLevel($m,'&nbsp;&nbsp;&nbsp;├─');

    	//关联查询模型的名字
    	foreach($m as $k3 => $v3){
    		$url=$v3['url'];
    		$mm=D('Model');
    		$data['model_table']=$url;
    		$arr=$mm->where($data)->select();
    		if ($v3['column_link']==1){
    			$m[$k3]['model_name'] = '<span style="color:#FF0000">外部链接</span>';
    		}
    		if ($v3['column_link']==2){
    			$m[$k3]['model_name'] = '内部链接';
    		}
    		
    		if ($v3['column_link']==0){
    			$m[$k3]['model_name'] = $arr['0']['model_name'];
    		}

    	}
    	//栏目链接重构
    	foreach($m as $k3 => $v3){
    		if ($v3['column_link']==1){
    			$m[$k3]['url'] = '';
    		}
    		if ($v3['column_link']==2){
    			$m[$k3]['url'] = '';
    		}
    		if ($v3['column_link']==0){
    			$m[$k3]['url'] = __APP__.'/'.MODULE_NAME.'/'.$v3['url'].'/'.index.'/'.'id'.'/'.$v3['id'];
    		}
    	}
//     	dump($m);
//     	exit;
    	$this->assign('vlist',$m);
    	$this->display();	
    }
    
    
    /**
     * 显示添加栏目
     */
    public function add(){
    	//显示所属栏目
    	$id=I('get.id');
    	if (!$id){
			$id='0';
    	}
    	//dump($id);
    	//exit;
    	$m=D('Column');//读取数据库模型model文件，关联模型。
    	$arr=$m->relation(true)->find($id);
    	//var_dump($arr);
    	//exit;
    	$this->assign('cate',$arr);
    	$this->assign('f_id',$id);
    	
    	//显示栏目所属模型
    	$m=M('Model')->select();
    	$this->assign('Modellist',$m);
    	
    	$this->display();
    }
    
    /**
     * 处理添加栏目
     */
    public function do_add(){
//     	dump($_POST);
//     	exit;
    	C('TOKEN_ON',false);//关闭表单令牌

    	$a=I('post.column_url');
    	$b=I('post.column_ename');
    	$c=I('post.column_link');
    	if ($c==1){
    		if ($a==''){
    			$this->error('外部链接需要填写');
    		}
    	}
    	
    	if ($c==2){
    		if ($b==''){
    			$this->error('别名需要填写');
    		}
    	}
    	
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
    	
    	$m=D('Column'); //先读取News数据库表模型文件
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	 
    	//**需要另外添加到数据库的在这里填写
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
    	$m->column_addtime=time();
    	$m->column_images=0;
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//**需要另外添加到数据库的在这里填写
    	 
    	$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
    	if ($arr){
    		$this->success('添加成功',U('Category/index'));
    	}else {
    		$this->error('添加失败');
    		//$this->error($m->geterror());
    	}
    	
    }
    /**
     * 显示栏目修改
     */
    public function edit(){
    	$id=I('get.id');
    	$m=D('Column'); //读取数据库模型model文件，关联模型。
    	$arr=$m->relation(true)->find($id);
    	//var_dump($arr);
    	//exit;
    	$this->assign('cate',$arr);
    	
    	//显示栏目所属模型
    	$m=M('Model')->select();
    	$this->assign('Modellist',$m);
    	
    	$this->display();
    }
    /**
     * 处理栏目修改
     */
    public function do_edit(){
        //dump($_POST);
    	//exit;
    	C('TOKEN_ON',false);//关闭表单令牌
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
    	
    	$m=D('Column');
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	 
    	//**需要另外添加到数据库的在这里填写
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
    	//$m->user_rsdate=time();
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
     * 更新排序处理
     */
    public function sortcate(){
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
    	
    	$m=M('Column');
    	foreach (I('post.') as $id=>$sort){
    		$m->where(array('id'=>$id))->setfield('column_sort',$sort);
    	}
    	$this->redirect('index');
    	
    	//dump($_POST);

    	$this->display();
    }
    
    
    /**
     * 删除栏目处理
     */
    public function delete(){
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
    	
    	//**删除栏目下文章操作
    	//查询指定id的栏目信息
    	$id=I('get.id');//类别ID
    	$topcate=M('Column')->where("id=$id")->order('column_sort')->select();
    	//dump($topcate);
    	//exit;
    	 
    	//查询所有栏目的信息
    	$m=M('Column')->order('column_sort')->select();
    	//dump($m);
    	//exit;
    	 
    	//查询指定id的栏目下的所有文章
    	foreach ($topcate as $k => $v){
    		$cids=Category::getChildsId($m, $v['id']);//传递一个父级分类ID返回所有子分类ID
    		$cids[]=$v['id'];//将父级id也压进来赋值给$cids
//     		dump($cids);
//     		exit;
    	
    		//查询数据，没有分页
    		$where=array('nv_id'=>array('IN', $cids));//查询新闻表nv_id字段和$cids相等时的数据
    		//$News=('News');
    		$topcate[$k]['news']=D('News')->field('id')->where($where)->where("news_dell=0")->select();
    		$result=$topcate[$k]['news'];
    		//查询新闻表下的所有文章   查询新闻数据赋值给字段news
    	}
    	//dump($result);
    	//exit;
    	//二维数组变一维数组
    	foreach ($result as $value){
    		$new_id[]=$value['id'];
    	}
//     	dump($new_id);
//     	exit;
    	
    	//删除栏目下的文章操作
    	$m=D('News'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	//判断id是数组还是一个数值
    	if(is_array($new_id)){
    		$where = 'id in('.implode(',',$new_id).')';
    		//implode() 函数返回一个由数组元素组合成的字符串
//     		dump($where);
//     		exit;
    		$data['news_dell']=1;
    		$count_News=$m->where($where)->save($data);//批量删除至回收站
    	}else{
    		$where = 'id='.$new_id;
    	}
    	

    	
    	
    	/**
    	 * 删除栏目及其子栏目操作
    	 */
    	$m=M('Column');
    	$id=I('get.id');
    	//dump($id);
    	//exit;
    	//查询指定id的栏目信息
    	$topcate=M('Column')->where("id=$id")->order('column_sort')->select();
//     	dump($topcate);
//     	exit;
    	$m=M('Column')->order('column_sort')->select();
    	//查询指定id的栏目下的所有文章
    	foreach ($topcate as $k => $v){
    		$id=Category::getChildsId($m, $v['id']);//传递一个父级分类ID返回所有子分类ID
    		$id[]=$v['id'];//将父级id也压进来赋值给$cids
    	}
//     	dump($id);
//     	exit;
    	
    	//判断id是数组还是一个数值
    	if(is_array($id)){
    		$where = 'id in('.implode(',',$id).')';
    		//implode() 函数返回一个由数组元素组合成的字符串
    	}else{
    		$where = 'id='.$id;
    	}
//     	dump($where);
//     	exit;
    	$count_Column=D('Column')->where($where)->delete(); //修改表单用save函数
    	//$count_Column=$m->where($where)->delete($id);
    	if ($count_Column>0){
    		$this->success('成功删除该栏目及所属文章',U('Category/index'),6);
    	}
    	else {
    		$this->error('删除失败');
    	}
    }

    /**
     * 批量删除栏目处理
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
    	
    	$m=D('Column'); //数据库表，配置文件中定义了表前缀，这里则不需要写
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
