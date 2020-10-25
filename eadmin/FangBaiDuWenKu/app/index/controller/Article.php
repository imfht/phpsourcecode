<?php
namespace app\index\controller;
use app\common\controller\HomeBase;




class Article extends  HomeBase
{
	
	/**
	 * 文章逻辑
	 */
	
	
	public function _initialize()
	{
		parent::_initialize();
	
	
	}
	
	public function index($id){
	
		if($id>0){
			 
			parent::$commonLogic->setDataValue('article',['id'=>$id], 'view', array('exp','view+1'));
	
			$info = parent::$commonLogic->getDataInfo('article',['id'=>$id]);
	
			 
	
			$this->assign('info',$info);
	
		}else{
			 
			$this->error('非法操作', 'index/index');
			 
		}
		return $this->fetch();
	
	}
	 
	public function artlist($id){
		 
		if($id>0){
	
			 
			 
			$artlist = parent::$commonLogic->getDataList('article',['tid'=>$id]);
			 
			$this->assign('id',$id);
			 
			$this->assign('artlist',$artlist);
			 
		}else{
	
			$this->error('非法操作', 'index/index');
	
		}
		return $this->fetch();
		 
	}
   
}
