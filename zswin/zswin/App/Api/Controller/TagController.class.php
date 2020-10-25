<?php
namespace Api\Controller;

class TagController extends ApiController
{

	protected $model;
	public function _initialize() {
		parent::_initialize();
		$this->model=D('tags');
		
	}
	public function getTagsCount($type){
	
	   
			$map['type']=array('in',$type);
		
		
		$data=$this->model->where($map)->count();
		
		if($data==null){
			$this->apiError("获取标签数失败", null);
		}else{
		    $this->apiSuccess("获取标签数成功", null, array('data'=>$data));	
		}
		
		
		
		
		
	}
	
	public function getTags($order,$field,$row,$limit,$type){
	
		
			$map['type']=array('in',$type);
	
		
		
		$p=I(C('VAR_PAGE'));
	    
	    if($limit){
		$data=$this->model->where($map)->order($order)->limit($row)->select();	
		}else{
		$data=$this->model->where($map)->order($order)->page(!empty($p)?$p:1,$row)->select();	
		}
		
		foreach ($data as $key =>$vo){
			
			$data[$key]['path']=getThumbImageById($vo['img']);
			$data[$key]['tagcolor']=colorCallback();
			$data[$key]['tagback']=colorbackCallback();
		}
		
	
		
		if($data==null){
			$this->apiError("获取标签列表失败", null);
		}else{
		    $this->apiSuccess("获取标签列表成功", null, array('data'=>$data));	
		}
		
		
	}
	
}