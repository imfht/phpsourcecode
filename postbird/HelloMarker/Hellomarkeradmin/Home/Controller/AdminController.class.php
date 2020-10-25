<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends Controller {
    public function index(){
	   session(null);
       $this->display('index');
    }
    public function indexShow(){
    	$this->redirect('Index/indexShow');
    }
    public function login(){
    	$backFlag=0;
    	$adminName=$_POST['adminname'];
    	$adminPassword=md5($_POST['adminpassword']);
    	$sql="SELECT * FROM mk_admin WHERE adminname='".$adminName."';";
    	$adminRows=array();
    	$helloMarker=new \Think\Model();
    	$adminRows=$helloMarker->query($sql);
    	if(count($adminRows)==0 || $adminPassword != $adminRows[0]['adminpassword']|| $adminRows[0]['admingrade']==0){
 			$backFlag=1;
 			$backInfo="用户名或密码错误!";
 			$this->assign('backFlag',$backFlag);
 			$this->assign('backInfo',$backInfo);
 			$this->index();
 			return false;
 			exit();
    	}else{
    		session('hellomarkeradminlogin',1);
    		session('hellomarkeradminname',$adminName);
    		$this->indexShow();
    		exit();
    	}
    }
     public function logout(){
     	$this->index();
     	exit();
     }
}