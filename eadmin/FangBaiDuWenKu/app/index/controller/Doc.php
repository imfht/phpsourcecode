<?php
namespace app\index\controller;
use app\common\controller\HomeBase;

use Qiniu\json_decode;


class Doc extends  HomeBase
{

	
	public function _initialize()
	{
		parent::_initialize();
		
	
		$ruleinfo=parent::$commonLogic->getDataList('pointRule',['controller' => 'docupload'],true,'',false);
		
		if($ruleinfo){
			
			$point_tip='';
			foreach ($ruleinfo as $k =>$v){
				
				$point_tip=$point_tip.parse_config_attr(config('scoretype_list'))[$v['scoretype']];
				
				if($v['type']==1){
				
					$point_tip=$point_tip.'+'.$v['score'];
				
				}else{
				
					$point_tip=$point_tip.'-'.$v['score'];
				}
				
			}	
		
				
				
		}else{
				
			$point_tip='';
				
		}
		
		$this->assign('point_tip',$point_tip);
		
	}
public function ajaxipstr(){
	$id=$this->param['id'];
	$ext=$this->param['ext'];
	$filename=$this->param['filename'];
	$num=$this->param['num'];
	$type=$this->param['type'];
	
	
	$docinfo=parent::$commonLogic->getDataInfo('doccon',['id' => $id]);
	
	
	
	
		
		if($type==1){
			$value['str']=getipstr($ext,$filename,$num,true,true);
		}else{
			$value['str']=getipstr($ext,$filename,$num,true,false);
		}
		
	
	
	$realstrcount=substr_count($value['str'],'stl_02');
	
	if($realstrcount!=$docinfo['pageid']&&$type==2){
		$docconLogic->setDocconValue(['id'=>$id],'pageid',$realstrcount);
	}
	
	
	
	return json_encode($value);
	
}
	public function docstatus(){//点赞和收藏的操作
		
		!is_login() && $this->jump([RESULT_ERROR, '请先登录']);
		
		$data=$this->param;
        $data['uid']=session('member_info')['id'];
        
        $where['uid']=$data['uid'];
        $where['sid']=$data['sid'];
        $where['type']=$data['type'];
      
        if(model('zan')->where($where)->count()>0){
        	if($where['type']==1){
        		$info='已赞过该文档';
        	}
        	if($where['type']==2){
        		$info='已赞过该评论';
        	}
        	if($where['type']==3){
        		$info='已收藏过该文档';
        	}
        	$this->jump([RESULT_ERROR, $info]);
        	
        }else{
        	$this->jump(parent::$commonLogic->dataAdd('zan',$data,false,'操作成功'));
        }
		
		
		
	}
	public function docpingfen(){
	
		!is_login() && $this->jump([RESULT_ERROR, '请先登录']);
	
		$data['itemid']=$this->param['sid'];
		$data['uid']=session('member_info')['id'];
		$data['score']=$this->param['type'];
	
		$where['uid']=$data['uid'];
		$where['itemid']=$data['itemid'];
		
	
		if(model('raty_user')->where($where)->count()>0){
		
				$info='已对该文档进行过评分';
		
	
			$this->jump([RESULT_ERROR, $info]);
			 
		}else{
			
			$obj=new Doc();
			
			$this->jump(parent::$commonLogic->dataAdd('raty_user',$data,false,'评分成功',$obj,'callback_pingfen'));
		}
	
	
	
	}
	public function callback_pingfen($result,$data){
		
		$itemid=$data['itemid'];
		$sum=model('raty_user')->where(['itemid'=>$itemid])->sum('score');
		$count=model('raty_user')->where(['itemid'=>$itemid])->count('score');
		
		$pingfen=round($sum/$count,2);
		parent::$commonLogic->setDataValue('doccon',['id'=>$itemid],'raty',$pingfen);
		
	}
	public function cnxs(){
	
		$id = $this->param['id'];
	
		$xsid = $this->param['xsid'];
	
		$obj =new Doc();
			
		
		$this->jump(parent::$commonLogic->setDataValue('docxs',['id'=>$xsid],'cnid',$id,'已采纳该答案',$obj,'cnxs_callback'));
	
	}
	public function cnxs_callback($value,$where){
		
		
		$xsinfo=parent::$commonLogic->getDataInfo('docxs',$where);
		parent::$commonLogic->setDataValue('docxs',$where,'status',2);
		$uid=model('doccon')->where(['id'=>$value])->value('uid');
		
		
		
		$content='您的文档在悬赏任务<a href="'.url('doc/docxscon',array('id'=>$xsinfo['id'])).'">'.$xsinfo['title'].'</a>中刚刚被采纳了';
		
		sendsysmess($content,0,$uid,1);
		
		
		
		point_change($uid,'point',$xsinfo['score'],1,'docxsbcn',$xsinfo['id'],0);//采纳后获得财富值
		
		point_controll($uid,'docxsbcn',$xsinfo['id']);//被采纳增加经验值
		
		
	}
	/***
	 * 悬赏
	 */
	public function docxs(){

		!is_login() && $this->error('请先登录', 'User/login');
		
		if(IS_POST){
				
			$data=$this->param;
				
			$data['status']=0;
		
			$data['uid']=session('member_info')['id'];
			
			$userinfo=parent::$commonLogic->getDataInfo('user',['id'=>$data['uid']]);
			
			if($userinfo['point']<$data['score']){
				
				$this->jump([RESULT_ERROR, '积分不足']);
				
			}else{
				
				$this->jump(parent::$commonLogic->dataAdd('docxs',$data,true,'悬赏添加成功'));
				
			}
				
			
		}
		
		$this->assign('groupcatelist',parent::$commonLogic->getDataList('groupcate',['status'=>1], true, 'sort desc'));
		
		
		return $this->fetch();
			
	}
	
