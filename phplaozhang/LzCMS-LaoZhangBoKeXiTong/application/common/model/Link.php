<?php
namespace app\common\model;

use think\Model;

/**
* 链接模型类
*/
class Link extends Model
{
	
	function initialize()
	{
		parent::initialize();
	}

	//添加链接
	function add($params){
		$result = $this->isUpdate(false)->allowField(true)->save($params);
		if($result){
			return true;
		}else{
			return false;
		}
	}

	//修改链接
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
		}elseif($act == 'move'){
			$ids = $params['ids'];
			$to_category_id = $params['to_category_id'];
			$result = $this->where('id','in',$ids)->update(['category_id'=>$to_category_id]);
		}
		if($result){
			return true;
		}else{
			return false;
		}
	}

	/**
	* 获取链接列表
	* 
	* 
	*/
	function get_list($where = '',$order = 'id desc',$page_size=15,$whith_page=0){
		if(!$order){ $order = 'id desc';}
		if($whith_page == 0){
			$links = $this->where($where)->order($order)->limit($page_size)->select();
			return $links;
		}
		$links = $this->where($where)->order($order)->paginate($page_size);
		if($whith_page == 1){
			return $links;
		}
		$links = $links->toArray();
		foreach ($links['data'] as $k => $v) {
			$links['data'][$k]['category_name'] = cache('categorys')[$v['category_id']]['name'];
		}
		return $links;
	}

}