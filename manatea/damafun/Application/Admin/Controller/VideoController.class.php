<?php 
namespace Admin\Controller;
use Think\Controller;
/**
* 
*/
class VideoController extends CommonController
{
	
	public function index()
	{
		$video = D("video");
		$data = $video->query("select cz_video.id,cz_cat.name pname, cz_video.name name, cz_user.name uname,pic,ptime,hot,comnumber from cz_video,cz_cat,cz_user where cz_video.pid=cz_cat.id and cz_video.uid=cz_user.id","select");
		$this->assign("data",$data);
		$this->display();
	}

/* 	//视频添加界面
	public function add(){
		$this->assign("select",D("Cat")->selectform());
		$this->display();
	} */	

	//视频修改界面
	public function mod(){
		$video = D("video");
		$data = $video->find($_GET['id']);
		$this->assign("select",D('cat')->selectform('pid',$data['pid'],$data['id']));
		$this->assign("data",$data);
		$this->display();
	}	

	//视频修改操作
	public function update(){
		$video = D("video");

		//这步骤用于判断所添加的视频必须为子类视频
		$pid = $video->query("select id from cz_cat where id not in(select pid from cz_cat);","select");
		$tmp = array();
		foreach ($pid as $row) {
			array_push($tmp, $row['id']);
		}
		if(in_array($_POST['pid'], $tmp)){

			if($video->where(array('id'=>$_POST['id']))->save($_POST))
				$this->success("修改成功",__MODULE__.'/video/index');
			else {
				$this->error("修改失败",__MODULE__."/video/index/id/<{$_POST['id']}>");
			}
		}
		else $this->error("必须选择子类存放视频",__MODULE__."/video/index/id/<{$_POST['id']}>");
	}

	//弹幕管理
	public function dama(){
		$data = D('video')->field('id,path')->find($_GET['id']);
		$xml = simplexml_load_file("./Public/uploads/video/info/{$data['path']}.xml");
		$arr = array();
		foreach ($xml->d as $obj) {
			$attributes = explode(",", $obj->attributes());
			$tmp = array($obj,$attributes);
			array_push($arr,$tmp);
		}
		$this->assign("vid",$data['id']);
		$this->assign("data",$arr);
		$this->display();

	}
	//删除弹幕
	public function deldama(){
 		$data = D('video')->field('id,path')->find($_GET['vid']);
		$doc = new \DOMDocument();
		$doc -> formatOutput = true;
		$doc -> load("./public/uploads/video/info/{$data['path']}.xml");
		$root = $doc->documentElement;
		$elms = $root->getElementsByTagName('d');
		foreach ($elms as $elm) {
			$attributes = explode(",",$elm->getAttribute('p'));
			if($attributes[0] == $_GET['time']){
				if($root->removeChild($elm)){
					$doc -> save("./public/uploads/video/info/{$data['path']}.xml");
					$this->success("删除成功",__MODULE__."/video/dama/id/{$_GET['vid']}");
				}
				else $this->error("删除失败",__MODULE__."/video/dama/id/{$_GET['vid']}");
			}
		} 
	}

	//视频评论管理
	public function comment(){
		$video = D("video")->find($_GET['id']);
		$data = D("comment")->query("select u.id uid,c.id id,u.name,c.comment,c.time from cz_user as u,cz_comment as c where c.uid = u.id and c.vid = {$video['id']}","select");
		$cat = D("cat")->where("id = {$video['pid']}")->find();
		$this->assign("video",$video);
		$this->assign("data",$data);
		$this->assign("cat",$cat);
		$this->display();
	}

	//删除评论
	public function delcom(){
		$num = M('comment')->field('vid')->find($_GET['id']);
		if(D("comment")->delete($_GET['id'])){
			M('video')->where(array('id'=>$num['vid']))->setDec('comnumber',1);
			$this->success("删除成功",__MODULE__."/video/comment/id/{$_GET['vid']}");
		}else{
			$this->error("删除失败",__MODULE__."/video/comment/id/{$_GET['vid']}");
		}

	}
/* 	//视频添加操作
	public function insert(){
		$video = D("video");
		$_POST['ptime']=time();

		//这步骤用于判断所添加的视频必须为子类视频
		$pid = $video->query("select id from cz_cat where id not in(select pid from cz_cat);","select");
		$tmp = array();
		foreach ($pid as $row) {
			array_push($tmp, $row['id']);
		}
		if(in_array($_POST['pid'], $tmp)){
		if($video->uploadPV()) 
			$this->success("添加成功","index");
		else
			$this->error($video->getMsg(),"video/add");
		}
		$this->error("必须选择子类存放视频","video/add");
	}
 */
	//视频删除操作
	public function delete(){
		$video = D('video');
 		if($video->delPV($_GET['id'])) 
			$this->success("删除成功",__CONTROLLER__."/index");
		else
			$this->error($video->getMsg()); 
	}
}
 ?>