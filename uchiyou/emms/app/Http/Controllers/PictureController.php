<?php
namespace App\Http\Controllers;


use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Util\PictureUtil;

/*
 * 上传和下载图片的控制类
 */
class PictureController extends Controller{
	
	/*
	 * 上传图片
	 */
	public function uploadPicture(Request $request){
		//判断请求中是否包含name=file的上传文件
		if(!$request->hasFile('picture')){
			exit('上传文件为空！');
		}
		$file = $request->file('picture');
		//判断文件上传过程中是否出错
		if(!$file->isValid()){
			return false;
		}
		$newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
		
		$savePath = PictureUtil::getPicBasePath().$newFileName;// 上传的文件保存到  storage/app/test/  目录下
		$bytes = Storage::put(
				$savePath,
				file_get_contents($file->getRealPath())
				);
		if(!Storage::exists($savePath)){
			return false;
		}
		return PictureUtil::removeDot($newFileName);
		//return $this->jsonResult(0,['url'=>$relatePath],'sucess');
	}
	/*
	 * 下载图片,与 PictureUtil 中上传图片相对应
	 * @param $url # 文件保存的相对路径，
	 * 即$savePath = PathUtil::getPicBasePath()/$relatePath = 用户值的的目录/数据库存储的路径
	 */
	public function downloadPicture($pictureName){
		$pictureName = PictureUtil::recoverDot($pictureName);
		header("Content-Type: ".Storage::mimeType(PictureUtil::getPicBasePath().$pictureName));// 设置响应头部的数据类型
		echo Storage::get(PictureUtil::getPicBasePath().$pictureName);
	}
}