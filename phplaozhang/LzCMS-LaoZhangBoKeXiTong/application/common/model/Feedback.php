<?php
namespace app\common\model;

use think\Model;

/**
* 留言模型类
*/
class Feedback extends Model
{
	
	function initialize()
	{
		parent::initialize();
	}

	//添加留言
	function add($params){
		$result = $this->isUpdate(false)->allowField(true)->save($params);
		if($result){
			return true;
		}else{
			return false;
		}
	}

	//修改留言
	function edit($params){
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
	* 获取留言列表
	* 
	* 
	*/
	function get_list($where = '',$order = 'id desc',$page_size=15,$whith_page=0){
		if(!$order){ $order = 'id desc';}
		if($whith_page == 0){
			$feedbacks = $this->view('feedback','*')->view('member','nickname,avatar','feedback.member_id = member.id','LEFT')->where($where)->order($order)->limit($page_size)->select();
			return $feedbacks;
		}
		$feedbacks = $this->view('feedback','*')->view('member','nickname,avatar','feedback.member_id = member.id','LEFT')->where($where)->order($order)->paginate($page_size);
		if($whith_page == 1){
			return $feedbacks;
		}
		$feedbacks = $feedbacks->toArray();
		foreach ($feedbacks['data'] as $k => $v) {
			$feedbacks['data'][$k]['category_name'] = cache('categorys')[$v['category_id']]['name'];
		}
		return $feedbacks;
	}

}