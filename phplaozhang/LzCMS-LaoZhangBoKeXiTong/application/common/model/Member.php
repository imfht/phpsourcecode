<?php
namespace app\common\model;

use think\Model;

/**
* 
*/
class Member extends Model
{
	
	function initialize()
	{
		parent::initialize();
	}
	//添加会员
	function add($params){
		$params['last_login_time'] = $params['update_time'] = $params['create_time'] = date('Y-m-d H:i:s'); 
		$result = $this->isUpdate(false)->allowField(true)->save($params);
		if($result){
			return $this->id;
		}else{
			return false;
		}
	}
	//修改会员
	function edit($params){
		$params['update_time'] = date('Y-m-d H:i:s');
		$result = $this->isUpdate(true)->allowField(true)->save($params);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	//批量操作
	/**
	* 批量操作
	* $act  操作类型 delete
	* $params 参数
	*/
	function batches($act,$params){
		if($act == 'delete'){
			$result = $this->destroy($params);
		}
		if($result){
			return true;
		}else{
			return false;
		}
	}

	/**
	* 获取列表
	* 
	* 
	*/
	function get_list($where = '',$order = 'id desc',$page_size=15,$whith_page=0){
		if(!$order){ $order = 'id desc';}
		if($whith_page == 0){
			$members = $this->where($where)->order($order)->limit($page_size)->select();
			return $members;
		}
		$members = $this->where($where)->order($order)->paginate($page_size);
		if($whith_page == 1){
			return $members;
		}
		$members = $members->toArray();
		return $members;
	}

}