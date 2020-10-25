<?php
namespace app\index\controller;
use app\common\controller\HomeBase;



class Search extends  HomeBase
{

	
	public function _initialize()
	{
		parent::_initialize();
	
	}
	
   public function index(){
   	
   	
   	
   	
   	empty($this->param['ext']) ? $ext = 0 : $ext = $this->param['ext'];
   	empty($this->param['keyword']) ? $keyword = '' : $keyword = $this->param['keyword'];

   	if(!empty(session('searchword'))&&$keyword == ''){
   		
   		$keyword=session('searchword');
   	}
   	
   	$where['m.status']=1;
   	if($keyword!=''){
   		
   		session('searchword',$keyword);
   		
   		$where['m.keywords|m.title']=array('like','%'.$keyword.'%');
   		
   		if(model('searchword')->where(['name'=>$keyword])->count()>0){
   			
   			parent::$commonLogic->setDataValue('searchword',['name'=>$keyword], 'num', array('exp','num+1'));
   			
   		}else{
   			$data['name']=$keyword;
   		
   			parent::$commonLogic->dataAdd('searchword',$data,false);
   			
   		}
   		
   		
   	}
   	switch($ext){
   		
   		case 'doc':
   			
   			$extstr=' AND file.ext in ("doc","docx","wps")';
   			
   			break;
   			case 'ppt':
   				$extstr=' AND file.ext in ("ppt","pptx","dps")';
   				break;
   				case 'txt':
   					$extstr=' AND file.ext ="txt"';
   					break;
   					case 'pdf':
   						$extstr=' AND file.ext ="pdf"';
   						break;
   						case 'xls':
   							$extstr=' AND file.ext in ("xls","xlsx","et")';
   							break;
   						default:
   							$extstr='';
   								break;
   	}
   	
   	 	
   $this->assign('keyword',$keyword);
   empty($this->param['sorttype']) ? $sorttype = 0 : $sorttype = $this->param['sorttype'];
   if($sorttype==1){
   
   	$sortdesc='m.down desc';
   
   }else{
   	$sortdesc='m.create_time desc';
   }
   
   
   
   $docconlist=parent::$commonLogic->getDataList('doccon',$where, 'm.*,user.username,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext', $sortdesc,0,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id'.$extstr],['doczj','m.zjid=doczj.id','LEFT']]);
  
   $daytime=time()-24*60*60;
   
   $zhoutime=time()-7*24*60*60;
   
   $mouthtime=time()-30*7*24*60*60;
   

   $docdownrankday=parent::$commonLogic->getDataList('doccz',['m.create_time'=>array('gt',$daytime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
   $docdownrankzhou=parent::$commonLogic->getDataList('doccz',['m.create_time'=>array('gt',$zhoutime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
   $docdownrankmouth=parent::$commonLogic->getDataList('doccz',['m.create_time'=>array('gt',$mouthtime)], 'm.*,count(m.id) as downcount,doccon.title', 'downcount desc',false,[['doccon','m.did=doccon.id']],'m.did',10);
   $this->assign('docdownrankday', $docdownrankday);//下载排行本日
   $this->assign('docdownrankzhou', $docdownrankzhou);//本周
   $this->assign('docdownrankmouth', $docdownrankmouth);//本月
   $docviewnrankday=parent::$commonLogic->getDataList('doccon',['status'=>1,'create_time'=>array('gt',$daytime)], '', 'view desc',false,'','',10);
   $docviewrankzhou=parent::$commonLogic->getDataList('doccon',['status'=>1,'create_time'=>array('gt',$zhoutime)], '', 'view desc',false,'','',10);
   $docviewrankmouth=parent::$commonLogic->getDataList('doccon',['status'=>1,'create_time'=>array('gt',$mouthtime)], '', 'view desc',false,'','',10);
   $this->assign('docviewnrankday', $docviewnrankday);
   $this->assign('docviewrankzhou', $docviewrankzhou);
   $this->assign('docviewrankmouth', $docviewrankmouth);
   
   $this->assign('ext',$ext);
   $this->assign('sorttype',$sorttype);
   $this->assign('list', $docconlist);
   
   
   	return $this->fetch();
   	
   }

}