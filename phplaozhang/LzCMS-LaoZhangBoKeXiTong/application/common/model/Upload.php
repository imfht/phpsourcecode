<?php
namespace app\common\model;

use think\Model;

/**
* 
*/
class Upload extends Model
{
	
	function initialize()
	{
		parent::initialize();
	}

	public function upfile($type,$filename = 'file',$is_water = false){
		// 获取表单上传文件 例如上传了001.jpg
		$file = request()->file($filename);
		// 移动到框架应用根目录/uploads/ 目录下
		$info = $file->move(ROOT_PATH . DS . 'uploads' . DS . $type);
		if($info){
			$path = DS . 'uploads' . DS . $type . DS .$info->getSaveName();
			//如果需要添加水印
			$setting = cache('settings');
			if($is_water && $setting['is_watermark'] && $setting['watermark'] && $type = 'images' ){
				$image = \think\Image::open(ROOT_PATH . $path);
				if($image->width() >= $setting['watermark_width'] && $image->height() >= $setting['watermark_height']){
					$image->water(ROOT_PATH . $setting['watermark'],$setting['watermark_locate'],$setting['watermark_alpha'])->save(ROOT_PATH . $path);
				}
			}
			$path=str_replace("\\","/",$path);
			return array('code'=>200,'msg'=>'上传成功','path'=>$path,'savename'=>$info->getSaveName(),'filename'=>$info->getFilename(),'info'=>$info->getInfo());
		}else{
			return array('code'=>0,'msg'=>$file->getError());
		}
	}

	public function upfiles($type,$filename = 'file',$is_water = false){
		// 获取表单上传文件 例如上传了001.jpg
		$files = request()->file($filename);
		$result = array('code'=>200,'msg'=>'',);
		foreach($files as $k => $file){
			// 移动到框架应用根目录/uploads/ 目录下
			$info = $file->move(ROOT_PATH . DS . 'uploads' . DS . $type);
			if($info){
				$path = DS . 'uploads' . DS . $type . DS .$info->getSaveName();
				//如果需要添加水印
				$setting = cache('settings');
				if($is_water && $setting['is_watermark'] && $setting['watermark'] && $type = 'images' ){
					$image = \think\Image::open(ROOT_PATH . $path);
					if($image->width() >= $setting['watermark_width'] && $image->height() >= $setting['watermark_height']){
						$image->water(ROOT_PATH . $setting['watermark'],$setting['watermark_locate'],$setting['watermark_alpha'])->save(ROOT_PATH . $path);
					}
				}
				$path=str_replace("\\","/",$path);
				$result['data'][$k] = array('code'=>200,'msg'=>'上传成功','path'=>$path,'savename'=>$info->getSaveName(),'filename'=>$info->getFilename(),'info'=>$info->getInfo());
			}else{
				$result['data'][$k] = array('code'=>0,'msg'=>$file->getError());
				$result['msg'] .= '第['.$k.']张'.$file->getError().' , ';
			}
		}
		if(empty($result['msg'])){
			$result['msg'] = '上传成功';
			$result['code'] = 200;
		}else{
			$result['msg'] = trim($result['msg'],',');
			$result['code'] = 100;
		}
		return $result;
	}

}