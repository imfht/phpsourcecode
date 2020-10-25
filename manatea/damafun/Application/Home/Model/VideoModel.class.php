<?php 
namespace Home\Model;
use Think\Model;
require_once ROOTPRO_PATH.'/Public/api/ffmpegphp/FFmpegAutoloader.php';
Class VideoModel extends Model
{

	//视频上传功能
	public function uploadPV()
	{
	
		//这里上传文件是 前端form表单 需添加enctype属性="multipart/form-data"
		if($_POST['pid']=='0'){
			$this->setMsg("请选择一个分类添加");
			return false;
		}

/*		$up = new FileUpload(); 
		$up->set("path","./public/uploads/images/")
		->set("maxSize",5000000)
		->set("allowType",array('gif','png','jpg'))
		->set("israndname",true);

		if($up->upload('pic')){
			$img = new Image("public/uploads/images/");
			$imgname = $img->thumb($up->getFileName(),50,50,'');
			if(!$imgname){
				$this->setMsg("图片压缩错误");
				return false;
			}
			$_POST['pic']=$imgname;
		}else{
			$this->setMsg("图片上传错误：".$up->getErrorMsg());
			return false;
		}
		if($up1->upload('path')){
			$_POST['path']=$up1->getFileName();
		}
		else {
			$this->setMsg("视频上传错误：".$up1->getErrorMsg());
			if(file_exists("./public/uploads/images/".$_POST["pic"]))
				unlink("./public/uploads/images/".$_POST["pic"]);
			return false;
		}*/



		$up = new \Think\Upload();
		
		$up->maxSize = 2000000000;
		//$up->mimes = array(get_mime_type('flv'),get_mime_type('avi'),get_mime_type('mp4'),get_mime_type("wmv"));
		$up->exts = array('flv','avi','mp4','wmv');
		$up->rootPath = 'Public/uploads/';
		$up->savePath = 'video/';
		$up->saveName = array('date','YmdHis');
		$up->autoSub = false;
/* 		$up1 ->set("path","./public/uploads/video/")//设置视频上传路径
		->set("allowType",array('flv','avi','mp4',"wmv"))//设置允许上传最大文件
		->set("maxSize",100000000)//设置允许上传最大文件
		->set("israndname",true);//设置允许生成随机文件名 */

		if($file = $up->upload()){
	//		p($file);
			$name = $file['path']['savename'];
	//		echo $name;
			$tmp=explode(".",$name);
			if($tmp[1]=="mp4"){
				$_POST['path']=$tmp[0]."1.mp4";
				$_POST['pic']=$tmp[0]."1.jpg";
			}else{
				$_POST['path']=$tmp[0].".mp4";
				$_POST['pic']=$tmp[0].".jpg";
			}
			$ffmpegInstance = new \ffmpeg_movie(ROOTPRO_PATH."/Public/uploads/video/{$name}",false);
			$cuttime = $ffmpegInstance->getDuration()/10;//获取截图时间点为视频时长的1/10
			$vcodec = $ffmpegInstance->getVideoCodec();//获取视频编码
			$acodec = $ffmpegInstance->getAudioCodec();//获取音频编码
		//	echo ROOTPRO_PATH."/Public/uploads/video/{$name}";
		//	p($ffmpegInstance);
			$ffmpegInstance=""; 
			if(explode(' ',$vcodec)[0]=="h264")
				$cmdv = "ffmpeg.exe -y -i  ".ROOTPRO_PATH."/Public/uploads/video/{$name} -aspect 16:9 -vcodec copy ";//视频编码设置选择-vcodec copy
			else 
				$cmdv="ffmpeg.exe -y -i  ".ROOTPRO_PATH."/Public/uploads/video/{$name} -aspect 16:9 -vcodec libx264 ";//".ROOTPRO_PATH."/public/uploads/video/{$_POST['path']}";
			if(explode(' ',$acodec)[0]=="mpeg4aac")
				$cmdv.="-acodec copy -preset ultrafast ".ROOTPRO_PATH."/Public/uploads/video/{$_POST['path']}";
			else
				$cmdv.=" -preset ultrafast ".ROOTPRO_PATH."/Public/uploads/video/{$_POST['path']}";
			$cmdj="ffmpeg.exe  -i  ".ROOTPRO_PATH."/Public/uploads/video/{$name}  -y -f image2 -ss {$cuttime}  -s 180*120 ".ROOTPRO_PATH."/Public/uploads/images/{$_POST['pic']}";
			
	//		echo $cmdj;
	//		echo $cmdv;
	 		shell_exec($cmdv);
		    shell_exec($cmdj);
			if(file_exists("./Public/uploads/video/{$name}")){
				unlink("./Public/uploads/video/{$name}");
			}
		}else {
			$this->setMsg($up->getError());
			return false;
		}
		if($this->create()) {
			$this->add();
			$this->saveXML($_POST['path']);
			return true;
		}else{
			if(file_exists("./Public/uploads/video/{$name}")){

				unlink("./Public/uploads/video/{$name}");
			}
			if(file_exists("./Public/uploads/images/".$_POST["pic"])){

				unlink("./Public/uploads/images/".$_POST["pic"]);
			}
			if(file_exists("./Public/uploads/video/".$_POST["path"])){

				unlink("./Public/uploads/images/".$_POST["path"]);
			}
			return false;
		} 
	}



	private function saveXML($path){
		$xml = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<i>
<chatserver/>
<chatid/>
<mission>0</mission>
<source>k-v</source>
</i>
EOD;
	file_put_contents("./Public/uploads/video/info/{$path}.xml",$xml);
	}

	public function search(){
		
		$data = array_filter(explode(" ",$_GET['query']));
		$arr = array();
		$str = "";
		foreach ($data as $row) {
			$str.="name like '%{$row}%' or ";
		}
		$query = substr($str,0,strlen($str)-4);

		return $this->where($query)->select();


	}


}
?>