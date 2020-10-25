<?php
namespace app\common\model;

use think\Model;

/**
* 
*/
class Page extends Model
{
	
	function initialize()
	{
		parent::initialize();
		$this->url = '';
	}

	function edit($params,$isUpdate){
		$params['create_time'] = date('Y-m-d H:i:s');
		$result = $this->allowField(true)->isUpdate($isUpdate)->save($params);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	/**
	* 获取单页详情
	*/
	function get_details($category_id){
		$result = $this->get(['category_id'=>$category_id]);
		if($result){
			return $result->toArray();
		}else{
			return false;
		}
	}
	/**
	* 获取列表
	* $whith_page 0不分页  1分页返回对象  2分页返回数组
	* 
	*/
	function get_list($where = '',$order = 'id desc',$page_size=15,$whith_page=0){
		if(!$order){ $order = 'id desc';}
		if($whith_page == 0){
			$pages = $this->where($where)->order($order)->limit($page_size)->select();
			return $pages;
		}
		$pages = $this->where($where)->order($order)->paginate($page_size);
		if($whith_page == 1){
			foreach ($pages as $k => $v) {
				$pages[$k]->url =  url('index/page/index',['category_id'=>$v['category_id']]);
				$pages[$k]->data['url'] = url('index/page/index',['category_id'=>$v['category_id']]);

			}
			return $pages;
		}
		$pages = $pages->toArray();
		foreach ($pages['data'] as $k => $v) {
			$pages['data'][$k]['category_name'] = cache('categorys')[$v['category_id']]['name'];
		}
		return $pages;
	}
}