	public function docxscomplete(){
		$this->assign('docxslist',parent::$commonLogic->getDataList('docxs',['m.status'=>1], 'm.*,user.username,doccate.name as tidname,groupcate.name as gidname', 'm.score desc',false,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id'],['groupcate','m.gid=groupcate.id']],'',4));
			

		return $this->fetch();
			
	}
	public function docxscon($id){

		
		if($id>0){
		$info = parent::$commonLogic->getDataInfo('docxs',['m.id'=>$id], 'm.*,user.username,user.userhead,doccate.name as tidname,groupcate.name as gidname',[['user','m.uid=user.id'],['doccate','m.tid=doccate.id'],['groupcate','m.gid=groupcate.id']]);
		
		parent::$commonLogic->setDataValue('docxs',['id'=>$id], 'view', array('exp','view+1'));//增加浏览数
		
		$info['usercount'] = model('docxs')->where(['uid'=>$info['uid']])->count();
		
		$this->assign('info',$info);
		
		$replylist=parent::$commonLogic->getDataList('doccon',['m.xsid'=>$info['id']], 'm.*,user.username,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext', 'm.create_time desc',5,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT']]);
		
		$this->assign('replylist',$replylist);
		
		$this->assign('rcount',model('doccon')->where(['xsid'=>$info['id']])->count());
		}else{
			$this->error('非法操作', 'index/index');
		}
		
		return $this->fetch();
			
	}
	public function docxslist(){

		
		$groupcatelist=parent::$commonLogic->getDataList('groupcate',['status'=>1], true, 'sort desc');
		
		$doccatelist=parent::$commonLogic->getDataList('doccate',['status'=>1], true, 'sort desc');
		
		empty($this->param['sorttype']) ? $sorttype = 0 : $sorttype = $this->param['sorttype'];
		
		empty($this->param['zoneid']) ? $zoneid = 0 : $zoneid = $this->param['zoneid'];
		
		empty($this->param['desc']) ? $desc = 1 : $desc = $this->param['desc'];
		
		empty($this->param['cid']) ? $cid = 0 : $cid = $this->param['cid'];
		
		empty($this->param['end']) ? $end = 0 : $end = $this->param['end'];
		
		if($desc==1){
			
			$asc=0;
			
		}else{
			
			$asc=1;
			
		}
		
		$lsarr=array();
		
		foreach ($doccatelist as $k => $v){
			
			$lsarr[$v['pid']][]=$v;
		
		}
		
		$sortdesc='';
		
		if($sorttype==0){
			
			if($desc==1){
				$sortdesc='m.days desc';
			}else{
				$sortdesc='m.days asc';
			}
			
		}
		if($sorttype==1){
				
			if($desc==1){
				$sortdesc='m.score desc';
			}else{
				$sortdesc='m.score asc';
			}
				
		}
		if($cid>0){
			
			$where['m.tid']=$cid;
			
		}else{
			
			if($zoneid>0){
				$where['m.gid']=$zoneid;
			}
			
			
			
		}
		
		
		if($end==1){
			$where['m.status']=2;
		}else{
			$where['m.status']=1;
		}
		
		
		$docxslist=parent::$commonLogic->getDataList('docxs',$where, 'm.*,user.username,doccate.name as tidname,groupcate.name as gidname', $sortdesc,0,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id'],['groupcate','m.gid=groupcate.id']]);
		$where['m.status']=1;
		$ranklist=parent::$commonLogic->getDataList('docxs',$where, 'm.*,user.username,doccate.name as tidname,groupcate.name as gidname', 'm.view asc',false,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id'],['groupcate','m.gid=groupcate.id']],'',10);
		
		
		
		$this->assign('list', $docxslist);
		
		$this->assign('ranklist', $ranklist);
		
		$this->assign('sorttype',$sorttype);
		
		$this->assign('zoneid',$zoneid);
		
		$this->assign('desc',$desc);
		
		$this->assign('asc',$asc);
		
		$this->assign('cid',$cid);
		
		$this->assign('end',$end);
		
		$this->assign('doccatelist',$lsarr);
		
		$this->assign('groupcatelist',$groupcatelist);
			
		return $this->fetch();
			
	}	
	
	
	
	
	public function doccatelist(){

		$zoneid=$this->param['id'];
		
		if(empty($zoneid)){
			$this->error('非法参数');
		}
		$daytime=time()-24*60*60;
		
		$zhoutime=time()-7*24*60*60;
		
		$mouthtime=time()-30*7*24*60*60;
		
		$docdownrankday=parent::$commonLogic->getDataList('doccz',['doccon.gid'=>$zoneid,'m.type'=>1,'m.create_time'=>array('gt',$daytime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
		$docdownrankzhou=parent::$commonLogic->getDataList('doccz',['doccon.gid'=>$zoneid,'m.type'=>1,'m.create_time'=>array('gt',$zhoutime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
		$docdownrankmouth=parent::$commonLogic->getDataList('doccz',['doccon.gid'=>$zoneid,'m.type'=>1,'m.create_time'=>array('gt',$mouthtime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
		$this->assign('docdownrankday', $docdownrankday);//下载排行本日
		$this->assign('docdownrankzhou', $docdownrankzhou);//本周
		$this->assign('docdownrankmouth', $docdownrankmouth);//本月
		$docviewnrankday=parent::$commonLogic->getDataList('doccon',['gid'=>$zoneid,'status'=>1,'create_time'=>array('gt',$daytime)], '', 'view desc',false,'','',10);
		$docviewrankzhou=parent::$commonLogic->getDataList('doccon',['gid'=>$zoneid,'status'=>1,'create_time'=>array('gt',$zhoutime)], '', 'view desc',false,'','',10);
		$docviewrankmouth=parent::$commonLogic->getDataList('doccon',['gid'=>$zoneid,'status'=>1,'create_time'=>array('gt',$mouthtime)], '', 'view desc',false,'','',10);
		$this->assign('docviewnrankday', $docviewnrankday);//热点排行本日
		$this->assign('docviewrankzhou', $docviewrankzhou);//本周
		$this->assign('docviewrankmouth', $docviewrankmouth);//本月
		
		$doccatelist=parent::$commonLogic->getDataList('doccate',['status'=>1,'pid'=>$zoneid], true, 'sort desc',false);
		
		$doccount = model('doccon')->where(['status'=>1,'gid'=>$zoneid])->count();
		$this->assign('doccount', $doccount);
		$this->assign('zoneid', $zoneid);
		
		$zonename=parent::$commonLogic->getDataInfo('groupcate',['id'=>$zoneid]);
		$this->assign('zonename', $zonename['name']);
		
		foreach($doccatelist as $k =>$v){
			$doccatelist[$k]['newlist']=parent::$commonLogic->getDataList('doccon',['m.tid'=>$v['id'],'m.status'=>1], 'm.*,user.username,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext', 'm.create_time desc',false,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT']],'',4);
			$hotlist=parent::$commonLogic->getDataList('doccon',['m.tid'=>$v['id'],'m.status'=>1], 'm.*,user.username,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext', 'm.view desc',false,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT']],'',3);
			
			//热门关键词
			$n=parent::$commonLogic->getDataList('searchword',['m.status'=>1], 'm.name', 'm.num desc',false,[['doccon','doccon.tid='.$v['id'].' AND doccon.keywords like "%m.name%"','LEFT']],'',4);
			
			$doccatelist[$k]['hotlist']=$hotlist;
			$doccatelist[$k]['keywords']=$n;
			
			
		}
		$this->assign('doccatelist', $doccatelist);
		
		return $this->fetch();
			
	}
	public function docchoice(){
		
		empty($this->param['doctype']) ? $doctype = 0 : $doctype = $this->param['doctype'];
		empty($this->param['sorttype']) ? $sorttype = 0 : $sorttype = $this->param['sorttype'];
		if($sorttype==1){
		
			$sortdesc='m.down desc';
		
		}else{
			$sortdesc='m.create_time desc';
		}
		if($doctype==1){//精品
		
				$docconlist=parent::$commonLogic->getDataList('doccon',['m.status'=>1,'m.choice'=>1], 'm.*,user.username,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext', 'm.create_time desc',0,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT']]);
		
		
		}elseif($doctype==3){
				$docconlist=parent::$commonLogic->getDataList('doccon',['m.status'=>1], 'm.*,user.username,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext', 'm.create_time desc',0,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT']]);
		
		}else{
			$docconlist=parent::$commonLogic->getDataList('doccon',['m.status'=>1], 'm.*,user.username,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext', 'm.settop desc,m.view desc',0,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT']]);
			
		}
	
		$daytime=time()-24*60*60;
		
		$zhoutime=time()-7*24*60*60;
		
		$mouthtime=time()-30*7*24*60*60;
		
		$docdownrankday=parent::$commonLogic->getDataList('doccz',['doccon.status'=>1,'m.type'=>1,'m.create_time'=>array('gt',$daytime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
		$docdownrankzhou=parent::$commonLogic->getDataList('doccz',['doccon.status'=>1,'m.type'=>1,'m.create_time'=>array('gt',$zhoutime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
		$docdownrankmouth=parent::$commonLogic->getDataList('doccz',['doccon.status'=>1,'m.type'=>1,'m.create_time'=>array('gt',$mouthtime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
		$this->assign('docdownrankday', $docdownrankday);
		$this->assign('docdownrankzhou', $docdownrankzhou);
		$this->assign('docdownrankmouth', $docdownrankmouth);
		$docviewnrankday=parent::$commonLogic->getDataList('doccon',['status'=>1,'create_time'=>array('gt',$daytime)], '', 'view desc',false,'','',10);
		$docviewrankzhou=parent::$commonLogic->getDataList('doccon',['status'=>1,'create_time'=>array('gt',$zhoutime)], '', 'view desc',false,'','',10);
		$docviewrankmouth=parent::$commonLogic->getDataList('doccon',['status'=>1,'create_time'=>array('gt',$mouthtime)], '', 'view desc',false,'','',10);
		$this->assign('docviewnrankday', $docviewnrankday);
		$this->assign('docviewrankzhou', $docviewrankzhou);
		$this->assign('docviewrankmouth', $docviewrankmouth);
		
		
		
		$this->assign('list', $docconlist);
		$this->assign('sorttype',$sorttype);
		$this->assign('doctype',$doctype);
		return $this->fetch();
	}
	public function doclist(){
		if(empty($this->param['zoneid'])||empty($this->param['cid'])){
				
			$this->error('非法参数');
				
		}
		
		$zoneid=$this->param['zoneid'];
		
		$cid=$this->param['cid'];
		$uid=is_login();
		
		$update_time=0;
		
		if($uid>0){
				
			$updatetime=model('zan')->where(['uid'=>$uid,'sid'=>$cid,'type'=>4])->value('update_time');
			if($updatetime>0){
				$update_time=$updatetime;
			}	
				
			model('zan')->where(['uid'=>$uid,'sid'=>$cid,'type'=>4])->setField('update_time',time());
				
		}
		empty($this->param['sorttype']) ? $sorttype = 0 : $sorttype = $this->param['sorttype'];
		
		
		if($sorttype==1){
		
				$sortdesc='m.down desc';
				
       }else{
			$sortdesc='m.create_time desc';
		}
	
		$doccatelist=parent::$commonLogic->getDataList('doccate',['status'=>1,'pid'=>$zoneid], true, 'sort desc',false);
		
		
		$count=0;
		$arrcount=0;
		foreach($doccatelist as $k => $v){
			$count++;
			if($count==5){
				$arrcount++;
				$count=0;
			}
			$arr[$arrcount][]=$v;
			
			
			
		}
		
		
		$docconlist=parent::$commonLogic->getDataList('doccon',['m.tid'=>$cid], 'm.*,user.username,user.nickname,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext', $sortdesc,0,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT']]);
		
		$daytime=time()-24*60*60;
		
		$zhoutime=time()-7*24*60*60;
		
		$mouthtime=time()-30*7*24*60*60;
		
		$docdownrankday=parent::$commonLogic->getDataList('doccz',['doccon.tid'=>$cid,'m.type'=>1,'m.create_time'=>array('gt',$daytime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
		$docdownrankzhou=parent::$commonLogic->getDataList('doccz',['doccon.tid'=>$cid,'m.type'=>1,'m.create_time'=>array('gt',$zhoutime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
		$docdownrankmouth=parent::$commonLogic->getDataList('doccz',['doccon.tid'=>$cid,'m.type'=>1,'m.create_time'=>array('gt',$mouthtime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
		$this->assign('docdownrankday', $docdownrankday);		
		$this->assign('docdownrankzhou', $docdownrankzhou);
		$this->assign('docdownrankmouth', $docdownrankmouth);
		$docviewnrankday=parent::$commonLogic->getDataList('doccon',['tid'=>$cid,'status'=>1,'create_time'=>array('gt',$daytime)], '', 'view desc',false,'','',10);
		$docviewrankzhou=parent::$commonLogic->getDataList('doccon',['tid'=>$cid,'status'=>1,'create_time'=>array('gt',$zhoutime)], '', 'view desc',false,'','',10);
		$docviewrankmouth=parent::$commonLogic->getDataList('doccon',['tid'=>$cid,'status'=>1,'create_time'=>array('gt',$mouthtime)], '', 'view desc',false,'','',10);
		$this->assign('docviewnrankday', $docviewnrankday);
		$this->assign('docviewrankzhou', $docviewrankzhou);
		$this->assign('docviewrankmouth', $docviewrankmouth);
		
		$this->assign('update_time', $update_time);
		
		$this->assign('list', $docconlist);
		
		$this->assign('doccatelist',$arr);
		
		$this->assign('sorttype',$sorttype);
		
		$this->assign('zoneid',$zoneid);
$cidinfo=parent::$commonLogic->getDataInfo('doccate',['id'=>$cid]);

		$this->assign('cidinfo',$cidinfo);
		
		$this->assign('cid',$cid);
			
		return $this->fetch();
			
	}
	public function doccon(){

		$id = $this->param['id'];
		if($id>0){
			$myuid = session('member_info')['id'];
			parent::$commonLogic->setDataValue('doccon',['id'=>$id], 'view', array('exp','view+1'));
			doccz($myuid,$id,2);//浏览记录
			
			
			$info = parent::$commonLogic->getDataInfo('doccon',['m.id'=>$id],'m.*,user.username,user.nickname,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext,file.size', [['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT']]) ;
			
			$info['fileinfo']=explode('.'.$info['ext'], $info['savename']);
			
			if(model('point_note')->where(['uid'=>$myuid,'itemid'=>$id,'controller'=>'docdown','scoretype'=>'point'])->count()>0||$myuid==$info['uid']){
				$info['hasdown']=1;
			}else{
				$info['hasdown']=0;
			}
			
			if(model('zan')->where(['uid'=>$myuid,'sid'=>$id,'type'=>1])->count()>0){
				$info['haszan']=1;
			}else{
				$info['haszan']=0;
			}
			if(model('zan')->where(['uid'=>$myuid,'sid'=>$id,'type'=>3])->count()>0){
			
				$info['hassc']=1;
			}else{
				$info['hassc']=0;
			}
			if($aaa=model('raty_user')->where(['uid'=>$myuid,'itemid'=>$id])->find()){
					
				$info['haspf']=1;
				
				$info['pingfen']=getpingfen($aaa['score']);
				
			}else{
				$info['haspf']=0;
			}
			
			if(model('doccon')->where(['status'=>1])->count()>0){
			
			$bfb=model('doccon')->where(['status'=>1,'raty'=>array('lt',$info['raty'])])->count()*100/model('doccon')->where(['status'=>1])->count();
			$bfb=round($bfb,2);
			}else{
				$bfb=0;
			}
			
		
			$this->assign('bfb',$bfb);
			$rzuserdoclistarr=getpingfen($info['raty']);
			$this->assign('rzuserdoclistarr',$rzuserdoclistarr);
			
			//喜欢此文档的还喜欢
			$zan['type']=3;
			$zan['sid']=$id;
			$uidarr=model('zan')->where($zan)->column('uid');
			
			$like['type']=3;
			$like['uid']=array('in',$uidarr);
			$like['sid']=array('neq',$id);
			$itemidarr=model('zan')->where($like)->column('sid');

			
			
			$likedocconlist=parent::$commonLogic->getDataList('doccon',['m.id'=>array('in',$itemidarr)],'m.*,count(ratyUser.itemid) as ratycount,user.username,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext', 'm.create_time desc',false,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT'],['ratyUser','m.id=ratyUser.itemid','LEFT']],'ratyUser.itemid',10);
			
			$this->assign('likedocconlist',$likedocconlist);
			
			//相关文档推荐
			$tjdocconlist=parent::$commonLogic->getDataList('doccon',['m.id'=>array('neq',$id),'m.choice'=>1,'m.tid'=>$info['tid']],'m.*,count(ratyUser.itemid) as ratycount,user.username,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext', 'm.create_time desc',false,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT'],['ratyUser','m.id=ratyUser.itemid','LEFT']],'ratyUser.itemid',10);
			
			$this->assign('tjdocconlist',$tjdocconlist);
			
			$commentlist=parent::$commonLogic->getDataList('comment',['m.fid'=>$id],'m.*,user.username,user.nickname,user.userhead,ratyUser.score as ratyscore', 'm.create_time desc',0,[['user','m.uid=user.id'],['ratyUser','m.fid=ratyUser.itemid','LEFT']]);
			
			$commentcount=model('comment')->where(['fid'=>$id])->count();
			                         
			
			$userinfo=parent::$commonLogic->getDataInfo('user',['id'=>$myuid]);
			
			$this->assign('userinfo',$userinfo);
			if($info['uid']==$userinfo['id']||$userinfo['point']>=$info['score']){
				$downstatus=1;
			}else{
				$downstatus=0;
			}
			$this->assign('downstatus',$downstatus);
			
			
			$this->assign('commentlist',$commentlist);
			$this->assign('commentcount',$commentcount);
			$this->assign('info',$info);
			
			
			if(model('zan')->where(['uid'=>$myuid,'sid'=>$info['tid'],'type'=>4])->count()>0){
				$hasrss=1;
			}else{
				$hasrss=0;
			}
			$this->assign('hasrss',$hasrss);
		}else{
			$this->error('非法操作', 'index/index');
		}
		
		return $this->fetch();
			
	}
	

	
	/***
	 * 文档上传
	 */
	public function docupload(){
		 
		!is_login() && $this->error('请先登录', 'User/login');
		
		empty($this->param['xsid']) ? $xsid = 0 : $xsid = $this->param['xsid'];
		
		$this->assign('xsid',$xsid);
		
		return $this->fetch();
		 
	}
	public function docuploadinfo(){

		!is_login() && $this->error('请先登录', 'User/login');
		empty($this->param['xsid']) ? $xsid = 0 : $xsid = $this->param['xsid'];
		$this->assign('xsid',$xsid);
		
		if(IS_POST){
			
			$data=$this->param;
			
			if(empty($data['status'])){
				
				$data['status']=0;
				
			}
			
			$data['uid']=session('member_info')['id'];
			
			$data['fileid']=session('last_uploadid');
			
			
			
			$result = parent::$commonLogic->getDataInfo('file',['id' => session('last_uploadid')]);
			
			
			$data['filename']=$result['name'];
			
			if($data['xsid']>0){
				
				parent::$commonLogic->setDataValue('docxs',['id'=>$data['xsid']], 'reply', array('exp','reply+1'));
				
				point_controll($data['uid'],'docxsupload',$data['xsid']);//增加经验值或者财富值
				
			}
           
		
			
			$obj =new Doc();
			
			$this->jump(parent::$commonLogic->dataAdd('doccon',$data,true,'文档添加成功',$obj,'docupload_callback'));
		}
		
		//$groupcateLogic = get_sington_object('groupcateLogic', LogicGroupcate::class);
		
		$this->assign('groupcatelist',parent::$commonLogic->getDataList('groupcate',['status'=>1], true, 'sort desc',false));
		
		$this->assign('taglist',parse_config_attr(config('keyword_list')));
		
		return $this->fetch();
			
	}
	public function docupload_callback($result,$data){
		
		
		
		session('last_uploadid',null);
	}
	/***
	 * 文档上传通过频道获取分类
	 */
	public function getDoccatedata(){
		
		
		
		$info=parent::$commonLogic->getDataList('doccate',['pid'=>$this->param['id']], true, 'sort desc',false);
		
		return json($info);
	}
	public function docuploadcomplete(){
		
		
		
		$this->assign('docxslist',parent::$commonLogic->getDataList('docxs',['m.status'=>1], 'm.*,user.username,doccate.name as tidname,groupcate.name as gidname', 'm.score desc',false,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id'],['groupcate','m.gid=groupcate.id']],'',4));
			
		return $this->fetch();
			
	}
	public function fulldoc(){
	
		$id = $this->param['id'];
		if($id>0){
				
			$info = parent::$commonLogic->getDataInfo('doccon',['m.id'=>$id],'m.*,user.username,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext,file.size', [['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT']]) ;
			$info['fileinfo']=explode('.'.$info['ext'], $info['savename']);
		
			$myuid = session('member_info')['id'];
			
			if(model('point_note')->where(['uid'=>$myuid,'itemid'=>$id,'controller'=>'docdown','scoretype'=>'point'])->count()>0||$myuid==$info['uid']){
				$info['hasdown']=1;
			}else{
				$info['hasdown']=0;
			}
			
			if(model('zan')->where(['uid'=>$myuid,'sid'=>$id,'type'=>1])->count()>0){
				$info['haszan']=1;
			}else{
				$info['haszan']=0;
			}
			if(model('zan')->where(['uid'=>$myuid,'sid'=>$id,'type'=>3])->count()>0){
					
				$info['hassc']=1;
			}else{
				$info['hassc']=0;
			}
			if($aaa=model('raty_user')->where(['uid'=>$myuid,'itemid'=>$id])->find()){
					
				$info['haspf']=1;
		
				$info['pingfen']=getpingfen($aaa['score']);
		
			}else{
				$info['haspf']=0;
			}
				
		
				
			$userinfo=session('member_info');
				
				
			if($info['uid']==$userinfo['id']||$userinfo['point']>=$info['score']){
				$downstatus=1;
			}else{
				$downstatus=0;
			}
			$this->assign('downstatus',$downstatus);

				
		}else{
			$this->error('非法操作', 'index/index');
		}
		$this->assign('info',$info);
		return $this->fetch();
			
	}

}