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
class BannerController extends CommonController {
	/**
	 * 显示轮播广告
	 */
	public function index() {
    	$m=D('Column');
    	$arr=$m->relation(true)->where("column_link=0 AND f_id=0")->order('column_sort')->select();
    	//**分页实现代码
    	$count = count($arr);// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
    	$arr=$m->relation(true)->where("column_link=0 AND f_id=0")->order('column_sort')->limit($Page->firstRow.','.$Page->listRows)->select();
//     	dump($arr);
//     	exit;
    	foreach($arr as $k2 => $v2){
    		$arr[$k2]['column_imgsize'] = get_byte($v2['column_imgsize']);
    	}
    	$this->assign('page',$show);// 赋值分页输出
    	$this->assign('vlist',$arr);
    	$this->assign('count',$count);
        $this->display();
	}
	
	/**
	 * 处理轮播广告添加
	 */
	public function upload() {
// 		dump($_POST);
// 		exit;
		$file=$_FILES;
		$file=$file['column_images'];
		$file=$file['name'];
// 		dump($file);
// 		exit;
		if (empty($file)){
			$this->error('请先选择上传图片');
		}
		if (!empty($_FILES)) {
			$id=I('post.id');
			$m=M('Column');
			$arr=$m->find($id);
// 			dump($arr);
// 			exit;
			if (!$arr['column_images']==null){
				//删除本地图片附件 unlink('图片url')
				unlink('./Uploads/'.$arr["column_images"]);
			}
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
		$upload->savePath  	=  '/Img/'; // 网站主栏目图片 设置附件上传（子）目录
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
		
		$id=I('post.id');
		//**获取栏目的下级所有子栏目
		$m=D('Column')->select();
		$m=Category::getChilds($m,$id); //获取id所有的下级栏目的信息
		//将二维数组变成一维数组
		foreach ($m as $value){
			$data[]=$value['id'];
		}
		$data[]=$id;
		//dump($data);
		//exit;
		
		$m=D('Column'); //数据库表，配置文件中定义了表前缀，这里则不需要写
		//判断id是数组还是一个数值
		if(is_array($data)){
			$where = 'id in('.implode(',',$data).')';
			//implode() 函数返回一个由数组元素组合成的字符串
		}else{
			$where = 'id='.$data;
		}
		//dump($where);
		//exit;
		$date['column_images']=$image;
		$date['column_imgsize']=$size;
		$count=$m->where($where)->save($date); //修改表单用save函数
		if ($count>0){
			$this->success('上传图片成功！',U('Banner/index'));
		}
		else {
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
	 * 显示栏目广告修改页面
	 */
	public function edit() {
		$id=I('get.id');
		//dump($id);
		//exit;
    	$m=D('Column');
    	$arr=$m->find($id);
    	//dump($arr);
    	//exit;

    	$this->assign('v',$arr);
    	$this->display();
	}



	//开启关闭栏目广告处理
	public function ifedit() {
		$id=I('get.id');
		//dump($id);
		$m=M('Column');
		$arr=$m->find($id);
		//dump($arr);
		//exit;
		
		if ($arr['column_ifimg']==1){
			$id=I('get.id');
			//**获取栏目的下级所有子栏目
			$m=D('Column')->select();
			$m=Category::getChilds($m,$id); //获取id所有的下级栏目的信息
			//将二维数组变成一维数组
			foreach ($m as $value){
				$data[]=$value['id'];
			}
			$data[]=$id;
			//dump($data);
			//exit;
				
			$m=D('Column'); //数据库表，配置文件中定义了表前缀，这里则不需要写
			//判断id是数组还是一个数值
			if(is_array($data)){
				$where = 'id in('.implode(',',$data).')';
				//implode() 函数返回一个由数组元素组合成的字符串
			}else{
				$where = 'id='.$data;
			}
			//dump($where);
			//exit;
			$date['column_ifimg']=0;
			$count=$m->where($where)->save($date); //修改表单用save函数
			if ($count>0){
				$this->success('关闭成功！');
			}
			else {
				$this->error('关闭失败！');
			}
			 
		}else {
			$id=I('get.id');
			//**获取栏目的下级所有子栏目
			$m=D('Column')->select();
			$m=Category::getChilds($m,$id); //获取id所有的下级栏目的信息
			//将二维数组变成一维数组
			foreach ($m as $value){
				$data[]=$value['id'];
			}
			$data[]=$id;
			//dump($data);
			//exit;
				
			$m=D('Column'); //数据库表，配置文件中定义了表前缀，这里则不需要写
			//判断id是数组还是一个数值
			if(is_array($data)){
				$where = 'id in('.implode(',',$data).')';
				//implode() 函数返回一个由数组元素组合成的字符串
			}else{
				$where = 'id='.$data;
			}
			//dump($where);
			//exit;
			$date['column_ifimg']=1;
			$count=$m->where($where)->save($date); //修改表单用save函数
			if ($count>0){
				$this->success('开启成功！');
			}
			else {
				$this->error('开启失败！');
			}
		}

	}

}

?>