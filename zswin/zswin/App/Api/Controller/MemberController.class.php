<?php
namespace Api\Controller;

class MemberController extends ApiController
{

	protected $model;
	public function _initialize() {
		parent::_initialize();
		
		
	}
	
	
	public function getMember($order,$field,$row,$limit){
	
		$map['status']=1;
		
		if($field!=''){
			
			$field='space_url,nickname,avatar32,avatar64,username,score,'.$field;
			
			
		}else{
		   $field='space_url,nickname,avatar32,avatar64,username,score';	
		}
		$p=I(C('VAR_PAGE'));
	if($limit){
		$data=M('member')->where($map)->order($order)->limit($row)->select();	
		}else{
		$data=M('member')->where($map)->order($order)->page(!empty($p)?$p:1,$row)->select();	
		}
		
		
		$fiearr=explode(',', $field);
		
		foreach ($data as $key =>$vo){
			//clean_query_user_cache($vo['uid'], $fiearr);
			$data[$key]['user']=query_user($fiearr,$vo['uid']);
				
        }
	
		
		if($data==null){
			$this->apiError("获取用户列表失败", null);
		}else{
		    $this->apiSuccess("获取用户列表成功", null, array('data'=>$data));	
		}
		
		
	}
	
}