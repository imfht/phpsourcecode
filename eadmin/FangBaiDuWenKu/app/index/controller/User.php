<?php
namespace app\index\controller;
use app\common\controller\HomeBase;

use app\common\logic\User as LogicUser;


class User extends  HomeBase
{
	
	// 用户逻辑
	private static $logicUser = null;

	
	
	public function _initialize()
	{
		parent::_initialize();
		
		self::$logicUser = get_sington_object('logicUser', LogicUser::class);
	

		$uid=is_login();
		
		if($uid>0){
			$doclist = parent::$commonLogic->getDataList('zan',['m.uid'=>$uid,'m.type'=>4],'m.*,doccate.id as cateid,doccate.name as catename,doccate.pid as zoneid','m.create_time desc',false,[['doccate','doccate.id=m.sid']]);
			$sccount=0;
			foreach ($doclist as $k =>$v){
			
				$sccount=$sccount+model('doccon')->where(['tid'=>$v['sid'],'create_time'=>array('gt',$v['update_time'])])->count();
			
			}
			$this->assign('sccount',$sccount);
		}
		
		
		
	}
	public function userdata($uid){
		
		$uid=session('member_info')['id'];
		
		$commonuserinfo=parent::$commonLogic->getDataInfo('user',['id'=>$uid]);
		$this->assign('commonuserinfo',$commonuserinfo);
		$doccount=model('doccon')->where(['uid'=>$uid])->count();
		$daytime=time()-24*60*60;
			
		$docidarr=model('doccon')->where(['uid'=>$uid])->column('id');
			
		$downcount=model('doccz')->where(['type'=>1,'did'=>array('in',$docidarr),'create_time'=>array('gt',$daytime)])->count();//下载
		$viewcount=model('doccz')->where(['type'=>2,'did'=>array('in',$docidarr),'create_time'=>array('gt',$daytime)])->count();//浏览
		$scorecount1=model('point_note')->where(['type'=>2,'infouid'=>$uid,'scoretype'=>'point','create_time'=>array('gt',$daytime)])->sum('score');//财富增加
		$scorecount2=model('point_note')->where(['type'=>1,'uid'=>$uid,'scoretype'=>'point','create_time'=>array('gt',$daytime)])->sum('score');
		$scorecount=$scorecount1+$scorecount2;
			
		$this->assign('doccount',$doccount);
		$this->assign('downcount',$downcount);
		$this->assign('viewcount',$viewcount);
		$this->assign('scorecount',$scorecount);
		
			
		$viewlist=parent::$commonLogic->getDataList('doccz',['m.uid'=>$uid,'m.type'=>2,'m.create_time'=>array('gt',$daytime)],'m.did,doccon.title,file.ext,max(m.create_time) as maxtime','m.create_time desc',false,[['doccon','doccon.id=m.did'],['file','file.id=doccon.fileid']],'m.did',4);
		$this->assign('viewlist',$viewlist);
		

	}
	public function focususer(){
		
		$type=$this->param['type'];
		$uid=is_login();
		if($uid==0){
			$this->jump(([RESULT_ERROR, '请登录后操作']));
		}else{
			if($type==1){//关注
					
				$data['type']=0;
				$data['sid']=$this->param['useruid'];
				$data['uid']=$uid;
				$this->jump(parent::$commonLogic->dataAdd('zan',$data,false,'关注成功'));
			}else{//取消关注
					
				
				$this->jump(parent::$commonLogic->dataDel('zan',['type'=>0,'sid'=>$this->param['useruid'],'uid'=>$uid],'取消关注',true));
			}
			
			
		}
	
		
		
	}
	public function rsscate(){
	
		$type=$this->param['type'];
		$uid=is_login();
		if($uid==0){
			$this->jump(([RESULT_ERROR, '请登录后操作']));
		}else{
			if($type==1){//订阅
					
				$data['type']=4;
				$data['sid']=$this->param['cateid'];
				$data['uid']=$uid;
				$this->jump(parent::$commonLogic->dataAdd('zan',$data,false,'订阅成功'));
			}else{//取消订阅
					
	
				$this->jump(parent::$commonLogic->dataDel('zan',['type'=>4,'sid'=>$this->param['cateid'],'uid'=>$uid],'取消订阅',true));
			}
				
				
		}
	
	
	
	}
	public function userfocus(){
		!is_login() && $this->jump(RESULT_REDIRECT, 'Index/index');
		$uid=is_login();
		$this->userdata($uid);
		empty($this->param['type']) ? $type = 1 : $type = $this->param['type'];
		
		$sidarr=model('zan')->where(['uid'=>$uid,'type'=>0])->column('sid');//得到所有的我关注的人
		$gzidarr=model('zan')->where(['sid'=>$uid,'type'=>0])->column('uid');//得到所有关注我的人
		
		if($type==1){//好友
			
			
			
			
			$userlist = parent::$commonLogic->getDataList('zan',['m.uid'=>array('in',$sidarr),'m.sid'=>$uid,'m.type'=>0],'m.uid,m.sid,m.create_time,user.nickname,user.id as userid,user.userhead,count(doccon.id) as doccount','m.create_time desc',8,[['user','user.id=m.uid'],['doccon','doccon.uid=m.uid','LEFT']],'doccon.uid');
		
		}
		if($type==2){//关注
			
			$userlist = parent::$commonLogic->getDataList('zan',['m.uid'=>$uid,'m.type'=>0,'m.sid'=>array('not in',$gzidarr)],'m.uid,m.sid,m.create_time,user.nickname,user.id as userid,user.userhead,count(doccon.id) as doccount','m.create_time desc',8,[['user','user.id=m.sid'],['doccon','doccon.uid=m.sid','LEFT']],'doccon.uid');
			
		}
		if($type==3){//粉丝
			$userlist = parent::$commonLogic->getDataList('zan',['m.sid'=>$uid,'m.type'=>0,'m.uid'=>array('not in',$sidarr)],'m.uid,m.sid,m.create_time,user.nickname,user.id as userid,user.userhead,count(doccon.id) as doccount','m.create_time desc',8,[['user','user.id=m.uid'],['doccon','doccon.uid=m.uid','LEFT']],'doccon.uid');
			
		}
		$this->assign('uid',$uid);
		$this->assign('type',$type);
		$this->assign('userlist',$userlist);
		
		return $this->fetch();
	}
	public function index(){
		!is_login() && $this->jump(RESULT_REDIRECT, 'Index/index');
		$uid=is_login();
		
		$this->userdata($uid);
		
		
		
		empty($this->param['type']) ? $type = 1 : $type = $this->param['type'];
		if($type==1){
			$doclist = parent::$commonLogic->getDataList('doccon',['m.uid'=>$uid,'m.status'=>1],'m.*,file.ext,user.username','m.create_time desc',8,[['user','user.id=m.uid'],['file','file.id=m.fileid']]);
			
		}
		if($type==2){
			$doclist = parent::$commonLogic->getDataList('doccon',['m.uid'=>$uid,'m.status'=>2],'m.*,file.ext,user.username','m.create_time desc',8,[['user','user.id=m.uid'],['file','file.id=m.fileid']]);
			
		}
		if($type==3){
			$doclist = parent::$commonLogic->getDataList('doccon',['m.uid'=>$uid,'m.status'=>0],'m.*,file.ext,user.username','m.create_time desc',8,[['user','user.id=m.uid'],['file','file.id=m.fileid']]);
			
		}
		$tjcount=model('doccon')->where(['uid'=>$uid,'status'=>0])->count();
		$this->assign('doclist',$doclist);
		$this->assign('type',$type);
		$this->assign('tjcount',$tjcount);
		
		return $this->fetch();
		 
	}
	public function home(){

		
		if(empty($this->param['id'])){
			$this->error('参数错误',url('index/index'));
		}else{
			$useruid=$this->param['id'];
			$uid=is_login();
			$userinfo=parent::$commonLogic->getDataInfo('user',['id'=>$useruid]);
			$this->assign('userinfo',$userinfo);
			
			//他的文档
			$hisdoclist=parent::$commonLogic->getDataList('doccon',['m.uid'=>$useruid,'m.status'=>array('gt',0)],'m.*,file.ext','m.create_time desc',10,[['file','file.id=m.fileid']]);
			//发起的悬赏
			$docxslist = parent::$commonLogic->getDataList('docxs',['uid'=>$useruid,'status'=>array('gt',0)],true,'create_time desc',false,'','',10);
			//响应的悬赏文档
			$xsdoclist = parent::$commonLogic->getDataList('doccon',['m.uid'=>$useruid,'m.xsid'=>array('gt',0)],'m.*,docxs.title as xstitle,docxs.cnid,docxs.score as xsscore','m.create_time desc',false,[['docxs','m.xsid=docxs.id']],'',10);
			
			$this->assign('hisdoclist',$hisdoclist);
			$this->assign('docxslist',$docxslist);
			$this->assign('xsdoclist',$xsdoclist);
			
			$usercount=parent::$commonLogic->getDataList('user',['m.id'=>$useruid],'m.*,sum(doccon.down) as sumdown,sum(doccon.view) as sumview,count(doccon.id) as doccount','doccon.create_time desc',false,[['doccon','m.id=doccon.uid']]);
			$this->assign('usercount',$usercount[0]);
			$this->assign('useruid',$useruid);
			
		
			if($uid==$useruid){
				$hasfocus=2;
			}else{
				if(model('zan')->where(['uid'=>$uid,'sid'=>$useruid,'type'=>0])->count()>0){
					$hasfocus=1;
				}else{
					$hasfocus=0;
				}
			}
			$this->assign('hasfocus',$hasfocus);
		}
		
		
			
		return $this->fetch();
			
	}
   public function setting(){
   	!is_login() && $this->jump(RESULT_REDIRECT, 'Index/index');
   	$uid=is_login();
   	$this->userdata($uid);
   $userinfo=	parent::$commonLogic->getDataInfo('user',['id'=>$uid]);
   	
   $sumdowncount=model('doccon')->where(['uid'=>$uid])->sum('down');
   $this->assign('sumdowncount',$sumdowncount);
   $gkdoccount=model('doccon')->where(['uid'=>$uid,'status'=>1])->count();
   $sydoccount=model('doccon')->where(['uid'=>$uid,'status'=>2])->count();
   $totaldoccount=model('doccon')->where(['uid'=>$uid])->count();
   $scoredoccount=model('doccon')->where(['uid'=>$uid,'score'=>array('gt',0)])->count();
   
   if($totaldoccount>0){
   	$tgl=round(($gkdoccount+$sydoccount)/$totaldoccount,2)*100;  
   }else{
   	$tgl=0;
   }
   
   //$tgl=round(($gkdoccount+$sydoccount)/$totaldoccount,2)*100;
   $this->assign('gkdoccount',$gkdoccount);
   $this->assign('sydoccount',$sydoccount);
   $this->assign('scoredoccount',$scoredoccount);
   $this->assign('tgl',$tgl);
   
   $this->assign('userinfo',$userinfo);
   
   
   //上传文档财富值
   
   $where['controller']='docupload';
   
   $where['type']=1;
   $where['uid']=$uid;
   $where['scoretype']='point';
   $docuploadscoresum=model('point_note')->where($where)->sum('score');
   $this->assign('docuploadscoresum',$docuploadscoresum);
   //文档被下载财富值
   
   $where1['controller']='docdown'; 
   $where1['type']=2;
   $where1['infouid']=$uid;
   $where1['scoretype']='point';
   $docdownedscoresum=model('point_note')->where($where1)->sum('score');
   $this->assign('docdownedscoresum',$docdownedscoresum);
   //文档完成悬赏任务财富值
    
   $where2['controller']='docxs';
   $where21['type']=2;
   $where2['infouid']=$uid;
   $where2['scoretype']='point';
   $docxsedscoresum=model('point_note')->where($where2)->sum('score');
   $this->assign('docxsedscoresum',$docxsedscoresum);
   //系统奖励财富值
   
   $where3['controller']=array('not in',array('docupload'));
    
   $where3['type']=1;
   $where3['uid']=$uid;
   $where3['scoretype']='point';
   $systemscoresum=model('point_note')->where($where3)->sum('score');
   $this->assign('systemscoresum',$systemscoresum);
   
   
   
   //文库下载消费财富值
   $where4['controller']='docdown';
   $where4['type']=2;
   $where4['uid']=$uid;
   $where4['scoretype']='point';
   $docdownscoresum=model('point_note')->where($where4)->sum('score');
   $this->assign('docdownscoresum',$docdownscoresum);
   //文库悬赏消费财富值
   $where5['controller']='docxs';
   $where5['type']=2;
   $where5['uid']=$uid;
   $where5['scoretype']='point';
   $docxsscoresum=model('point_note')->where($where5)->sum('score');
   $this->assign('docxsscoresum',$docxsscoresum);
   $this->assign('docxstotalscoresum',$docxsscoresum+$docdownscoresum);
   //上传文档经验值
   $exwhere['controller']='docupload';
   
   $exwhere['type']=1;
   $exwhere['uid']=$uid;
   $exwhere['scoretype']='expoint1';
   $docuploadexsum=model('point_note')->where($exwhere)->sum('score');
   $this->assign('docuploadexsum',$docuploadexsum);
   //下载文档经验值
   $exwhere1['controller']='docdown';
    
   $exwhere1['type']=1;
   $exwhere1['uid']=$uid;
   $exwhere1['scoretype']='expoint1';
   $docdownexsum=model('point_note')->where($exwhere1)->sum('score');
   $this->assign('docdownexsum',$docdownexsum);
   //日常操作经验值
   $exwhere2['controller']=array('not in',array('docupload','docdown'));
    
   $exwhere2['type']=1;
   $exwhere2['uid']=$uid;
   $exwhere2['scoretype']='expoint1';
   $systemexsum=model('point_note')->where($exwhere2)->sum('score');
   
   
   $this->assign('systemexsum',$systemexsum);
   
   
   	return $this->fetch();
   	
   }
   /**
    * 修改个人头像处理
    */
   public function setavatarHandle(){
   	
   	$info=session('member_info');
   	$data=$this->param;
   	$data['id']=$info['id'];
   
   	$obj=new User();
   	$this->jump(parent::$commonLogic->dataEdit('user',$data,false,$info='信息编辑成功',$obj,'callback_setinfo'));
   }
   
