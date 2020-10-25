<?php
namespace app\index\controller;
use app\common\controller\HomeBase;



class Index extends  HomeBase
{

	
	
	public function _initialize()
	{
		parent::_initialize();

	}
	
   public function index(){
  


   	$uid=session('member_info')['id'];
   	
   	if($uid>0){
   		
   		$userdoclistcount = model('doccon')->where(['uid'=>$uid,'status'=>1])->count();
   		
   		$info=parent::$commonLogic->getDataInfo('user',['id'=>$uid]);
   		$this->assign('userinfo',$info);
   		$this->assign('userdoccount',$userdoclistcount);
   		
   		
   	}

   	$slideimgs=parent::$commonLogic->getDataList('slideimg',['status'=>1]);
   	$this->assign('slideimgs',$slideimgs);
   	
   	
   	
   	$alldoccount = model('doccon')->where(['status'=>1])->count();
   	$alldoccount=$alldoccount;
   	$bit = 9;//产生7位数的数字编号
   	$num_len = strlen($alldoccount);
   	$zero = '';
   	for($i=$num_len; $i<$bit; $i++){
   		$zero .= "0";
   	}
   	$real_num = $zero.$alldoccount;
   	
   	$countarr = str_split($real_num);
   	
   	
   	
   	
   	$this->assign('countarr',$countarr);
   	$this->assign('alldoccount',$alldoccount);
   	
   	
   	$doczdrank=parent::$commonLogic->getDataList('doccon',['m.status'=>1,'m.settop'=>1], 'm.*,user.username,user.nickname,user.grades,file.savename,file.ext,user.status as userstatus,user.statusdes', 'm.choice desc,m.create_time desc',false,[['file','m.fileid=file.id','LEFT'],['user','m.uid=user.id','LEFT']],'',2);
   	$this->assign('doczdrank',$doczdrank);
   	
   	//精品文库列表已经改为最新文档
   	$choicedoclist = parent::$commonLogic->getDataList('doccon',['status'=>1,'choice'=>1], true, 'create_time desc',false,'','',10);
   
		
   	$this->assign('choicedoclist',$choicedoclist);
   	//最新
   	$newdoclist = parent::$commonLogic->getDataList('doccon',['m.status'=>1], 'm.*,user.username,user.nickname,user.grades,file.savename,file.ext,user.status as userstatus,user.statusdes', 'm.create_time desc',false,[['file','m.fileid=file.id','LEFT'],['user','m.uid=user.id','LEFT']],'',10);
	 
   	
   	$this->assign('newdoclist',$newdoclist);
   	//热门文库列表
   	$hotdoclist = parent::$commonLogic->getDataList('doccon',['m.status'=>1], 'm.*,user.username,user.nickname,user.grades,file.savename,file.ext,user.status as userstatus,user.statusdes', 'm.view desc,m.create_time desc',false,[['file','m.fileid=file.id','LEFT'],['user','m.uid=user.id','LEFT']],'',10);
   	
   	
   	
   	$this->assign('hotdoclist',$hotdoclist);
   	//公告列表
   	$gglist = parent::$commonLogic->getDataList('article',['status'=>1,'tid'=>3], true, 'create_time desc',false,'','',3);
   	 
   	
   	$this->assign('gglist',$gglist);
   	
   	
   	
   	$yzm_list = parse_config_array('yzm_list');//1\注册2\登录3\忘记密码4\后台登录
   	
   	if(in_array(2, $yzm_list)){
   		 
   		$yzm=1;
   		 
   	}else{
   		 
   		$yzm=0;
   		 
   	}
   	$this->assign('yzm',$yzm);
   	
   	//$groupcatelist=self::$groupcateLogic->getGroupcateList('', true, 'sort desc');
   	
   	$groupcatelist=parent::$commonLogic->getDataList('groupcate',['status'=>1],true,'sort desc',false);
   	
   	foreach($groupcatelist as $k => $v){
   		
   		
   		$groupcatelist[$k]['child']=parent::$commonLogic->getDataList('doccate',['pid'=>$v['id']],true,'sort desc',false);
   		
   	}
   	$this->assign('catelist',$groupcatelist);
   
   $xscount=model('docxs')->where(['status'=>2,'cnid'=>array('gt',0)])->count();
   $this->assign('xscount',$xscount);
   //悬赏列表
   $xslist = parent::$commonLogic->getDataList('docxs',['m.status'=>1], 'm.*,user.username,user.nickname,doccate.name as tidname,groupcate.name as gidname', 'm.create_time desc',false,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id'],['groupcate','m.gid=groupcate.id']],'',6);
    
   
   $this->assign('xslist',$xslist);
   $userxslist = parent::$commonLogic->getDataList('docxs',['m.status'=>2,'m.cnid'=>array('gt',0)], 'm.*,doccon.xsid,doccon.uid as duid,doccon.id as did', 'm.update_time desc',false,[['doccon','m.cnid=doccon.id','LEFT']],'',3);
   $this->assign('userxslist',$userxslist);
   
   $zhoutime=time()-7*24*60*60;
   //认证用户文库列表
   $rzuserdoclist = parent::$commonLogic->getDataList('doccon',['m.status'=>1,'m.uid'=>array('neq',1)], 'm.uid,sum(m.raty) as sumraty,count(m.id) as dcount,sum(m.down) as sumdown,user.username,user.nickname,user.userhead,user.grades,user.description as udes,user.status as userstatus,user.statusdes', 'dcount desc',false,[['user','m.uid=user.id and user.status=3']],'m.uid',1);
   $zhourzuserdoclist = parent::$commonLogic->getDataList('doccon',['m.status'=>1,'m.uid'=>array('neq',1)], 'm.uid,count(m.id) as dcount,sum(m.down) as sumdown,user.username,user.nickname,user.status as userstatus,user.statusdes', 'dcount desc',false,[['user','m.uid=user.id and user.status=3']],'m.uid',5);
   
   $this->assign('zhourzuserdoclist',$zhourzuserdoclist);
   $this->assign('rzuserdoclist',$rzuserdoclist);
 
   
   if($rzuserdoclist){
   	
   	$raty['raty']=array('gt',0);
   	$raty['uid']=$rzuserdoclist[0]['uid'];
   $ratycount=	model('doccon')->where($raty)->count();
   	if($ratycount>0){
   		$rzuserdoclist[0]['raty'] =	round($rzuserdoclist[0]['sumraty']/$ratycount,2);
   	}else{
   		$rzuserdoclist[0]['raty'] =	0;
   	}
  
   $rzuserdoclistarr=getpingfen($rzuserdoclist[0]['raty']);
   $this->assign('rzuserdoclistarr',$rzuserdoclistarr);
   }
  
   
   
   
   
   //非认证用户文库列表
   $norzuserdoclist = parent::$commonLogic->getDataList('doccon',['m.status'=>1,'m.uid'=>array('neq',1)], 'm.uid,sum(m.raty) as sumraty,count(m.id) as dcount,sum(m.down) as sumdown,user.username,user.nickname,user.userhead,user.grades,user.description as udes,user.status as userstatus,user.statusdes', 'dcount desc',false,[['user','m.uid=user.id and user.status>0']],'m.uid',1);
   $zhounorzuserdoclist = parent::$commonLogic->getDataList('doccon',['m.status'=>1,'m.uid'=>array('neq',1)], 'm.uid,count(m.id) as dcount,sum(m.down) as sumdown,user.username,user.nickname,user.status as userstatus,user.statusdes', 'dcount desc',false,[['user','m.uid=user.id and user.status>0']],'m.uid',5);
  
   if($norzuserdoclist){
   	
   	$raty['raty']=array('gt',0);
   	$raty['uid']=$norzuserdoclist[0]['uid'];
   	$ratycount=	model('doccon')->where($raty)->count();
   	if($ratycount>0){
   		 $norzuserdoclist[0]['raty']=	round($norzuserdoclist[0]['sumraty']/$ratycount,2);
   	}else{
   		 $norzuserdoclist[0]['raty']=	0;
   	}
   	
  
   $norzuserdoclistarr=getpingfen($norzuserdoclist[0]['raty']);
   $this->assign('norzuserdoclistarr',$norzuserdoclistarr);
   }

   
   $this->assign('zhounorzuserdoclist',$zhounorzuserdoclist);
   $this->assign('norzuserdoclist',$norzuserdoclist);
   
   	return $this->fetch();
   	
   }
   public function yzemailurl($id){
   	if (is_login()==0) {
   
   		$this->error('亲！请登录',url('index/login'));
   	} else {
   		$uid = is_login();
   		 
   		 
   		$user=parent::$commonLogic->getDataInfo('user',['id'=>$uid]);
   		 
   		if($id==md5($user['salt'].$uid.$user['usermail'])){
   			if($user['status']<3){
   
   					
   				parent::$commonLogic->setDataValue('user',['id'=>$uid],'status',2);
   			}else{
   					
   				parent::$commonLogic->setDataValue('user',['id'=>$uid],'status',5);
   			}
   
   			$this->success('验证成功',url('user/index'));
   				
   				
   		}else{
   			$this->error('非法验证',url('user/index'));
   		}
   		 
   	}
   	 
   	 
   	 
   	 
   }
   public function yzemail(){
   	 
   	$mail=$this->request->param();
   
   	$uid =  is_login();
   	$user=parent::$commonLogic->getDataInfo('user',['id'=>$uid]);
   	 
   	$emailinfo=parent::$commonLogic->getDataInfo('user',['usermail'=>$mail['email'],'id'=>array('neq',$uid)]);
   	if($emailinfo){
   		return json(array('code'=>0,'msg'=>'该邮箱已经被其他账号注册'));
   	}else{
   		$n['usermail']=$mail['email'];
   		$n['id']=$uid;
   		parent::$commonLogic->dataEdit('user',$n,false);
   		 
   
   		 
   		$data['email']=$mail['email'];
   		$data['title']='邮箱验证';
   		$str=md5($user['salt'].$uid.$data['email']);
   		$data['body']='您的链接已经生成<br>http://'.$_SERVER['HTTP_HOST'].url('index/yzemailurl').'?id='.$str;
   		 
   
   		 
   		asyn_sendmail($data);
   		return json(array('code'=>1,'msg'=>'邮箱登录已更改为新邮箱，请到邮箱查收验证'));
   	}
   
   	 
   	 
   	 
   	 
   	 
   	 
   	 
   }
   public function forgetcodebymail(){
   	 
   	$mail=$this->request->param();
   
   	$emailinfo=parent::$commonLogic->getDataInfo('user',['usermail'=>$mail['email']]);
   	//是否能得到
   
   
   	if($emailinfo){
   
   
   		$data['email']=$mail['email'];
   		$data['title']='忘记密码-邮箱验证';
   
   
   		$code=generate_code($mail['email']);
   		$data['body']='您的验证码已经生成<br>'.$code;
   		asyn_sendmail($data);
   		return json(array('code'=>1,'msg'=>'验证码已经发送，请到邮箱查收验证'));
   		 
   	}else{
   		return json(array('code'=>0,'msg'=>'该邮箱不存在'));
   
   
   
   
   	}
   	 
   }
   public function reyzemail(){
   	 
   	$mail=$this->request->param();
   	$uid =  is_login();
   	$user=parent::$commonLogic->getDataInfo('user',['id'=>$uid]);
   	 
   	 
   	$emailinfo=parent::$commonLogic->getDataInfo('user',['usermail'=>$mail['email'],'id'=>array('neq',$uid)]);
   	if($emailinfo){
   		return json(array('code'=>0,'msg'=>'邮箱已被使用'));
   	}else{
   		$n['usermail']=$mail['email'];
   		if($user['status']==2){
   			$n['status']=1;
   		}else{
   			$n['status']=3;
   		}
   		$n['id']=$uid;
   		parent::$commonLogic->dataEdit('user',$n,false);
   		 
   
   		$data['email']=$mail['email'];
   		$data['title']='邮箱验证';
   		$str=md5($user['salt'].$uid.$data['email']);
   		$data['body']='您的链接已经生成<br>http://'.$_SERVER['HTTP_HOST'].url('index/yzemailurl').'?id='.$str;
   		asyn_sendmail($data);
   		return json(array('code'=>1,'msg'=>'邮箱登录已更改为新邮箱，请到邮箱查收验证'));
   		 
   		 
   		 
   	}
   
   }
    
   public function send_mail()
   {
   	 
   	 
   	$mail=$this->request->param();
   	 
   	$res=send_email($mail['email'], $mail['title'], $mail['body']);
   	 
   	if($res==1){
   		return json(array('code'=>1,'msg'=>'邮件已发送，请到邮箱进行查收'));
   		//	$this->success('邮件已发送，请到邮箱进行查收');
   	}else{
   		return json(array('code'=>0,'msg'=>'邮件发送失败，请检查邮箱设置'));
   		//$this->error('邮件发送失败，请检查邮箱设置');
   	}
   }
}