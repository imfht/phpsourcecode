<?php
namespace Home\Controller;


class UserbaseController extends HomeController{

	//系统首页
    public function _initialize($userid){
      parent::_initialize();
     // header("Cache-control:no-cache,no-store,must-revalidate");
      //header("Pragma:no-cache");
     // header("Expires:0");
      //C('HTTP_CACHE_CONTROL','no-cache, no-store');
      
      //dump($_SERVER);
        $userid=I('uid',$_SESSION['zs_home']['user_auth']['uid'],'int');
        //判断进入个人中心的是谁，两种情况，一种没有参数，直接访问，可以访问自己的个人中心，带参数则是访问别人的个人中心
        //如果访问别人的个人中心，是可以访问的，但是不能发给对方发私信，在消息哪里提示登录
      	if($_SESSION['zs_home']['user_auth']['uid']<1){
        $this->error('你还未登录，请登录后重试！',U('User/login'));
      	}
        
      	if($userid==0||$_SESSION['zs_home']['user_auth']['uid']==$userid){
      		$cxuid=$_SESSION['zs_home']['user_auth']['uid'];
      		$ucenter=true;
      		$userart='5,2,1';
      	}else{
      	
      		$cxuid=$userid;
      		$ucenter=false;	
      		$userart='1';
      	}
      	
      	$cxuser=query_user(array('pos_province','pos_community','pos_city','pos_district','zan','fensi','focusnum','scartnum','tagfocusnum','supportnum','score','signature','commentnum','artnum','allartnum','email','uid','username','nickname','reg_time','space_url','last_login_time','avatar32', 'avatar64', 'avatar128', 'avatar256'),$cxuid);
      	
     
      	
      	
      		$hasfocususer=hasguanzhu($cxuid, $_SESSION['zs_home']['user_auth']['uid'], 0);
      	
      	$this->assign('hasfocususer',$hasfocususer);
      	
      	
      	
      	$map['uid']=$cxuid;
      	$map['status']=1;
      	$map['tag']=array('neq','null');
      	
      	$tagarr=array_filter(M('article')->where($map)->getField('tag',true));
      	
      	if($tagarr!=null){
      		
      		$taglist=implode(',',$tagarr);
      		
      	$taglistcache=explode(',', $taglist);
      	
      	$taglistcache=array_unique($taglistcache);
      
      	foreach ($taglistcache as $key =>$vo){
      		
      		
      		$usertaglist[$key]=gettaginfobytitle($vo);
      		
      		
      	}
      		
      		
      	}
      	
      	$usertaglist=array_filter($usertaglist);
      	
      	
      	
      	
      	$this->assign('usertaglist',$usertaglist);
      	
      	
      	
      	
      	timetonow();
      	$this->assign('cxuid',$cxuid);
      	 $this->assign('ucenter',$ucenter);
      	 $this->assign('cxuser',$cxuser);
       $this->assign('userid',$userid);
       
       $this->assign('webdescription','用户中心');
    	$this->assign('webkeyword','用户中心');
    	$this->assign('webtitle','用户中心');
       
    }
   public function yzmail(){
	
   	$uid=is_login();
   	
   	$mailuid=think_decrypt(I('uid'));
   	
   	if($uid!=$mailuid){
   		
   		$this->error('非法验证操作或验证已超时',U('Index/index'));
   		
   	}else{
   		
   		$map['id']=$mailuid;
   		$res=M('userexp')->where($map)->find();
   		if($res!=''){
   			
   			$data['id']=$mailuid;
   			$data['email']=think_decrypt(I('mail'));
   			M('userexp')->save($data);
   			
   		}else{
   			
   			$data['id']=$mailuid;
   			$data['email']=think_decrypt(I('mail'));
   			M('userexp')->add($data);
   		}
   		
   		M('ucenter_member')->where($map)->setField('email',think_decrypt(I('mail')));
   		
   		$this->success('邮箱验证通过',U('Ucenter/index'));
   		
   		
   	}
   	
   	
   	
	
   }
    
}