   /**
    * 修改个人信息处理
    */
   public function setinfoHandle()
   {
   	
   
   	$info=session('member_info');
   	
   	$data=$this->param;
   	$data['username']=$info['username'];
   	$data['id']=$info['id'];
   	
   	$obj=new User();
   	
   	$this->jump(parent::$commonLogic->dataEdit('user',$data,true,$info='信息编辑成功',$obj,'callback_setinfo'));
   	 
   }
   public function callback_setinfo($result,$data){
   	$member=parent::$commonLogic->getDataInfo('user',['id'=>$data['id']]);
   	session('member_info', $member);
   
   }
   /**
    * 修改密码处理
    */
   public function setpasswordHandle()
   {
   	$data=$this->param;
   	$info=session('member_info');
   	$this->jump(self::$logicUser->setMemberPassword($data, $info));
   	 
   }
   public function mess(){
   	!is_login() && $this->jump(RESULT_REDIRECT, 'Index/index');
   	$uid=is_login();
   	$this->userdata($uid);
   	$midarr=model('readmessage')->where(['uid'=>$uid])->column('mid');
   	$list=parent::$commonLogic->getDataList('message',['id'=>array('not in',$midarr),'touid'=>array('in',array(0,$uid)),'status'=>1],true,'update_time desc');
   	
   	$data['uid']=$uid;
   	
   	parent::$commonLogic->dataEdit('readtime',$data,false);
   	
   	
   	$this->assign('list',$list);
   	return $this->fetch();
   
   }
public function ajaxdelmess(){
	$myuid=is_login();
	$id=$this->param['id'];
	$uid=$this->param['uid'];
	if($uid>0){
		
		$where['id']=$id;
		
		$this->jump(parent::$commonLogic->dataDel('message',$where,'删除成功',true));
	}else{
		$data['uid']=$myuid;
		$data['mid']=$id;
		$this->jump(parent::$commonLogic->dataAdd('readmessage',$data,false,'删除成功'));
	}
	
}
public function ajaxdelallmess(){
	$uid=is_login();
	$midarr=model('readmessage')->where(['uid'=>$uid])->column('mid');
	parent::$commonLogic->dataDel('message',['id'=>array('not in',$midarr),'touid'=>$uid],'',true);//删除私信
	
	$list=parent::$commonLogic->getDataList('message',['id'=>array('not in',$midarr),'touid'=>0],true,'update_time desc',false);
	foreach ($list as $k =>$v){
		$data['uid']=$uid;
		$data['mid']=$v['id'];
		$n=parent::$commonLogic->dataInsert('readmessage',$data,false,'删除成功');
		
		
	}
	
	$this->jump([RESULT_SUCCESS, '清空成功']);
	
}



