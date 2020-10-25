<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use think\Controller;
use app\common\logic\Docxs as LogicDocxs;
use app\common\logic\Doccon as LogicDoccon;


class Ybcommand extends Controller
{
    
   public function copyfile($fileid,$id){
   	
   	 $fileinfo = model('file')->where(['id'=>$fileid])   ->find();
   	
   	  copy(getfileurl_jd($fileid,0), PATH_UPLOAD.'doc'.DS.$fileinfo['savename']);
   	  

   	  

   	 
   	
   }
   public function docconall($arr){
   	
   	$ids=model('doccon')->where(array('status'=>0))->column('id');

   	
   	
   	if(model('doccon')->where(['id'=>array('in',$ids)])->setField('status',1)){
   	
   		foreach ($ids as $k =>$v){
   	
   			$info = model('doccon')->where(['id'=>$v])->find();
   	
   			$content='您的文档<a href="'.routerurl('doc/doccon',array('id'=>$info['id'])).'">'.$info['title'].'</a>已经审核通过';
   	
   			sendsysmess($content,0,$info['uid'],1);
   	
   	
   			if($info['xsid']>0){
   					
   				$xsinfo=model('docxs')->where(['id'=>$info['xsid']])->find();
   					
   				$content='您的悬赏<a href="'.routerurl('doc/docxscon',array('id'=>$info['xsid'])).'">'.$xsinfo['title'].'</a>有了新文档';
   					
   				sendsysmess($content,0,$xsinfo['uid'],1);
   			}
   	
   	
   			if($info['pageid']==0){
   					
   				point_controll($info['uid'],'docupload',$info['id']);
   					
   				$httpstr = http_curl(url('admin/Ybcommand/copyfile'),array('fileid'=>$info['fileid'],'id'=>$info['id']), 'POST');
   	
   	
   					
   			}
   	
   	
   	
   		}
   	
   	
   	
   		
   	
   	}
   	
   	
   	
   }
   /**
    * 初始化文档页数
    */
   public function changepageid(){
   
   	//预览是否存在
   	$docconLogic = get_sington_object('docconLogic', LogicDoccon::class);
   
   	$time=time();
   	
   	$docconlist = $docconLogic->getDocconList(['m.rightpage'=>0,'m.status'=>array('gt',0)], true, 'm.create_time desc',100);
   
   
   
   
   	foreach($docconlist as $k =>$v){
   	
   		$arr=explode('.'.$v['ext'], $v['savename']);
   

   			if(file_exists(PATH_UPLOAD.'docview/Preview/'.$arr[0].'.png')||file_exists(PATH_UPLOAD.'docview/Preview/'.$arr[0].'0001.jpg')){
   				 
   				$ipstr=getipstr($v['ext'],$arr[0]);
   				 
   				$realstrcount=substr_count($ipstr,'stl_02');
   				 
   				//得到文档页数
   				if($realstrcount>0&&$realstrcount!=$v['pageid']){
   					echo '《'.$v['title'].'》的实际页数：'.$realstrcount.'<br>';
   					echo '《'.$v['title'].'》的显示页数：'.$v['pageid'].'<br>';
   					
   					$data['id']=$v['id'];
   					$data['pageid']=$realstrcount;
   					$data['update_time']=time();
   					$data['rightpage']=1;
   				
   					$docconLogic->setDocconInfo($data);
   					
   					
   				}
   
   				 
   			}
   			 
   		
   
   
   
   
   
   
   	}
   
  return json(array('code'=>1,'msg'=>'更新成功'));
   				
   }
   //计划任务
   public function twentyfour(){
   
   	$docxsLogic = get_sington_object('docxsLogic', LogicDocxs::class);

   	$docxslist = $docxsLogic->getDocxsList(['m.status'=>1], true, 'm.id desc');
   
  if($docxslist){
   	
   foreach ($docxslist as $k => $v){
   
   if(lefttime($v['create_time'])>0){
   	if($v['days']-lefttime($v['create_time'])>0){
   		 
   		 
   		 
   		$docxsLogic->setDocxsValue(['id'=>$v['id']],'days',$v['days']-lefttime($v['create_time']));
   		 
   	}else{
   		 
   		$data['id']=$v['id'];
   		$data['days']=0;
   		$data['status']=2;
   		$docxsLogic->setDocxsInfo($data);
   		//悬赏结束如果还没有一篇文档，则退还积分，还未写
   		if($v['reply']==0){
   			
   			point_change($v['uid'],'point',$v['score'],1,'docxsfb',$v['id'],0);
   			
   		}
   		 
   		 
   	}
   }
   	

   	
   	}

  }

   	
   
   }

}
