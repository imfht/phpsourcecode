<?php
namespace Home\Controller;
use Think\Controller;
	class IndexController extends Controller{
		//首页显示页面
		public function index(){
/*			echo "<b>欢迎使用《细说PHP》中的BroPHP框架1.0, 第一次访问时会自动生成项目结构：</b><br>";
			echo '<pre>';
			echo file_get_contents('./runtime/_damafun_index.php');
			echo '</pre>';*/
			$cat = D("cat")->where(array('pid'=>'0'))->limit(5)->select();
			$video = D("video")->order("hot desc")->limit(6)->select();
			$recom = D('recom');
// 			p(I('session.user')['id']);

			//若用户登陆，则获取用户有关视频推荐
			if(I('session.user')){
				$recominfo = $recom->getInformation();
				$recomed=$recom->getRecommendedItems($recom->transformPrefs($recominfo),$recom->calculateSimilarItems($recominfo),I('session.user')['id']);
				if($recomed){
					$recomvideo = M('video')->where(array('id'=>array('in',array_keys($recomed))))->select();
				}
			}
			
// 			p($recomvideo);
			$this->assign("video",$video);
			$this->assign("cat",$cat);
			$this->assign("recom",$recomvideo);
			$this->display();
		}	

		//分类显示页面
		public function forward(){

			//当前分类
			$nowcat = D("cat")->where(array('id'=>$_GET['cat']))->find();
			$arr = D("cat")->getChildCatId($_GET['cat']);
			$tmp = D("cat")->getChildCat($_GET['cat']);
			//解决视频，用户联表查询
			$str = '';
			foreach ($arr as $row) {
				$str.="$row,";
			}

			$str = substr($str,0,strlen($str)-1);
			//$video = D("video")->where(array("pid"=>$arr))->select();
			//$user = D("user")->r_select(array('video',null,'uid',array('video',null,null,'pid')));
			
			//$video = D("video")->query("select v.*,u.id uid,u.name uname from cz_user u,cz_video v where u.id=v.uid and v.pid in({$str}) ","select",$arr);
			$video =D('video')->alias('v')->field('v.*,u.id uid,u.name uname')->join("JOIN __USER__ u ON u.id=v.uid and v.pid in ({$str})")->select();
			$cat = D("cat")->where("pid = 0")->limit(5)->select();

			//当前分类下的所有视频
			$this->assign("video",$video);
			//导航的前五个根分类
			$this->assign("cat",$cat);
			//所有分类下的子分类
			$this->assign("scat",$tmp); 
			//当前分类
			$this->assign("nowcat",$nowcat);
			$this->display(); 
		}

		//搜索页面
		public function isearch(){

			$video = D("video");
			$cat = D("cat")->where("pid = 0")->limit(5)->select();
			$data = $video->search();
			$this->assign("cat",$cat);
			$this->assign("data",$data);
			$this->display();

		}
		public function showCat(){
			$video = D("video");
			$cat = D("cat")->where("pid = 0")->limit(5)->select();
			$data = D("cat")->select();
			foreach ($data as $key=>$value){
				$row = M('video')->where("pid = {$value['id']}")->select();
				$data[$key]['video']=$row;
			}
			//$data = D("cat")->r_select(array("video",null,"pid",array("video",null,'2',true)));
			$this->assign("data",$data);
			$this->assign("cat",$cat);
			$this->display();
		}
	}