   public function shoucang(){
   	!is_login() && $this->jump(RESULT_REDIRECT, 'Index/index');
   	$uid=is_login();
   	$this->userdata($uid);
   	empty($this->param['type']) ? $type = 1 : $type = $this->param['type'];//1表示文档2表示订阅的分类
   	if($type==1){
   		$doclist = parent::$commonLogic->getDataList('zan',['m.uid'=>$uid,'m.type'=>3],'m.*,file.ext,doccon.raty,doccon.title,user.username','m.create_time desc',8,[['doccon','doccon.id=m.sid'],['file','file.id=doccon.fileid'],['user','user.id=doccon.uid']]);
   		
   	}else{
   		$doclist = parent::$commonLogic->getDataList('zan',['m.uid'=>$uid,'m.type'=>4],'m.*,doccate.id as cateid,doccate.name as catename,doccate.pid as zoneid','m.create_time desc',8,[['doccate','doccate.id=m.sid']]);
   		foreach ($doclist as $k =>$v){
   			
   			$doclist[$k]['updatecount']=model('doccon')->where(['tid'=>$v['sid'],'create_time'=>array('gt',$v['update_time'])])->count();
   			
   		}
   		
   		
   	}
   	$this->assign('type',$type);
   	$this->assign('doclist',$doclist);
   	
   	return $this->fetch();
   	 
   }
   public function xuanshang(){
   	!is_login() && $this->jump(RESULT_REDIRECT, 'Index/index');
   	$uid=is_login();
   	$this->userdata($uid);
   	empty($this->param['type']) ? $type = 1 : $type = $this->param['type'];//1表示我的2表示我完成的
   	empty($this->param['status']) ? $status = 1 : $status = $this->param['status'];//1表示全部
   	
   	if($type==1){
   		
   		
   		
   		if($status==1){
   			//我的全部悬赏任务
   			$doclist = parent::$commonLogic->getDataList('docxs',['uid'=>$uid,'status'=>array('gt',0)],true,'create_time desc',0);
   			 
   		}elseif($status==2){
   			//我的待采纳悬赏任务
   			$doclist = parent::$commonLogic->getDataList('docxs',['uid'=>$uid,'status'=>array('gt',0),'cnid'=>0,'reply'=>array('gt',0)],true,'create_time desc',0);
   			
   		}else{
   			//我的无文档的悬赏任务
   			$doclist = parent::$commonLogic->getDataList('docxs',['uid'=>$uid,'status'=>array('gt',0),'reply'=>0],true,'create_time desc',0);
   			 
   		}
   		
		
	

   		
   	}else{
   		if($status==1){
   			//我完成的全部悬赏任务
   			$doclist = parent::$commonLogic->getDataList('doccon',['m.uid'=>$uid,'m.xsid'=>array('gt',0)],'m.*,docxs.title as xstitle,docxs.cnid','m.create_time desc',0,[['docxs','m.xsid=docxs.id']]);
   			
   		}else{
   			//我完成的被采纳悬赏任务
   			$doclist = parent::$commonLogic->getDataList('doccon',['m.uid'=>$uid,'m.xsid'=>array('gt',0)],'m.*,docxs.title as xstitle','m.create_time desc',0,[['docxs','m.xsid=docxs.id and docxs.cnid = m.id']]);
   			
   		}
 		
   		
 		 
   	}
   	$this->assign('status',$status);
   	$this->assign('type',$type);
   	
   	$this->assign('doclist',$doclist);

   	 
   	return $this->fetch();
   	 
   }
   public function docdown(){
   	
   	!is_login() && $this->jump(RESULT_REDIRECT, 'Index/index');
   	$uid=is_login();
   	$this->userdata($uid);
   	$doclist = parent::$commonLogic->getDataList('doccz',['m.uid'=>$uid,'m.type'=>1],'m.*,file.ext,doccon.raty,doccon.title,user.username,max(m.create_time) as maxtime','m.create_time desc',8,[['doccon','doccon.id=m.did'],['file','file.id=doccon.fileid'],['user','user.id=doccon.uid']],'m.did');
   	 
   	$this->assign('doclist',$doclist);
   	
   	return $this->fetch();
   	 
   	return $this->fetch();
   	 
   }
   /**
    * 注册页面
    */
   public function register(){

   	is_login() && $this->jump(RESULT_REDIRECT, 'Index/index');
   	
   	$yzm_list = parse_config_array('yzm_list');//1\注册2\登录3\忘记密码4\后台登录
   	 
   	if(in_array(1, $yzm_list)){
   		 
   		$yzm=1;
   		 
   	}else{
   		 
   		$yzm=0;
   		 
   	}
   	
   	$this->assign('yzm',$yzm);
   	
   	return $this->fetch();
   	 
   }
   /**
    * 注册处理
    */
   public function regHandle($username = '', $password = '', $repassword = '',$usermail = '', $verify = '')
   {
   	 
   	$this->jump(self::$logicUser->regHandle($username, $password, $repassword,$usermail, $verify));
   
   }   
   
   
   
   
   /**
    * 登录页面
    */
   public function login(){
   	
   	is_login() && $this->jump(RESULT_REDIRECT, 'Index/index');
   	
   	$yzm_list = parse_config_array('yzm_list');//1\注册2\登录3\忘记密码4\后台登录
   	
   	if(in_array(2, $yzm_list)){
   		
   		$yzm=1;
   		
   	}else{
   		
   		$yzm=0;
   		
   	}
   	
   	$this->assign('yzm',$yzm);
   	
   	return $this->fetch();
   	 
   }
   /**
    * 登录处理
    */
   public function loginHandle($username = '', $password = '', $verify = '')
   {
   	 
   	$this->jump(self::$logicUser->loginHandle($username, $password, $verify));
   	
   }
   /**
    * 注销处理
    */
   public function logout()
   {
   	 
   	$this->jump(self::$logicUser->logout());
   
   }
   /**
    * 忘记密码页面
    */
   public function forget(){
   	session('http_referer',1);
   	if (IS_POST) {
   
   
   		$datan=$this->request->param();
   
   		$n=parent::$commonLogic->getDataInfo('user',['usermail'=>$datan['email']]);
   
   
   
   		if(empty($n)||($n['status']!=2&&$n['status']!=5)){
   			return json(array('code' => 0, 'msg' => '邮箱未激活或邮箱未注册'));
   		}else{
   
   			$data['email']=$n['usermail'];
   
   			$data['title']='找回密码';
   			$str=md5($n['salt'].$n['id'].$n['usermail']);
   
   			$data['body']='http://'.$_SERVER['HTTP_HOST'].url('user/resetmima').'?mod='.$n['id'].'&id='.$str;
   
   
   			asyn_sendmail($data);
   			return json(array('code'=>200,'msg'=>'邮件已发送，请到邮箱进行查收'));
   				
   				
   				
   		}
   
   
   
   
   	}else{
   
   
   	}
   
   	return $this->fetch();
   	 
   }
    
