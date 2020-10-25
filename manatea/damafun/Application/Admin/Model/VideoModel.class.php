<?php 
namespace Admin\Model;
use Think\Model;

Class VideoModel extends Model 
{
	//视频上传功能
	function uploadPV()
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



		$up1 = new FileUpload();

		$up1 ->set("path","./public/uploads/video/")
		->set("allowType",array('flv','avi','mp4',"wmv"))
		->set("maxSize",500000000)
		->set("israndname",true);

		if($up1->upload('path')){
			$name=$up1->getFileName();
			$tmp=explode(".",$name);
			if($tmp[1]=="mp4"){
				$_POST['path']=$tmp[0]."1.mp4";
				$_POST['pic']=$tmp[0]."1.jpg";
			}else{
				$_POST['path']=$tmp[0].".mp4";
				$_POST['pic']=$tmp[0].".jpg";
			}
			$ffmpegInstance = new ffmpeg_movie(ROOTPRO_PATH."/public/uploads/video/{$name}",false);
			$cuttime = $ffmpegInstance->getDuration()/10;//获取截图时间点为视频时长的1/10
			$vcodec = $ffmpegInstance->getVideoCodec();//获取视频编码
			$acodec = $ffmpegInstance->getAudioCodec();//获取音频编码
			$ffmpegInstance=""; 
			if($vcodec=="h264")
				$cmdv = "ffmpeg.exe -y -i  ".ROOTPRO_PATH."/public/uploads/video/{$name} -aspect 16:9 -vcodec copy ";//视频编码设置选择-vcodec copy
			else 
				$cmdv="ffmpeg.exe -y -i  ".ROOTPRO_PATH."/public/uploads/video/{$name} -aspect 16:9 -vcodec libx264 ";//".ROOTPRO_PATH."/public/uploads/video/{$_POST['path']}";
			if($acodec=="mpeg4aac")
				$cmdv.="-acodec copy -preset ultrafast ".ROOTPRO_PATH."/public/uploads/video/{$_POST['path']}";
			else
				$cmdv.=" -preset ultrafast ".ROOTPRO_PATH."/public/uploads/video/{$_POST['path']}";
			$cmdj="ffmpeg.exe  -i  ".ROOTPRO_PATH."/public/uploads/video/{$name}  -y -f image2 -ss {$cuttime}  -s 180*120 ".ROOTPRO_PATH."/public/uploads/images/{$_POST['pic']}";

			shell_exec($cmdv);
			shell_exec($cmdj);
			if(file_exists("./public/uploads/video/{$name}")){
				unlink("./public/uploads/video/{$name}");
			}
		}else {
			$this->setMsg($up1->getErrorMsg());
			return false;
		}
		if($this->insert()) {
			$this->saveXML($_POST['path']);
			return true;
		}else{
			if(file_exists("./public/uploads/video/{$name}")){

				unlink("./public/uploads/video/{$name}");
			}
			if(file_exists("./public/uploads/images/".$_POST["pic"])){

				unlink("./public/uploads/images/".$_POST["pic"]);
			}
			if(file_exists("./public/uploads/video/".$_POST["path"])){

				unlink("./public/uploads/images/".$_POST["path"]);
			}
			return false;
		}
	}
	//视频删除操作
	function delPV($id)
	{
		$ids = $this->where(array('id'=>$id))->select();
		foreach ($ids as $item) {
			if(file_exists("./public/uploads/images/".$item["pic"])){
				unlink("./public/uploads/images/".$item["pic"]);
			}
			if(file_exists("./public/uploads/video/".$item["path"])){
				unlink("./public/uploads/video/".$item["path"]);
			}
			if(file_exists("./public/uploads/video/info/".$item["path"].".xml"))
				unlink("./public/uploads/video/info/".$item["path"].".xml");
		}
		//if($this->where(array('id'=>$id))->r_delete(array("comment","vid"))){
		M('comment')->field('id')->where('vid = '.$id)->delete();
		if(M('video')->delete($id)){
 			return true;
		}else{
			$this->setMsg("删除视频失败");
			return false;
		} 

	}

	function saveXML($path){
		$xml = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<i>
<chatserver/>
<chatid/>
<mission>0</mission>
<source>k-v</source>
</i>
EOD;
	file_put_contents("./public/uploads/video/info/{$path}.xml",$xml);
	}
}

?>