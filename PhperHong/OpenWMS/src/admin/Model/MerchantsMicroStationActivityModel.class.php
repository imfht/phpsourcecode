<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Model;
use Think\Model;
use Think\Exception;
use Think\Log;
use Think\Cache;
class MerchantsMicroStationActivityModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('merchants_micro_station_activity');
 		$this->cache   = Cache::getInstance();
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取商家
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_activity_list(){
 		$list = $this->handler->field('id, user_id, title, summary, thumb, start_datetime, end_datetime, create_datetime')->order('id desc')->select();
 		foreach ($list as $key => &$value) {
 	
 			if ($value['start_datetime']<time() && $value['end_datetime'] > time()){
 				$value['status'] = 1;
 			}else if ($value['start_datetime']>time() ){
 				$value['status'] = 2;
 			}else if($value['end_datetime']<time() ){
 				$value['status'] = 0;
 			}
 				
 		}
 		return $list;
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取数量
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_activity_count(){
 		$list = $this->handler->field('start_datetime, end_datetime')->select();
 		$count = array('status0'=>0, 'status1'=>0, 'status2'=>0);
 		foreach ($list as $key => &$value) {
 	
 			if ($value['start_datetime']<time() && $value['end_datetime'] > time()){
 				$count['status1']++;
 			}else if ($value['start_datetime']>time() ){
 				$count['status2']++;
 			}else if($value['end_datetime']<time() ){
 				$count['status0']++;
 			}
 				
 		}
 		return $count;
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号及编号获取活动信息
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_activity_info_by_id($id){
 		if (empty($id)){
 			return false;
 		}
 		$product_info = $this->handler->where(array('id'=>$id))->find();
 		
 		return $product_info;
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取商家（后台）
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_activity_list_by_userid(){
 		$list = $this->handler->order('id desc')->select();
 		foreach ($list as $key => &$value) {
 	
 			if ($value['start_datetime']<time() && $value['end_datetime'] > time()){
 				$value['status'] = 1;
 			}else if ($value['start_datetime']>time() ){
 				$value['status'] = 2;
 			}else if($value['end_datetime']<time() ){
 				$value['status'] = 0;
 			}
 			$value['start_datetime'] = date('Y-m-d H:i:s', $value['start_datetime']);	
 			$value['end_datetime'] = date('Y-m-d H:i:s', $value['end_datetime']);	
 			$value['subcontent'] = mb_substr(strip_tags($value['content']), 0, 100, 'utf-8').'...';	
 		}
 		return $list;
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据活动编号获取详情
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_activity_info($id){
 		$info = $this->handler->where(array('id'=>$id))->find();
 		if ($info['start_datetime']<time() && $info['end_datetime'] > time()){
			$info['status'] = 1;
		}else if ($info['start_datetime']>time() ){
			$info['status'] = 2;
		}else if($info['end_datetime']<time() ){
			$info['status'] = 0;
		}
		return $info;
 	}
 	/**
	 +----------------------------------------------------------
	 * 添加活动
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function add_activity($param){
 		if (empty($param['title'])){
 			throw new Exception("请填写标题", 1);
 		}
 		if (empty($param['content'])){
 			throw new Exception("请填写内容", 1);
 		}

 		if(empty($param['start_datetime'])){
 			throw new Exception("请填写开始时间", 1);
 		}
 		if(empty($param['end_datetime'])){
 			throw new Exception("请填写结束时间", 1);
 		}
 		if (strtotime($param['start_datetime']) >= strtotime($param['end_datetime'])){
 			throw new Exception("结束时间必须大于开始时间", 1);
 		}

 		//获取摘要
 		$summary = mb_substr(strip_tags($param['content']), 0, 100, 'utf-8');
 		return $this->handler->add(array(
 			
 			'title'		=> $param['title'],
 			'summary'	=> $summary,
 			'start_datetime'	=> strtotime($param['start_datetime']),
 			'end_datetime'	=> strtotime($param['end_datetime']),
 			'thumb'		=> $param['thumb'],
 			'content'	=> $param['content'],
 			'create_datetime'	=> date('Y-m-d H:i:s'),
 		));
 	}
 	/**
	 +----------------------------------------------------------
	 * 编辑活动
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function edit_activity($param){
 		$id = intval($param['id']);
 		if ($id == 0){
 			throw new Exception("请选择要编辑的产品", 1);
 		}
 		if (empty($param['title'])){
 			throw new Exception("请填写标题", 1);
 		}
 		if (empty($param['content'])){
 			throw new Exception("请填写内容", 1);
 		}
 		if(empty($param['start_datetime'])){
 			throw new Exception("请填写开始时间", 1);
 		}
 		if(empty($param['end_datetime'])){
 			throw new Exception("请填写结束时间", 1);
 		}
 		if (strtotime($param['start_datetime']) >= strtotime($param['end_datetime'])){
 			throw new Exception("结束时间必须大于开始时间", 1);
 		}
 		$product_info = $this->get_station_activity_info($id);

 		if (!$product_info){
 			throw new Exception("产品不存在", 1);
 		}

 		//获取摘要
 		$summary = mb_substr(strip_tags($param['content']), 0, 100, 'utf-8');
 		return $this->handler->where(array('id'=>$id))->save(array(
 			
 			'title'		=> $param['title'],
 			'summary'	=> $summary,
 			'start_datetime'	=> strtotime($param['start_datetime']),
 			'end_datetime'	=> strtotime($param['end_datetime']),
 			'thumb'		=> $param['thumb'],
 			'content'	=> $param['content'],
 		));
 	}
 	/**
	 +----------------------------------------------------------
	 * 删除活动
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function del_activity($id){
		if (empty($id)){
			throw new Exception("请选择要删除的产品", 1);
		}
		$new_list = $this->handler->where(array('id'=>array('IN', $id)))->select();
		if(!$new_list){
			throw new Exception("没有发现可删除的产品", 1);
		}
		//获取要删除的id
		$del_id = '';
		foreach ($new_list as $key => $value) {
			$del_id .= $value['id'] . ',';
		}
		$del_id = rtrim($del_id, ',');
		return $this->handler->where(array('id'=>array('IN', $id)))->delete();
	}
 	
}