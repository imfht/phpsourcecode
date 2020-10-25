<?php
namespace Home\Controller;


class UcenterController extends UserbaseController{

	public function _initialize($userid){
		$this->userid=I('uid',$_SESSION['zs_home']['user_auth']['uid'],'int');
		parent::_initialize($userid);
	}
	
	//系统首页
    public function index(){
    	
       $uid=is_login();
       $map['id']=$uid;
       if(M('userexp')->where($map)->count()>0){
       $expinfo=M('userexp')->where($map)->find();
       $this->assign('expinfo',$expinfo);
       }
       $this->display();
    }

    public function useravatarset(){
        $this->display();
    }
    public function userfocus(){
       $this->display();
    }
    public function usertagfocus(){
       $this->display();
    }
    public function usersc(){
       $this->display();
    }
    public function changepwd(){
	$this->display();
    }
    public function yzmail(){
	$this->display();
    }
    public function usermail(){
	
	if(is_login()==0){
		$this->error('尚未登录');
	}
	
    $p=I(C('VAR_PAGE'));
	$map['to_uid']=is_login();
	$map['status']=1;
	
	$maillist=M('Message')->where($map)->page(!empty($p)?$p:1,10)->select();
	$mailcount=M('Message')->where($map)->count();
	
	$PAGE = new \Think\Page($mailcount, 10);
	$messpage=$PAGE->show();
	$this->assign('messpage',$messpage);
	$map['is_read']=0;
	$readmailcount=M('Message')->where($map)->count();
    $this->assign('maillist',$maillist);
    $this->assign('mailcount',$mailcount);
    $this->assign('readmailcount',$readmailcount);   

    
    
    $this->display();
}
public function mailread(){
	
	$id=I('get.id');
	
	$res=M('message')->where(array('id'=>$id))->setField('is_read',1);
	if(!$res){
		$this->error('标记失败！');
	}else{
	    $this->success('标记成功！','',array('id'=>$id));	
	}
	
}
public function delmail(){
	
	$id=I('get.id');
	
	$res=M('message')->where(array('id'=>$id))->delete();
	if(!$res){
		$this->error('删除失败！');
	}else{
	    $this->success('删除成功！','',array('id'=>$id));	
	}
	
}
public function usersendmail(){

	    $userid=I('uid',0);
        if(is_login()==0){
	    	$this->error('请先登录！',U('User/login'));
	    	
	    	
	    }
	    if($userid==0){
	    	$this->error('非法操作！',U('Index/index'));
	    	
	    	
	    }
        if($userid==is_login()){
	    	$this->error('自己给自己不用发私信了，直接发送脑电波！',U('Index/index'));
	    	
	    	
	    }
         $this->assign('to_uid',$userid); 
         $this->assign('from_uid',is_login()); 
         
         $map['to_uid']=array('in',array(is_login(),$userid));
          $map['from_uid']=array('in',array(is_login(),$userid));
          $map['type']=1;
          $map['status']=1;
          $tomaillist=M('Message')->where($map)->order('create_time desc')->select();
       foreach ($tomaillist as $key =>$vo){
       
       	$tomaillist[$key]['fromuser']=query_user(array('avatar64','space_url','nickname'),$vo['from_uid']);
       	//	$tomaillist[$key]['touser']=query_user(array('avatar64','space_url','nickname'),$vo['to_uid']);
       	
       	
       }
          $this->assign('tomaillist',$tomaillist); 
       
         
        $this->display();
    }
public function sendmess(){
	
	
	
if (false === $data= D('message')->create()) {

	$this->error(D('message')->getError());
}
if($data['title']==''){
	$data['title']=time();
}
$list = D('message')->add($data);
if ($list !== false) {
	$this->success('发送私信成功!');

} else {
	$this->error(0,'发送私信失败!');
}
}  
public function userart(){
if($this->userid==$_SESSION['zs_home']['user_auth']['uid']){
	$userart='5,2,1';
}else{
	$userart='1';
}
	$this->assign('userart',$userart);
	
        $this->display();
    }
public function artadd(){

	if(IS_POST){
		$input= new \OT\Input();
		 
		$input->noGPC();
	
		$uid=$_SESSION['zs_home']['user_auth']['uid'];
		if(!$uid>0){
			$this->error('请先登录');
		}
		if (false === $data= D('Article')->create()) {

			
			$this->error(D('Article')->getError());
		}
		if($data['cid']==null){
			
			$this->error('分类为空');
		}
		
		$data['description']=op_h(I('description'));
		
		
	   if(mb_strlen(op_h($data['description']),'utf-8')<30){
			$this->error('文章内容必须大于30字');
		}
	   if(mb_strlen($data['title'],'utf-8')>80){
			$this->error('文章标题必须小于80字');
		}
		
		foreach ($data['tag'] as $key =>$vo){
			
			
			$data['tag'][$key]=mb_substr($vo, 0, 15, 'utf-8');
		}
		
		
		D('Tags')->InsertTags($data['tag'],1);
		$data['tag']=implode(',',$data['tag']);
		
	
		
		
	    
	    
	   
	    //$this->apiError(0,$data['description']);
	    
		
		
		//$data['description']=$data['description'];
		$data['title']=op_t($data['title']);
		$data['uid']=$uid;
		$status=$data['status'];
		$data['copyright']=str_replace('{title}',$data['title'] , $data['copyright']);
		$data['copyright']=str_replace('{url}',$_SERVER["HTTP_HOST"], $data['copyright']);
		$data['copyright']=str_replace('{name}',C('WEB_SITE') , $data['copyright']);
		//保存当前数据对象
		$list =D('Article')->add($data);
		if ($list !== false) {
		
			if($status==1){
			setuserscore($uid, C('ARTSCORE'));
			}
			clean_query_user_cache($uid,array('artnum'));
			$this->success('添加文章成功!',U('Ucenter/userart'));

		} else {
			$this->error('添加文章失败!');
		}
		
		
		
	}else{
		
		
		$uid=is_login();
        //判断如果是后台管理员则不受限制
		if(is_admin($uid) == false){
        $roleauth=getmroleauth($uid);
		
		// $setting['exts'] =$roleauth['fileext'];
		 
		 $extsarr=explode(',', $roleauth['fileext']);
		
		if(!empty($extsarr)){
		 $extstr='';	
		foreach ($extsarr as $key1 =>$vo1){
			
			
			$extstr .= '*.'.$vo1.';';
		}
		$this->assign('extstr',$extstr);
		}
		
		 
		
		if($roleauth['yesart'] != 1){
			
			 $this->error('你无权投稿！');
			
		}
		}
		 $this->display();
	}
      
              
       
}
public function artedit(){

	
	
	if(IS_POST){
		
	    $input= new \OT\Input();
     
         $input->noGPC();
		
		
		$uid=$_SESSION['zs_home']['user_auth']['uid'];
		if(!$uid>0){
			$this->error('请先登录');
		}
		if (false === $data= D('Article')->create()) {

			
			$this->error(0,D('Article')->getError());
		}
		$data['description']=op_h(I('description'));
	if($data['cid']==null){
			
			$this->error('分类为空');
		}
		
		if(mb_strlen(op_h($data['description']),'utf-8')<30){
			$this->error('文章内容必须大于30字');
		}
	   if(mb_strlen($data['title'],'utf-8')>80){
			$this->error('文章标题必须小于80字');
		}
	    //$data['tag']=op_t($data['tag']);
		foreach ($data['tag'] as $key =>$vo){
				
				
			$data['tag'][$key]=mb_substr($vo, 0, 15, 'utf-8');
		}
		 D('Tags')->InsertTags($data['tag'],1,$data['id']);
		 
       
		 
		$data['tag']=implode(',',$data['tag']);
		
	
		
		
	   
	    
	    
		//$data['description']=$data['description'];
		
		$data['title']=op_t($data['title']);
		$data['uid']=$uid;
		$status=$data['status'];
		
		$ystatus=D('Article')->where(array('id'=>$data['id']))->getField('status');
		
		//保存当前数据对象
		$list = D('Article')->save($data);
		if ($list !== false) {
		   if($status==1&&$ystatus!=1){
			setuserscore($uid, C('ARTSCORE'));
			}
			$this->success('编辑文章成功!',U('Ucenter/userart'));

		} else {
			$this->error('编辑文章失败!');
		}
		
		
		
	}else{
		
		$uid=is_login();
		
		$id=I('id');
		
		if(!is_admin($uid)){

		 $roleauth=getmroleauth($uid);
		
		// $setting['exts'] =$roleauth['fileext'];
		 
		 $extsarr=explode(',', $roleauth['fileext']);
		
		if(!empty($extsarr)){
		 $extstr='';	
		foreach ($extsarr as $key1 =>$vo1){
			
			
			$extstr .= '*.'.$vo1.';';
		}
		$this->assign('extstr',$extstr);
		}
		
			
			
	    if(!getarteditauth($id,$uid)){
			
			
			 $this->error('无权编辑该文章或编辑时间已过','',false,true);
			
		}
		
		
		}
		$info=callApi('Art/getArtInfo',array($id));
		$info['data']['description']= stripcslashes($info['data']['description']);
		
		$this->assign('info',$info['data']);
		 $this->display();
	}
      
              
       
}
    public function doCropAvatar($crop)
    {
    	$crop=I('crop');
        //调用上传头像接口改变用户的头像
        $result = callApi('User/applyAvatar', array($crop));
       
        $this->ajaxReturn($result);

      
    }

    public function doUploadAvatar()
    {
        //调用上传头像接口
        $result = callApi('User/uploadTempAvatar');//result就是数组
       
        $this->ajaxReturn($result);
       
    }

}