<?php
namespace app\common\model;

use think\Model;

/**
* 图片模型类
*/
class Picture extends Model
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
	* 获取图片列表
	* $whith_page 0不分页  1分页返回对象  2分页返回数组
	* 
	*/
	function get_list($where = '',$order = 'id desc',$page_size=15,$whith_page=0){
		if(!$order){ $order = 'id desc';}
		if($whith_page == 0){
			$pictures = $this->where($where)->order($order)->limit($page_size)->select();
			return $pictures;
		}
		$pictures = $this->where($where)->order($order)->paginate($page_size);
		if($whith_page == 1){
			return $pictures;
		}
		$pictures = $pictures->toArray();
		foreach ($pictures['data'] as $k => $v) {
			$pictures['data'][$k]['category_name'] = cache('categorys')[$v['category_id']]['name'];
		}
		return $pictures;
	}
	/**
	* 获取详情
	*/
	function get_details($id){
		$result = $this->get($id);
		$picture = $result->toArray();
		$next = $this->where('id','<',$id)->order('id desc')->find();
		$prev = $this->where('id','>',$id)->order('id asc')->find();
		$prev = $prev?$prev->toArray():array('title'=>'返回列表','url'=>url('index/picture/lists',['category_id'=>$picture['category_id']]));
		$next = $next?$next->toArray():array('title'=>'返回列表','url'=>url('index/picture/lists',['category_id'=>$picture['category_id']]));
		$picture['prev'] = array('title'=>$prev['title'],'url'=>$prev['url']);
		$picture['next'] = array('title'=>$next['title'],'url'=>$next['url']);
		return $picture;
	}

}