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
use Common\Lib\Common; //引入类函数
use Common\Lib\String; //引入类函数
use Think\Upload;
class AdvertController extends CommonController {
	/**
	 * 显示轮播广告
	 */
	public function index() {
		//查询指定id的栏目信息
		$id=I('get.id');//类别ID
		$topnav=M('Adnav')->where("id=$id")->select();
// 		dump($topnav);
// 		exit;
		 
		//查询指定id的栏目下的所有文章
		foreach ($topnav as $k => $v){
			//查询数据，没有分页
			$where['advert_nav'] = $id;
			$topnav[$k]['news']=D('Advert')->where($where)->order('advert_sort')->relation(true)->select();
			$result=$topnav[$k]['news'];
		}
		//**分页实现代码
		$count = count($result);// 查询满足要求的总记录数
		$Page = new \Think\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page->show();// 分页显示输出
		//**分页实现代码
		//查询指定id的栏目下的所有文章
		foreach ($topnav as $k => $v){
			//查询数据，没有分页
			$where['advert_nav'] = $id;
			$topnav[$k]['news']=D('Advert')->where($where)->relation(true)->limit($Page->firstRow.','.$Page->listRows)->order('advert_sort,id desc')->select();
			$result=$topnav[$k]['news'];
		}
		
		//循环截取字符 substr_ext函数写在commonaction.class.php中
		foreach($result as $k2 => $v2){
			$result[$k2]['advert_name'] = Common::substr_ext($v2['advert_name'], 0, 12, 'utf-8',"");
		}
		
		foreach($result as $k2 => $v2){
			$result[$k2]['advert_size'] = get_byte($v2['advert_size']);
		}

// 				dump($result);
// 				exit;
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('vlist',$result);
		$this->assign('nav',$id);
		$this->assign('count',$count);
		$this->display();
		
	}
	
	/**
	 * 处理轮播广告添加
	 */
	public function upload() {
		C('TOKEN_ON',false);//关闭表单令牌
// 		dump($_POST);
// 		exit;
		$file=$_FILES;
		$file=$file['advert_image'];
		$file=$file['name'];
		if (empty($file)){
			$this->error('请先上传图片附件');
		}

		//读取模型表看看是否需要过滤 允许提交
		$m=D('Advert');
		if (!$m->create()){
			$this->error($m->geterror());
		}
		if (!empty($_FILES)) {
			//如果有文件上传 上传附件
			$this->_upload();
		}
	}
	
	/**
	 * 文件上传
	 */
	protected function _upload() {
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize 	= 3145728;// 设置附件上传大小
		$upload->exts    	= array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath	= './Uploads/';// 设置附件上传根目录
		$upload->savePath  	=  '/Images/'; // 设置附件上传（子）目录
		$upload->autoSub 	= true;
		$upload->subName 	= array('date','Ymd');
		$upload->saveName = array('uniqid','');//设置上传文件规则
		$info = $upload->upload();
		//设置需要生成缩略图，仅对图像文件有效
		//$upload->thumb              = true;
		//设置需要生成缩略图的文件后缀
		//$upload->thumbPrefix        = 'm_,s_';  //生产2张缩略图
		//设置缩略图最大宽度
		//$upload->thumbMaxWidth      = '400,100';
		//设置缩略图最大高度
		//$upload->thumbMaxHeight     = '400,100';
		
		//$upload->thumbRemoveOrigin  = true;//删除原图
		if (!$info) {
			//捕获上传异常
			$this->error($upload->getErrorMsg());
		} else {
			//取得成功上传的文件信息
			//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
			//Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], APP_PATH.'Tpl/Public/Images/logo.png');
			//dump($uploadList[0]);
			//exit;
			foreach($info as $file){
				$image = $file['savepath'].$file['savename'];
				$size = $file['size'];
			}
		}
// 		dump($info);
// 		exit;
		//写入到数据库中
		$m=D('Advert'); //先读取News数据库表模型文件
		if (!$m->create()){
			$this->error($m->geterror());
		}
		
		//**需要另外添加到数据库的在这里填写
		//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
		$m->advert_time=time();
		$m->advert_image=$image;
		$m->advert_size=$size;
		
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//**需要另外添加到数据库的在这里填写
		
		$nav=I('post.advert_nav');
		
		$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
		if ($arr){
			//$this->success('上传图片成功！');
			$this->success("上传图片成功", U('Advert/index', array('id' => $nav)));
		}else {
			$this->error('上传图片失败!');
			//$this->error($m->geterror());
		}

	}
	
	
	/**
	 * 显示首页轮播广告
	 */
	public function do_advert() {
		$m=D('Advert');	
		//**分页实现代码
		$count=$m->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page->show();// 分页显示输出
		//**分页实现代码
	
		$arr=$m->order('user_rsdate')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($arr);
		//exit;
	
		//**分页实现代码
		$this->assign('page',$show);// 赋值分页输出
		//**分页实现代码
		$this->assign('vlist',$arr);
		$this->display();
	}

	
	/**
	 * 显示添加轮播广告页面
	 */
	public function add() {
		$nav=I('get.nav');
// 		dump($nav);
// 		exit;
		
		//文章所属分类
		$m=M('Adnav')->order('id ASC')->select();
		//dump($m);
		//exit;
		$this->assign('cate',$m);
		$this->assign('nav',$nav);
		$this->display();
	}
	