   public function resetmima(){
   	 
   	$data=$this->request->param();
   	$n=parent::$commonLogic->getDataInfo('user',['id'=>$data['mod']]);
   
   	if(md5($n['salt'].$n['id'].$n['usermail'])==$data['id']){
   
   		$this->assign('userid',$n['id']);
   		$this->assign('salt',md5($n['salt']));
   		$this->assign('username',$n['username']);
   		 
   		return $this->fetch();
   	}else{
   		$this->error('非法操作',url('user/forget'));
   	}
   	 
   }
   public function resetpass()
   {
   	$data=$this->request->param();
   	$n=parent::$commonLogic->getDataInfo('user',['id'=>$data['uid']]);
   	if(md5($n['salt'])==$data['salt']){
   		 
   		if(md5($data['password'].$n['salt'])==$n['password']){
   
   			$this->jump([RESULT_SUCCESS, '密码重置成功']);
   
   		}else{
   			$m['id']=$n['id'];
   			$m['password']= md5($data['password'].$n['salt']);
   				
   			$this->jump(parent::$commonLogic->dataEdit('user', $m,false,'密码重置成功'));
   		}
   		 
   		 
   
   	}else{
   		$this->error('非法操作',url('index/index'));
   	}
   
   
   	 
   	 
   }
}