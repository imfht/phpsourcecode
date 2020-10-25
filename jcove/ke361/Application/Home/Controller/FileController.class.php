<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Home\Model\PictureModel;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */

class FileController extends HomeController {
	/* 文件上传 */
	public function upload(){
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		);

		/* 记录附件信息 */
		if($info){
			$return['data'] = think_encrypt(json_encode($info['download']));
		} else {
			$return['status'] = 0;
			$return['info']   = $File->getError();
		}

		/* 返回JSON数据 */
		$this->ajaxReturn($return);
	}

	/* 下载文件 */
	public function download($id = null){
		if(empty($id) || !is_numeric($id)){
			$this->error('参数错误！');
		}

		$logic = D('Download', 'Logic');
		if(!$logic->download($id)){
			$this->error($logic->getError());
		}
		
	}
	public function uploadAvatar(){
	    $files = $_FILES;
	    $setting  = C('AVATAR_UPLOAD');
	    $driver = C('PICTURE_UPLOAD_DRIVER');
	    $PictureM = new PictureModel();
	    $info = $PictureM->upload(
            $_FILES,
            C('AVATAR_UPLOAD'),
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$driver}_CONFIG")
        ); 
	    if ($info) { //文件上传成功，不记录文件
	        $return['status'] = 1;
	        if ($info['Filedata']) {
	            $return = array_merge($info['Filedata'], $return);
	        }
	        if ($info['download']) {
	            $return = array_merge($info['download'], $return);
	        }
	        /*适用于自动表单的图片上传方式*/
	        if ($info['file']) {
	            $return['data']['file'] = $info['file'];
	            $path = $info['file']['url'] ? $info['file']['url'] : $setting['rootPath'].$info['file']['savepath'].$info['file']['savename'];
	            $src = get_image_url($info['file']['path']);
	            // $return['data']['file']['path'] =;
	            $return['data']['file']['path'] =$path;
	            $return['data']['file']['src']=$src;
	            $size =  getimagesize($path);
	            $return['data']['file']['width'] =$size[0];
	            $return['data']['file']['height'] =$size[1];
	            $return['data']['file']['time'] =time();
	        }
	    } else {
	        $return['status'] = 0;
	        $return['info'] = $PictureM->getError();
	    }
	    $this->ajaxReturn($return);
	}
	//裁切图片
	public function cropPicture($crop = null,$path)
	{
	    //如果不裁剪，则发生错误
	    if (!$crop) {
	        $this->error('必须裁剪');
	    }
	    $driver = C('PICTURE_UPLOAD_DRIVER');
	    if (strtolower($driver) == 'local') {
	        //解析crop参数
	        $crop = explode(',', $crop);
	        $x = $crop[0];
	        $y = $crop[1];
	        $width = $crop[2];
	        $height = $crop[3];
	        //本地环境
	        $image = new \Think\Image();
	        $image->open($path);
	        //生成将单位换算成为像素
	        $x = $x * $image->width();
	        $y = $y * $image->height();
	        $width = $width * $image->width();
	        $height = $height * $image->height();
	        //如果宽度和高度近似相等，则令宽和高一样
	        if (abs($height - $width) < $height * 0.01) {
	            $height = min($height, $width);
	            $width = $height;
	        }
	        //调用组件裁剪头像
	        $image->crop($width, $height, $x, $y);
	        $image->save($path);
	        //返回新文件的路径
	        return  substr($path,1);
	    }else{
	        $name = get_addon_class($driver);
	        $class = new $name();
	        $new_img = $class->crop($path,$crop);
	        return $new_img;
	    }
	}
}
