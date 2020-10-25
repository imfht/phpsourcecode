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
class MerchantsMicroStationSlideModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('merchants_micro_station_slide');
 		$this->cache   = Cache::getInstance();
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取商家微站幻灯片
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_slide_list(){
 		$list = $this->handler->select();
 		if (!$list){
 			$list = C('DEFAULT_STATION_SLIDE');
 			foreach ($list as $key => &$value) {
 				$value['image'] = 'default/'.$value['image'];
 			}
 		}
 		return $list;
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取商家微站幻灯片数量
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_station_slide_count(){
 		return $this->handler->count();
 	}
 	/**
	 +----------------------------------------------------------
	 * 根据商家编号获取商家微站幻灯片(后台)
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function get_station_slide_list_by_userid(){
		return $this->handler->select();
	}
	/**
	 +----------------------------------------------------------
	 * 根据编号获取商家微站幻灯片
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function get_station_slide_info_by_id($id){
		return $this->handler->where(array('id'=>$id))->find();
	}
 	/**
	 +----------------------------------------------------------
	 * 添加幻灯片
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function add_slide($param){
		



		$upload = new \Think\Upload();// 实例化上传类    
		$upload->maxSize    = 2000000 ;// 设置附件上传大小    

		$upload->exts       = array('jpg', 'gif', 'png');// 设置附件上传类型    
		$upload->rootPath   = STATIC_PATH;   
		$upload->savePath   = 'upload/station_slide/'; // 设置附件上传目录    
		$upload->saveName   = array('uniqid','');
		$upload->autoSub    = false;
		$upload->replace    = true;
		// 上传文件     
		$info   =   $upload->upload();    
		if(!$info) {
		   	// 上传错误提示错误信息          
		   	throw new Exception($upload->getError(), 1);
		}

		$info['fileToUpload']['imagename'] = $info['fileToUpload']['savename'];
		$rs = $this->handler->add(array(
		   	'sort'   => 1,
		   	'image'   => $info['fileToUpload']['savename'],
		   	'create_datetime'   => date('Y-m-d H:i:s')
		));
		if (!$rs){
		   	$imagename = 'upload/station_slide/'.$info['fileToUpload']['savename'];
		   	//删除原图片
		   	@unlink($imagename);
		   	throw new Exception("添加广告失败", 1);
		}
		return $info['fileToUpload'];


	}
	/**
	 +----------------------------------------------------------
	 * 编辑幻灯片
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function edit_slide($param){
		$id = intval($param['id']);
		if ($id == 0){
			throw new Exception("请选择要编辑的幻灯片", 1);
		}
		$slide_info = $this->get_station_slide_info_by_id($id);
		if (!$slide_info){
			throw new Exception("幻灯片不存在，请重试", 1);
		}
		
		return $this->handler->where(array('id'=>$id))->save(array(
			'sort'	=> intval($param['sort']),
			'url'	=> $param['url'],
		));
	}
	/**
	 +----------------------------------------------------------
	 * 删除幻灯片
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $user_id
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function del_slide($id){
		if (empty($id)){
			throw new Exception("请选择要删除的幻灯片", 1);
		}
		$slide_list = $this->handler->where(array('id'=>array('IN', $id)))->select();
		if(!$slide_list){
			throw new Exception("没有发现可删除的幻灯片", 1);
		}
		//获取要删除的id
		$del_id = '';
		foreach ($slide_list as $key => $value) {
			$del_id .= $value['id'] . ',';
			//删除图片
			@unlink('upload/station_slide/'.$value['image']);
		}
		$del_id = rtrim($del_id, ',');
		return $this->handler->where(array('id'=>array('IN', $id)))->delete();
	}
}