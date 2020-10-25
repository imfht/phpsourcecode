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
class PhotoController extends CommonController {
	/**
	 * 显示该父级栏目及其子级栏目所有文章
	 */
    public function index(){
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
    		//dump($cids);
    		//exit;
    		
    		//查询数据，没有分页
    		$where=array('nv_id'=>array('IN', $cids));//查询新闻表nv_id字段和$cids相等时的数据
    		//$News=('News');
    		$topcate[$k]['news']=D('News')->where($where)->where("news_dell=0")->relation(true)->select();
    		$result=$topcate[$k]['news'];
    		
    		//数据分页开始
    		$count = count($result);// 查询满足要求的总记录数
    		$Page = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    		$show = $Page->show();// 分页显示输出
    		//数据分页结束
    		
    		$where=array('nv_id'=>array('IN', $cids));//查询新闻表nv_id字段和$cids相等时的数据
    		//$News=('News');
    		$topcate[$k]['news']=D('News')->where($where)->where("news_dell=0")->relation(true)->limit($Page->firstRow.','.$Page->listRows)->order('news_sort,id desc')->select();
    		$result=$topcate[$k]['news'];
    		//dump($result);
    		//exit;
    	}
    	
    	//循环截取字符 substr_ext函数写在commonaction.class.php中
    	foreach($result as $k2 => $v2){
    		$result[$k2]['news_title'] = Common::substr_ext($v2['news_title'], 0, 16, 'utf-8',"");
    	}
    	//     	dump($result);
    	//     	exit;

    	
    	//**分页实现代码
    	$this->assign('page',$show);// 赋值分页输出
    	$this->assign('count',$count);// 赋值分页输出
    	//**分页实现代码
    	
    	$this->assign('vcolumn',$topcate);
    	$this->assign('module',MODULE_NAME);
    	$this->assign('vlist',$result);
    	$this->assign('nav',$id);
    	$this->display();
    }
    
    
    /**
     * 显示文章视图
     */
    public function add(){
    	
    	$id=I('get.nav');
    	//文章所属分类
    	$m=M('Column')->order('column_sort ASC')->select();

    	$m=Category::unlimitedForLevel($m,'&nbsp;&nbsp;├─');
    	//$m=Category::unlimitedForlayer($m,'cate');
    	//$m=Category::getParents($m,21);
    	//dump($m);
    	//exit;
    	$this->assign('cate',$m);
    	
    	//文章属性
    	$attr=M('Attr')->select();
    	$this->assign('flagtypelist',$attr);
    	$this->assign('nav',$id);
    	
    	$this->display();
    }
    
    /**
     * 处理添加文章
     */
    public function do_add(){
    	//dump($_POST);
    	//exit;
        	
    	$m=D('News'); //读取Message表的model模型文件MeesageModel.class.php
    	
    	//自动创建  不需要接收表单    	
    	if (!$m->create()){
    		$this->error($m->geterror());
    	}
    	
    	//**需要另外添加到数据库的在这里填写 
    	//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库	
    	//$m->news_addtime=time();
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
    	//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
    	//**需要另外添加到数据库的在这里填写
    	
    	
    	$arr=$m->relation(true)->add();
    	if ($arr){
    		$this->success('新增成功');
    	}else {
    		$this->error('新增失败');
    	}
    	
    }
    /**
     * 显示文章修改
     */
    public function edit(){
    	$id=I('get.id');
    	$m=D('News');//读取数据库模型model文件，关联模型。
    	$arr=$m->relation(true)->find($id);
    	//dump($arr);
    	//exit;
    	
    	//文章属性
    	$attr=M('Attr')->select();
    	$this->assign('flagtypelist',$attr);
    	
    	//显示所属栏目
    	$m=M('Column')->order('column_sort ASC')->select();
    	$m=Category::unlimitedForLevel($m,'&nbsp;&nbsp;├─');
    	$this->assign('Columnlist',$m);
    	
    	$this->assign('cate',$arr);
    	$this->display();
    }
    /**
     * 处理栏目修改
     */
    public function do_edit(){
    	//dump($_POST);
    	//exit;
        $m=D('News'); //读取Message表的model模型文件MeesageModel.class.php	
    	$m->create();//自动创建  不需要接收表单
    	
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
     * 更新排序处理
     */
    public function sortcate(){
    	$m=M('Column');
    	foreach (I('post.') as $id=>$sort){
    		$m->where(array('id'=>$id))->setfield('column_sort',$sort);
    	}
    	$this->redirect('index');
    	
    	//dump($_POST);

    	$this->display();
    }
    
    /**
     * 显示回收站新闻
     */
    public function trach(){
    	$m=D('News');
    	$arr=$m->relation(true)->where("news_dell=1")->order('news_addtime desc')->select();
    	//显示被删除news_dell=1的数据
    	//dump($arr);
    	//exit;
    	
    	$this->assign('vlist',$arr);
    	$this->display();
    }
    
    /**
     * 删除新闻至回收站处理
     */
    public function do_trach(){

        $m=M('News'); //数据库表，配置文件中定义了表前缀，这里则不需要写
        $data['id']=I('get.id');
    	$data['news_dell']=1;
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('删除至回收站成功！');
    	}
    	else {
    		$this->error('删除至回收站失败！');
    	}
    
    }
    
    
    /**
     * 回收站新闻还原处理
     */
    public function to_trach(){
    
    	$m=M('News'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	$data['id']=I('get.id');
    	$data['news_dell']=0;
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('还原成功！','__URL__/index');
    	}
    	else {
    		$this->error('还原失败！');
    	}
    
    }
    
    
    /**
     * 删除新闻处理
     */
    public function delete(){
    	$m=M('News');
    	$id=I('get.id');
    	$count=$m->delete($id);
    	if ($count>0){
    		$this->success('删除成功');
    	}
    	else {
    		$this->error('删除失败');
    	}
    }
  
}
?>
