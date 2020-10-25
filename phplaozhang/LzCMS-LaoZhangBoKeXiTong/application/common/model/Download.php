<?php
namespace app\common\model;

use think\Model;

/**
* 下载模型类
*/
class Download extends Model
{
	
	function initialize()
	{
		parent::initialize();
	}

	//添加图片
	function add($params){
		$result = $this->isUpdate(false)->allowField(true)->save($params);
		if($result){
			return true;
		}else{
			return false;
		}
	}

	//修改图片
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
	* 获取列表
	* 
	* 
	*/
	function get_list($where = '',$order = 'id desc',$page_size=15,$whith_page=0){
		if(!$order){ $order = 'id desc';}
		if($whith_page == 0){
			$downloads = $this->where($where)->order($order)->limit($page_size)->select();
			return $downloads;
		}
		$downloads = $this->where($where)->order($order)->paginate($page_size);
		if($whith_page == 1){
			return $downloads;
		}
		$downloads = $downloads->toArray();
		foreach ($downloads['data'] as $k => $v) {
			$downloads['data'][$k]['category_name'] = cache('categorys')[$v['category_id']]['name'];
		}
		return $downloads;
	}

	/**
	* 获取文章详情
	*/
	function get_details($id){
		$result = $this->get($id);
		$download = $result->toArray();
		return $download;
	}

}