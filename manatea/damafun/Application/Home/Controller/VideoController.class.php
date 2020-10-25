<?php 
namespace Home\Controller;
use Think\Controller;
class VideoController extends Controller
{
	//视频播放页面
	public function index()
	{
		$video = D("video");
		$cat = D("cat")->where("pid = 0")->limit(5)->select();
		$data = $video->find($_GET['vid']);
		$comment = D("comment")->query("select c.*,u.name from cz_comment c,cz_user u where c.vid = %d and c.uid = u.id",$_GET['vid']);
		
		//若视频存在，则访问量+1
	 	if($data){
			$video->where(array('id'=>$_GET['vid']))->setInc("hot",1);
			$user = D("user")->where("id = {$data['uid']}")->find();
			//获取该视频的评分详情
			$recom = D('recom')->getInformation($_GET['vid']);
		}
		
		//检测用户是否已经评分
		if(in_array($_SESSION['user']['id'],$recom)){
			$this->assign("recomed",true);
		}
		
		//获取视频相关推荐
		$recom = D('recom');
		$recominfo=$recom->topMatches($recom->getInformation(),$_GET['vid'],4);
		if($recominfo){
			$recomvideo=M('video')->where(array('id'=>array('in',array_keys($recominfo))))->select();
		}
		
		$this->assign("cat",$cat);
		$this->assign("video",$data);
		$this->assign("user",$user);
		$this->assign("comment",$comment);
		$this->assign('recom',$recomvideo);
		$this->display(); 
	}

	//视频上传
	public function add(){

		$cat = D("cat")->where("pid = 0")->limit(5)->select();
		$select = D("cat")->selectform();
		$this->assign("select",$select);
		$this->assign("cat",$cat);
		$this->display();
	}

	public function upload(){

		$video = D("video");
		$_POST['ptime']=time();

		//这步骤用于判断所添加的视频必须为子类视频
		$pid = $video->query("select id from cz_cat where id not in(select pid from cz_cat);");
		$tmp = array();
		foreach ($pid as $row) {
			array_push($tmp, $row['id']);
		}
		if(in_array($_POST['pid'], $tmp)){
		if($video->uploadPV()) 
			$this->success("添加成功",__MODULE__."/index/index");
		else
			$this->error($video->getMsg(),__MODULE__."/video/add");
		}
		else $this->error("必须选择子类存放视频",__MODULE__."/video/add");
 		//$video->uploadPV();

	}

	//发送弹幕
	public function sendDama(){
		if(isset($_POST['cont'])){
			if($_SESSION['userLogin']&&$_SESSION['user']['allow']){
				$url = "./Public/uploads/video/info/{$_POST['vpath']}.xml";
				$cont = I('post.cont','','strip_tags');
				$playTime = $_POST['playTime'];
 				$dom = new \DOMDocument('1.0');
				$dom->formatOutput = true;
				$dom->load($url);
				$element = $dom->createElement("d",$cont);
				$attribute = $dom->createAttribute("p");
				$attribute->value= $playTime.",1,25,16777215,".time().",0,D263bd64,30725023,{$_SESSION['user']['name']},{$_SESSION['user']['id']}";
				$element->appendChild($attribute);	
				$dom->getElementsByTagName("i")->item(0)->appendChild($element);
				$dom->save($url);	 
				exit('1');
				
			}
		}
		exit(0) ;
	}

	public function sendComment(){
		if(isset($_POST['cont'])){
			$comment = D("comment");
			$_POST['time']=time();
			$_POST['comment']=I('post.comment');
			$comment->create();
			if($cid = $comment->add()){
				D('video')->where(array('id'=>$_POST['vid']))->setInc('comnumber',1);
				$user = D("user")->field("name")->find($_POST['uid']);
				$arr = array("cid"=>$cid,"name"=>$user['name'],"time"=>date("Y-m-d H:i:s"),"comment"=>$_POST['comment']);
				exit (json_encode($arr));
			} 
		}
		exit(0) ;
	}
	
	/*
	 * 设置用户评分（AJAX传输）
	 * 
	 */
	public function setRating(){
		if(isset($_POST['cont'])){
			$uname=I('post.username');
			$vid=I('post.vid');
			$rating = I('post.rating');
			$recom = D("recom")->field('per')->where(array('vid'=>$vid))->find();
			if($recom){
				$data['per']="{$recom['per']},{$uname}:{$rating}";
				M("recom")->where(array('vid'=>$vid))->save($data);
			}else{
				$data['vid']=$vid;
				$data['per']="{$uname}:{$rating}";
				M("recom")->add($data);
			}
			exit("评论成功！！");
		}
	}
	

}
?>