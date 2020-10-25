<?php
namespace app\index\controller;
use app\common\controller\HomeBase;



class Comment extends  HomeBase
{
	
	public function _initialize()
	{
		parent::_initialize();
		
	}
	
   public function addcomment(){
   	
   $data=$this->param;
   $data['uid']=session('member_info')['id'];

   $where['uid']=$data['uid'];
   $where['fid']=$data['fid'];

   $info = parent::$commonLogic->getDataInfo('comment',$where);
   
   if(time()-$info['create_time']<60){
   	$this->jump([RESULT_ERROR,'两次评论时间过短']);
   }
   
   	$obj=new Comment();
   	
   	
   	$this->jump(parent::$commonLogic->dataAdd('comment',$data,true,'添加评论成功',$obj,'addcomment_callback'));
   
   
   	
   	
   }
   public function addcomment_callback($result,$data){
   	
   	$info=parent::$commonLogic->getDataInfo('doccon',['id'=>$data['fid']]);
   	
   	$content='您的文档<a href="'.url('doc/doccon',array('id'=>$info['id'])).'">'.$info['title'].'</a>刚刚被'.getusernamebyid($data['uid']).'评论了';
   	
   	sendsysmess($content,0,$info['uid'],1);
   	
   	if($data['tid']>0){
   		
   		$cinfo = parent::$commonLogicic->getDataInfo('comment',['id'=>$data['tid']]);
   		
   		model('comment')->where(['id'=>$data['tid']])->setInc('reply');
   		
   		$content=getusernamebyid($data['uid']).'刚刚在<a href="'.url('doc/doccon',array('id'=>$info['id'])).'">'.$info['title'].'</a>回复了您的评论,回复如下:<br>'.$data['content'];
   		
   		sendsysmess($content,$data['uid'],$cinfo['uid'],1);
   		
   		
   	}
   	
   	
   	
   	
   }
   
   
   
}
