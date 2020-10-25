<?php
namespace Home\Controller;
use Think\Controller;
class BugController extends AclController {
    public function index(){
        $this->display('index');
    }
    public function bugWork(){
    	$backFlag=0;
    	$bugName=$_POST['bugname'];
    	$bugTime=Date('Y-m-d');
    	$bugText=$_POST['bugtext'];
    	$bugContact=$_POST['bugcontact'];
    	if(strlen($bugName)==0 || strlen($bugText)==0){
    		$backFlag=1;
    		$backInfo="反馈内容不能为空，请重新填写！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->index();
    		return 0;
    		exit();
    	}
    	if(strlen($bugContact)==0){
    		$bugContact="未留联系方式！";
    	}
    	
    	$helloMarker=new \Think\Model();
    	$sql="INSERT INTO mk_bug (bugname,bugtime,bugtext) VALUES ('".$bugName."','".$bugTime."','".$bugText."');";
    	if($helloMarker->execute($sql)){
    		$backFlag=0;
    		$backInfo="您的意见我已经收到，非常感谢您的支持！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->redirect('index');
    		return 0;
    	}else{
    		$backFlag=1;
    		$backInfo="由于网络原因，未能收到您的反馈，非常抱歉！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->index();
    		return 0;
    	}                                                                                                                                                                                                                                            
    }
}