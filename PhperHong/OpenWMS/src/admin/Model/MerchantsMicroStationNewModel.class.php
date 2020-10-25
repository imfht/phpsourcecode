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
class MerchantsMicroStationNewModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('merchants_micro_station_new');
 		$this->cache   = Cache::getInstance();
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取商家新闻列表
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_new_list(){
 		$list = $this->handler->field('id, user_id, title, summary, create_datetime')->order('id desc')->select();
 		
 		return $list;
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取商家新闻数量
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_new_count(){
 		return $this->handler->count();
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取商家新闻列表（后台）
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_new_list_by_userid(){
 		$list = $this->handler->order('id desc')->select();
 		//清楚html
 		foreach ($list as $key => &$value) {
 			$value['subcontent'] = mb_substr(strip_tags($value['content']), 0, 100, 'utf-8').'...';
 		}
 		return $list;
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据新闻编号及用户编号获取新闻详情
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_new_info_by_id($id){
 		if (empty($id)){
 			return false;
 		}
 		$new_info = $this->handler->where(array('id'=>$id))->find();
 		
 		return $new_info;
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据新闻编号获取新闻详情
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_new_info($id){
 		return $this->handler->where(array('id'=>$id))->find();
 	}
 	/**
	 +----------------------------------------------------------
	 * 添加新闻
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function add_new($param){
 		if (empty($param['title'])){
 			throw new Exception("请填写标题", 1);
 		}
 		if (empty($param['content'])){
 			throw new Exception("请填写内容", 1);
 		}
 		//获取摘要
 		$summary = mb_substr(strip_tags($param['content']), 0, 100, 'utf-8');
 		return $this->handler->add(array(
 			
 			'title'		=> $param['title'],
 			'summary'	=> $summary,
 			'content'	=> $param['content'],
 			'create_datetime'	=> date('Y-m-d H:i:s'),
 		));
 	}
 	/**
	 +----------------------------------------------------------
	 * 编辑新闻
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function edit_new($param){
 		$id = intval($param['id']);
 		if ($id == 0){
 			throw new Exception("请选择要编辑的新闻", 1);
 		}
 		if (empty($param['title'])){
 			throw new Exception("请填写标题", 1);
 		}
 		if (empty($param['content'])){
 			throw new Exception("请填写内容", 1);
 		}
 		$new_info = $this->get_station_new_info($id);

 		if (!$new_info){
 			throw new Exception("新闻不存在", 1);
 		}
 		
 		//获取摘要
 		$summary = mb_substr(strip_tags($param['content']), 0, 100, 'utf-8');
 		$rs = $this->handler->where(array('id'=>$id))->save(array(
 			
 			'title'		=> $param['title'],
 			'summary'	=> $summary,
 			'content'	=> $param['content'],
 		));

 		return $rs;
 	}
 	/**
	 +----------------------------------------------------------
	 * 删除新闻
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function del_new($id){
		if (empty($id)){
			throw new Exception("请选择要删除的新闻", 1);
		}
		
		$new_list = $this->handler->where(array('id'=>array('IN', $id)))->select();
		if(!$new_list){
			throw new Exception("没有发现可删除的新闻", 1);
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