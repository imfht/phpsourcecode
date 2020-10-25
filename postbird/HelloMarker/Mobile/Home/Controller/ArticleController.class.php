<?php
namespace Home\Controller;
use Think\Controller;
class ArticleController extends AclController {
	public function index(){
		$this->allArticle();
		$this->display('index');
	}

	public function allArticle(){
		$helloMarker=new \Think\Model();
		$sql="SELECT * FROM mk_article ORDER BY articleTop DESC,articleid DESC";
		$articleRows=$helloMarker->query($sql);
		// echo "<pre>";
		// 	print_r($articleRows);
		// echo "</pre>";
		$this->assign('articleRows',$articleRows);
	} 

	public function article(){
		$helloMarker=new \Think\Model();
		$id=$_REQUEST['id'];
		$sql="SELECT * FROM mk_article where articleid=".$id;
		$articleRows=$helloMarker->query($sql);
		$articleError=0;
		if(count($articleRows)==0){
			$articleError=1;
			$this->assign('articleError',$articleError);
			$this->display('article');
			exit();
		}else{
			$articleRows[0]['articlecontent']=htmlspecialchars_decode($articleRows[0]['articlecontent']);
			$this->assign('articleRows',$articleRows);
			$this->display('article');
			exit();
		}


	}

}