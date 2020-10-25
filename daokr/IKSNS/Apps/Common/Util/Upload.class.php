<?php

/**
 * Author : 小麦
 * 通用上传类
 */
namespace Common\Util;
class Upload {
		
	/*
	 * @param string $files  要上传的表单名称支持多文件
	 * @param string $savePath  形如：'article/' 保存路径
	 * @param string $thumb 缩略图可以是数组或者字符串逗号分隔 如果为空则 不创建缩略图
	 * @param string $sava_name_rule 要保存的文件名称规则
	 * @param string $cutmode 图片裁剪模式
	 * @param array  $exts  设置附件上传类型 默认是 'jpg', 'gif', 'png', 'jpeg'
	 * return 数据
	 * 
	 * 'name' => string '1.jpg' (length=5)
	 * 'type' => string 'image/jpeg' (length=10)
	 * 'size' => int 386868
	 * 'key' => string 'picfile' (length=7)
	 * 'ext' => string 'jpg' (length=3)
	 * 'md5' => string 'f051f4cbf18d99d9196f5d05bb8d7b27' (length=32)
  	 * 'sha1' => string 'cc6a006699f9d7d66112fd9f189dab440f2174f7' (length=40)
  	 * 'savename' => string 'c4ca4238a0b923820dcc509a6f75849b.jpg' (length=36)
  	 * 'savepath' => string 'face/000/00/00/' (length=15)
	 * 
	 * */
	public static  function saveLocalFile($savePath, $thumb = array(), $sava_name_rule= array(),$cutmode = 4, $exts = array()){
        //后缀名		
		$exts =  empty($exts) ? array('jpg', 'gif', 'png', 'jpeg') : $exts;
		//保存文件名称规则
		$sava_name_rule = empty($sava_name_rule) ? array('uniqid', '') : $sava_name_rule;
		
		//调用TP专用上传类
		$upload = new \Think\Upload(); // 实例化上传类
		$upload->maxSize = 1024*1024*2 ; // 设置附件上传大小 默认2M 1024*1024*2
		$upload->exts = $exts;// 设置附件上传类型
		$upload->autoSub = false; //自动子目录保存文件
		$upload->replace = true;  //存在同名是否覆盖
		$upload->savePath = $savePath; // 设置附件上传目录
		$upload->saveName = $sava_name_rule; // 保存文件名称规则
		
		$info   =   $upload->upload();
		
		$arrinfo = array();
		
		if(!$info) {
			return array('error'=>$upload->getError());
	    }else{
	    	$info = array_values($info);
	    	//保存成功开始切割
	    	foreach ($info as $keyinfo=>$patharr){
	    		$arrinfo[$keyinfo] = $patharr;
	    		//保存成功 判断是否需要切割
			    if(is_array($thumb) && !empty($thumb)){ 
			    	//宽度 高度
		    		$arrThumbWidth = explode(',',$thumb['width']);
					$arrThumbHeight = explode(',',$thumb['height']);
					
			    	//网站根目录
					$root_path = './Data/upload/';
					//源路径
					$imgDir  =  $root_path . $patharr['savepath'].$patharr['savename'];
					
					$arrfilename = explode('.', $patharr['savename']);
		
			    	foreach($arrThumbWidth as $key => $item){
			    		//新图片路径
			    		$new_img = $arrfilename[0].'_'.$item.'_'.$arrThumbHeight[$key].'.'.$patharr['ext'];
			    		$new_img_path = $root_path.$patharr['savepath'].$new_img;
		
			    		//开始切割图片
			    		$ic = new \Common\Util\ImageCrop($imgDir); 
			    		$ic->Crop($item, $arrThumbHeight[$key], $cutmode, $new_img_path);
						$ic->SaveImage();
						$ic->destory();
		
						$arrinfo[$keyinfo]['img_'.$item.'_'.$arrThumbHeight[$key]] = $patharr['savepath'].$new_img;
					}
					
			    }	    		
	    	}

	    	//计算是否是单个文件
	    	if(count($arrinfo) == 1){
	    		return $arrinfo[0];
	    	}
	    }
	    return $arrinfo;
	}
	
}
