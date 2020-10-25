<?php
namespace app\index\controller;
use app\common\controller\HomeBase;




class Cidian extends  HomeBase
{
	
	/**
	 * 文章逻辑
	 */
	
	

	
	public function _initialize()
	{
		parent::_initialize();
		
		
	}
   public function fengmian(){
 
   	$catelist=parent::$commonLogic->getDataList('articlecate',['pid'=>0,'id'=>array('in','4,5,6,7,8,9,10,11,12,13')],true,'sort desc,create_time desc',false);
   	
   	$this->assign('catelist',$catelist);
   	return $this->fetch();
   	
   }
   public function index(){
 
   	
   	empty($this->param['cid']) ? $cid = 4 : $cid = $this->param['cid'];

   	$info=parent::$commonLogic->getDataInfo('articlecate',['id'=>$cid]);
   	
   	$catelist = parent::$commonLogic->getDataList('articlecate',['pid'=>0,'id'=>array('in','4,5,6,7,8,9,10,11,12,13')],true,'sort desc,create_time desc',false);
   	
   	
   	$list = parent::$commonLogic->getDataList('article',['tid'=>$cid]);
   	
   	$this->assign('list',$list);
   	$this->assign('catelist',$catelist);
   	$this->assign('info',$info);
   	$this->assign('cid',$cid);
   	return $this->fetch();
   	
   }
   public function content($id){
   	$info = parent::$commonLogic->getDataInfo('article',['id'=>$id]);
   	$this->assign('info',$info);
   	return $this->fetch();
   
   }

   
}
