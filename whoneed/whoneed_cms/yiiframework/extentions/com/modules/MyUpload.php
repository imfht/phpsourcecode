<?php
/**
 * @desc
 * 文件上传类
 *
 * @author
 * Huyf
 */
class MyUpload extends CComponent
{
		static public $file;
		/*
		 * 上传图片，生成缩略图
		 */
		static public function upload_image($filed_name,$uid) {
			if(!empty($_FILES[$filed_name]['name']) && $_FILES[$filed_name]['error'] === UPLOAD_ERR_OK)
			{
				self::$file = $_FILES[$filed_name];
				$upload = CUploadedFile::getInstanceByName($filed_name);
				if($upload)
				{
					// 获取文件后辍名
					$ext = explode('.', self::$file['name']);
					$ext = array_pop($ext);
					
					// 过滤不是.png、.gif、.jpg、.jpeg结尾的文件
					if(!in_array($ext, array('png', 'jpg', 'jpeg', 'gif','mp3')))
					{
						 $result = '';
					}
					else {
						$date = date('Ymd') ;
						$img = "b".$uid._.time().rand(1,100).".".$ext ;
						$thumb_img = "s".$uid._.time().rand(1,100).".".$ext ;
						$date = date('Ymd') ;
						$upload_dir = IMG_ROOT."/upload/".$date ;
							
						// 文件名
						if(!file_exists($upload_dir))
						{
							mkdir($upload_dir);
						}
						if(!file_exists($upload_dir."/photo"))
						{
							mkdir($upload_dir."/photo");
						}
						//生成原图
						$upload->saveAs($upload_dir."/photo/".$img);
							
						//生成缩略图
						$image = Yii::app()->image->load($upload_dir."/photo/".$img);
						$image->resize(220, 150);
						$image->save($upload_dir."/photo/".$thumb_img);
						
						$result = array('img'=>"/upload/".$date."/photo/".$img,'thumb_img'=>"/upload/".$date."/photo/".$thumb_img);
					}
				}
			}
			else
			{
				$result = '';
			}
			return $result ;
		}
		
		/*
		 * 上传头像
		*/
		static public function upload_header_photo($filed_name,$uid) {
			if(!empty($_FILES[$filed_name]['name']) && $_FILES[$filed_name]['error'] === UPLOAD_ERR_OK)
			{
				self::$file = $_FILES[$filed_name];
				$upload = CUploadedFile::getInstanceByName($filed_name);
				if($upload)
				{
					// 获取文件后辍名
					$ext = explode('.', self::$file['name']);
					$ext = array_pop($ext);
						
					// 过滤不是.png、.gif、.jpg、.jpeg结尾的文件
					if(!in_array($ext, array('png', 'jpg', 'jpeg', 'gif','mp3')))
					{
						$result = '';
					}
					else {
						$date = date('Ymd') ;
						$img = "b".$uid._.time().rand(1,100).".".$ext ;
						$thumb_img = "s".$uid._.time().rand(1,100).".".$ext ;
						$date = date('Ymd') ;
						$upload_dir = IMG_ROOT."/upload/".$date ;
							
						// 文件名
						if(!file_exists($upload_dir))
						{
							mkdir($upload_dir);
						}
						if(!file_exists($upload_dir."/photo"))
						{
							mkdir($upload_dir."/photo");
						}
						//生成原图
						$upload->saveAs($upload_dir."/photo/".$img);
							
						//生成缩略图
						$image = Yii::app()->image->load($upload_dir."/photo/".$img);
						$image->resize(220, 150);
						$image->save($upload_dir."/photo/".$thumb_img);
		
						$result = array('img'=>"/upload/".$date."/photo/".$img,'thumb_img'=>"/upload/".$date."/photo/".$thumb_img);
					}
				}
			}
			else
			{
				$result = '';
			}
			return $result ;
		}
}