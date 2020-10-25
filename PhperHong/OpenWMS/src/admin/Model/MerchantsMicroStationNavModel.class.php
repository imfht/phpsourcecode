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
class MerchantsMicroStationNavModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('merchants_micro_station_nav');
 		$this->cache   = Cache::getInstance();
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取商家导航
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_nav_list(){
 		$sys_nav_list = C('STATION_NAV_LIST');
 		$list = $this->handler->select();
 		if (!$list){
 			return $sys_nav_list;
 		}
 		//剔除禁用的并将用户自定义的名称及导航加入到列表中
 		foreach ($list as $key => $value) {
 			if ($value['status'] == 0 && $sys_nav_list[$value['nav_id']]){
 				unset($sys_nav_list[$value['nav_id']]);
 				break;
 			}
 			if (!$sys_nav_list[$value['nav_id']]){
 				$sys_nav_list[] = $value;
 			}else{
 				$sys_nav_list[$value['nav_id']]['nav_name'] = $value['nav_name'];
 				$sys_nav_list[$value['nav_id']]['nav_image'] = $value['nav_image'];
 				$sys_nav_list[$value['nav_id']]['sort'] = $value['sort'];
 			}
 		}
 		//数组排序
 		$sys_nav_list = multi_array_sort($sys_nav_list, 'sort');

 		return $sys_nav_list;
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取商家导航(后台)
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_nav_list_by_userid(){
 		$sys_nav_list = C('STATION_NAV_LIST');
 		$list = $this->handler->select();

 		if (!$list){
 			return $sys_nav_list;
 		}
 		//剔除禁用的并将用户自定义的名称及导航加入到列表中
 		foreach ($list as $key => $value) {
 			if (!$sys_nav_list[$value['nav_id']]){
 				$sys_nav_list[] = $value;
 			}else{
 				$sys_nav_list[$value['nav_id']]['id'] = $value['id'];
 				$sys_nav_list[$value['nav_id']]['nav_name'] = $value['nav_name'];
 				$sys_nav_list[$value['nav_id']]['nav_image'] = $value['nav_image'];
 				$sys_nav_list[$value['nav_id']]['sort'] = $value['sort'];
 				$sys_nav_list[$value['nav_id']]['status'] = $value['status'];
 			}
 		}
 		//数组排序
 		$sys_nav_list = multi_array_sort($sys_nav_list, 'sort');

 		return $sys_nav_list;
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据编号及商家编号获取商家导航(后台)
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function get_station_nav_info_by_id($id, $nav_id){
		if(empty($id) && empty($nav_id)){
			return false;
		}
		if (intval($id) == 0){
 			//通过nav_id查找是否在系统导航中
 			$sys_nav_list = C('STATION_NAV_LIST');
 			$nav_info = $sys_nav_list[$nav_id];
 			if (!$nav_info){
 				throw new Exception("未知的导航，请确认", 1);
 			}
 			return $nav_info;
 		}

		
		return $this->handler->where(array('id'=>$id))->find();
	}
 	/**
	 +----------------------------------------------------------
	 * 根据编号获取商家导航
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function get_nav_info_by_id($id){
		return $this->handler->where(array('id'=>$id))->find();
	}

 	/**
	 +----------------------------------------------------------
	 * 添加导航
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function add_nav($param){
 		
 		if (empty($param['nav_name'])){
 			throw new Exception("请填写导航名称", 1);
 		}
 		if (empty($param['nav_image'])){
 			throw new Exception("请选择一个图标", 1);
 		}
 		if(empty($param['nav_href'])){
 			throw new Exception("请填写导航链接", 1);
 		}
 		if (intval($param['sort']) == 0){
 			$param['sort'] = 1;
 		}
 		return $this->handler->add(array(
 			'nav_name'	=> $param['nav_name'],
 			'nav_image'	=> $param['nav_image'],
 			'nav_href'	=> $param['nav_href'],
 			'sort'		=> $param['sort'],
 			'status'	=> empty($param['status']) ? 0 : 1,
 			'type'		=> 0,
 			'create_datetime'	=> date('Y-m-d H:i:s'),
 		));
 	}
 	/**
	 +----------------------------------------------------------
	 * 编辑导航
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function edit_nav($param){
 		
 		if (empty($param['nav_name'])){
 			throw new Exception("请填写导航名称", 1);
 		}
 		if (empty($param['nav_image'])){
 			throw new Exception("请选择一个图标", 1);
 		}
 		if(empty($param['nav_href'])){
 			throw new Exception("请填写导航链接", 1);
 		}
 		if (intval($param['sort']) == 0){
 			$param['sort'] = 1;
 		}
 		$nav_info = $this->get_nav_info_by_id($param['id']);
 		$b = false;
 		if (!$nav_info){
 			//通过nav_id查找是否在系统导航中
 			$sys_nav_list = C('STATION_NAV_LIST');
 			$nav_info = $sys_nav_list[$param['nav_id']];
 			if (!$nav_info){
 				throw new Exception("未知的导航，请确认", 1);
 			}
 			$b = true;
 		}else{
 			if ($nav_info['user_id'] != $adminid){
 				throw new Exception("该菜单导航不属于你，你不能进行编辑", 1);
 			}
 		}
 		if ($b){
 			return $this->handler->add(array(
	 			'nav_name'	=> $param['nav_name'],
	 			'nav_image'	=> $param['nav_image'],
	 			'nav_href'	=> $param['nav_href'],
	 			'sort'		=> $param['sort'],
	 			'nav_id'	=> $param['nav_id'],
	 			
	 			'status'	=> empty($param['status']) ? 0 : 1,
	 			'type'		=> $param['type'],
	 			'create_datetime'	=> date('Y-m-d H:i:s'),
	 		));
 		}else{
 			return $this->handler->where(array('id'=>$param['id']))->save(array(
 				'nav_name'	=> $param['nav_name'],
	 			'nav_image'	=> $param['nav_image'],
	 			'nav_href'	=> $param['nav_href'],
	 			'sort'		=> $param['sort'],
	 			'nav_id'	=> $param['nav_id'],
	 			'status'	=> empty($param['status']) ? 0 : 1,
 			));
 		}
 		
 	}
 	/**
	 +----------------------------------------------------------
	 * 删除导航
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function del_nav($id){
 		
 		$nav_info = $this->get_nav_info_by_id($id);
 		
 		if (!$nav_info){
 			throw new Exception("未知的导航，请确认", 1);
 		}
 		if ($nav_info['user_id'] != $adminid){
			throw new Exception("该菜单导航不属于你，你不能进行编辑", 1);
		}
		if($nav_info['type'] == 1){
			throw new Exception("系统导航，不能被删除", 1);
		}
		return $this->handler->where(array(array('id'=>$id)))->delete();
 	}
}