	/**
	 * 显示广告分类
	 */
	public function adnav() {
		$m=D('Adnav');
		$arr=$m->order('id')->select();
		//**分页实现代码
		$count = count($arr);// 查询满足要求的总记录数
		$Page = new \Think\Page($count,11);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page->show();// 分页显示输出
		//**分页实现代码
		$arr=$m->order('id')->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		// 		dump($arr);
		// 		exit;
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('vlist',$arr);
		$this->assign('count',$count);
		$this->assign('module',MODULE_NAME);
		$this->display();
	}
	/**
	 * 广告栏目编辑控制器方法
	 */
	public function adnavedit() {
		$id=I('get.id');
// 		dump($id);
// 		exit;
		$m=D('Adnav');
		$arr=$m->find($id);
		$count = count($arr);// 查询满足要求的总记录数
// 				dump($arr);
// 				exit;
		$this->assign('v',$arr);
		$this->assign('count',$count);
		$this->assign('module',MODULE_NAME);
		$this->display();
	}
	
	/**
	 * 处理广告编辑
	 */
	public function do_adnavedit() {
// 	    dump($_POST);
//     	exit;
    	$m=D('Adnav'); //数据库表，配置文件中定义了表前缀，这里则不需要写
    	$data['id']=I('post.id');
    	$data['adnav_name']=I('post.adnav_name');
    	$count=$m->save($data); //修改表单用save函数
    	if ($count>0){
    		$this->success('修改成功！',U('Advert/adnav'));
    	}
    	else {
    		$this->error('修改失败！');
    	}
	}
	/**
	 * 广告栏目删除
	 */
	public function adnavdel() {
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
    	
    	$m=M('Adnav');
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
	 * 显示添加广告分类
	 */
	public function addsort() {
		$this->display();
	}
	/**
	 * 处理添加广告分类
	 */
	public function do_addsort() {
// 		dump($_POST);
// 		exit;
	    $m=D('Adnav'); //先读取News数据库表模型文件
    	if (!$m->create()){
	    	$this->error($m->geterror());
	    }
		
		//**需要另外添加到数据库的在这里填写
		//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
		//$m->adnav_time=time();
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//**需要另外添加到数据库的在这里填写
		
		$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
		if ($arr){
			$this->success('添加成功,刷新可见！',U('Advert/adnav'));
		}else {
			$this->error('添加失败');
			//$this->error($m->geterror());
		}
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
	 * 处理修改
	 */
	public function do_edit() {
		C('TOKEN_ON',false);
// 		dump($_POST);
// 		exit;
		
		$file=$_FILES;
		$file=$file['advert_image'];
		$file=$file['name'];
// 		dump($file);
// 		exit;
		if (empty($file)){
				$m=D('Advert');
				if (!$m->create()){
					$this->error($m->geterror());
				}
				$data['id']=I('post.id');
				$data['advert_nav']=I('post.advert_nav');
				$data['advert_name']=I('post.advert_name');
				$data['advert_remark']=I('post.advert_remark');
				$data['advert_url']=I('post.advert_url');
				$data['advert_sort']=I('post.advert_sort');
				$data['advert_show']=I('post.advert_show');
				
				$count=$m->save($data); //修改表单用save函数
				if ($count>0){
					$this->success('修改成功！');
				}
				else {
					$this->error('修改失败！');
				}
		}else {
			$id=I('post.id');
			$m=M('Advert');
			$arr=$m->find($id);
// 			dump($arr);
// 			exit;
			if (!$arr['advert_image']==null){
				//删除本地图片附件 unlink('图片url')
				unlink('./Uploads'.$arr["advert_image"]);
			}
			
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize 	= 3145728;// 设置附件上传大小
			$upload->exts    	= array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath	= './Uploads/';// 设置附件上传根目录
			$upload->savePath  	=  '/Images/'; // 网站主栏目图片 设置附件上传（子）目录
			$upload->autoSub 	= true;
			$upload->subName 	= array('date','Ymd');
			$upload->saveName = array('uniqid','');//设置上传文件规则
			$info = $upload->upload();
			//设置需要生成缩略图，仅对图像文件有效
			//$upload->thumb              = true;
			//设置需要生成缩略图的文件后缀
			//$upload->thumbPrefix        = 'm_,s_';  //生产2张缩略图
			//设置缩略图最大宽度
			//$upload->thumbMaxWidth      = '400,100';
			//设置缩略图最大高度
			//$upload->thumbMaxHeight     = '400,100';
			
			//$upload->thumbRemoveOrigin  = true;//删除原图
			if (!$info) {
				//捕获上传异常
				$this->error($upload->getErrorMsg());
			} else {
				//取得成功上传的文件信息
				//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
				//Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], APP_PATH.'Tpl/Public/Images/logo.png');
				//dump($uploadList[0]);
				//exit;
			
				foreach($info as $file){
					$image = $file['savepath'].$file['savename'];
					$size = $file['size'];
				}
			}
// 			dump($size);
// 			exit;
			
			$m=M('Advert');
			$data['id']=I('post.id');
			$data['advert_nav']=I('post.advert_nav');
			$data['advert_name']=I('post.advert_name');
			$data['advert_remark']=I('post.advert_remark');
			$data['advert_url']=I('post.advert_url');
			$data['advert_show']=I('post.advert_show');
			$data['advert_sort']=I('post.advert_sort');
			
			//$date['advert_time']=time();
			
			$data['advert_image']=$image;
			$data['advert_size']=$size;
			
			$count=$m->save($data); //修改表单用save函数
// 			dump($count);
// 			exit;
			if ($count>0){
				$this->success('修改成功！');
			}
			else {
				$this->error('修改失败!');
				//$this->error($m->geterror());
			}
		}
	}



	/**
	 * 删除轮播广告处理
	 */
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
		$nav=I('get.nav');
		$m=M('Advert');
		foreach (I('post.') as $id=>$sort){
			$m->where(array('id'=>$id))->setfield('advert_sort',$sort);
		}
		$this->success("更新成功", U('Advert/index', array('id' => $nav)));
	}

}

?>