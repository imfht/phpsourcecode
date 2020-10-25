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
class MerchantModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->cache   = Cache::getInstance();
 	}
 	/**
	 +----------------------------------------------------------
	 * 获取配置信息
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_auth_info(){
 		
 		return array (
			'qq_verify' 		=> C('qq_verify'),
			'weibo_verify' 		=> C('weibo_verify'),
			'weixin_verify' 	=> C('weixin_verify'),
			'mobile_verify' 	=> C('mobile_verify'),
			'akey_verify' 		=> C('akey_verify'),
			'virtual_verify' 	=> C('virtual_verify'),
			'url'				=> C('WEIXIN_HREF_URL') . '/?weixintoken='.md5('weixin'),
			'weibo_name' 		=> C('weibo_name'),
			'weixin_name' 		=> C('weixin_name'),
			'weixin_id' 		=> C('weixin_id'),
			'online_times' 		=> C('online_times'),
			'online_times1' 	=> C('online_times1'),
			'online_type' 		=> C('online_type'),
			'rest_online_times' => C('rest_online_times'),
			'href' 				=> C('href'),
			'href_website' 		=> C('href_website'),
			'ad_times' 			=> C('ad_times'),
			'qr_code' 			=> C('qr_code'),
			'one_auth_type' 	=> C('one_auth_type'),
			'one_auth_href' 	=> C('one_auth_href'),
			'two_auth_type' 	=> C('two_auth_type'),
			'two_auth_href' 	=> C('two_auth_href'),
			'old_user_auth_type'=> C('old_user_auth_type'),
			'ad_status' 		=> C('ad_status'),
		);
 	}
 	/**
	 +----------------------------------------------------------
	 * 获取页面配置信息
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_page_info(){
 		return array (
			'homepage_logo' 	=> C('homepage_logo'),
			'homepage_banner' 	=> C('homepage_banner'),
			'shop_name' 		=> C('shop_name'),
			'telephone' 		=> C('telephone'),
		);
 	}
 	/**
	 +----------------------------------------------------------
	 * 获取广告配置信息
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function get_ad_list(){
 		return C('ad_list');
 	}
 	/**
     +----------------------------------------------------------
     * 添加广告
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function add_merchant_ad(){
		$upload = new \Think\Upload();// 实例化上传类    
		$upload->maxSize    = 2000000 ;// 设置附件上传大小    

		$upload->exts       = array('jpg', 'gif', 'png');// 设置附件上传类型    
		$upload->rootPath   = STATIC_PATH;   
		$upload->savePath   = 'upload/merchant_ad/'; // 设置附件上传目录    
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

		$ad_list = C('ad_list');
		$ad_list[] = array(
			'title'	=>'',
			'image' => $info['fileToUpload']['savename'],
		);
		
		$this->update_config(array('ad_list'=>$ad_list));
		
		return $info['fileToUpload'];
    }
     /**
     +----------------------------------------------------------
     * 删除广告
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $id
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function del_merchant_ad($id){
		$ad_list = C('ad_list');
		
	
		//删除图片
		$imagename = 'upload/merchant_ad/'.$ad_list[$id]['image'];
		//删除原图片
		@unlink($imagename);
		unset($ad_list[$id]);
		$this->update_config(array('ad_list'=>$ad_list));
		
		return true;
    }
	/**
	 +----------------------------------------------------------
	 * 获取商家设置的强制重连时间
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function get_merchant_online_times(){
		$online_times = C('online_times');
		$online_times1 = C('online_times1');
		return (intval($online_times)*60+intval($online_times1))*60;
	}
	/**
	 +----------------------------------------------------------
	 * 保存页面设置
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $param 数组
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function save_page($param){
		if (empty($param['shop_name']) || mb_strlen($param['shop_name'], 'utf8') < 2 || mb_strlen($param['shop_name'], 'utf8') > 20){
			throw new Exception("店名不能为空，或者不在范围之内[2-20]", 1);
			return false;	
		}
		if(empty($param['telephone']) || mb_strlen($param['telephone'], 'utf8') < 8 || mb_strlen($param['telephone'], 'utf8') > 20){
			throw new Exception("负责人联系方式不能为空，或者不在范围之内[8-20]", 1);
			return false;	
		}
		if (empty($param['homepage_banner'])){
			throw new Exception("请上传认证页面主图", 1);
			return false;	
		}
		//保存认证设置
		$this->update_config(array(
            'shop_name'     	=> $param['shop_name'],
            'telephone'    		=> $param['telephone'],
            'homepage_banner'   => $param['homepage_banner'],
            'homepage_logo'   	=> $param['homepage_logo'],
		));
		return true;
	}
	/**
	 +----------------------------------------------------------
	 * 认证设置
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $param 数组
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function save_auth($param){
		//检查该商户是否存在

	
		if (intval($param['qq_verify']) == 0 && intval($param['weibo_verify']) == 0 && intval($param['weixin_verify']) == 0 && intval($param['mobile_verify']) == 0 && intval($param['akey_verify']) == 0 && intval($param['virtual_verify']) == 0){
			throw new Exception("请至少选择一个认证方式", 1);
		}

		$rest_online_times = explode('-', $param['rest_online_times']);
		if (!empty($param['online_type']) && strtotime($rest_online_times[1]) - strtotime($rest_online_times[0]) < 0){
			throw new Exception("上网时段控制的起始时间不能大于结束时间", 1);
		}
		if(intval($param['online_times']) > 24){
			throw new Exception("上午时间限制不能大于24小时", 1);
		}
		if ($param['href'] == 'fixedwebsite' && empty($param['href_website'])){
			throw new Exception("请填写跳转的URL", 1);
		}

		
		if ($param['weixin_verify'] == 1 && empty($param['weixin_name'])){
			throw new Exception("请填写微信名称", 1);
		}
		if($param['weibo_verify'] == 1 && empty($param['weibo_name'])){
			throw new Exception("请填写微博名称", 1);
		}
		if($param['weixin_verify'] == 1 && empty($param['qr_code'])){
			throw new Exception("请上传微信二维码", 1);
		}
		if (intval($param['ad_times']) < 3 || intval($param['ad_times'])> 20){
			throw new Exception("广告时间范围为[3-20]秒", 1);
			
		}
		
		//保存认证设置
		
		$this->update_config(array(
            'qq_verify'     	=> intval($param['qq_verify']),
            'weibo_verify'    	=> intval($param['weibo_verify']),
            'weixin_verify'   	=> intval($param['weixin_verify']),
            'mobile_verify'   	=> intval($param['mobile_verify']),
            'akey_verify' 		=> intval($param['akey_verify']),
            'virtual_verify'	=> intval($param['virtual_verify']),
            'weixin_name'		=> $param['weixin_name'],
            'one_auth_type'		=> $param['one_auth_type'],
            'one_auth_href'		=> $param['one_auth_href'],
            'two_auth_type'		=> $param['two_auth_type'],
            'two_auth_href'		=> $param['two_auth_href'],
            'rest_online_times'	=> $param['rest_online_times'],
            'online_type'		=> $param['online_type'],
            'old_user_auth_type'=> empty($param['old_user_auth_type']) ? 0 : 1,
            'ad_status'			=> empty($param['ad_status']) ? 0 : 1,
            'online_times'		=> intval($param['online_times']),
            'online_times1'		=> intval($param['online_times1']),
            'href'   			=> $param['href'],
            'href_website'     	=> $param['href_website'],
            'weibo_name'		=> $param['weibo_name'],
            'qr_code'			=> $param['qr_code'],
            'ad_times'			=> $param['ad_times'],
		));
		return true;
	}
	//修改配置文件
	protected function update_config($new_config) {
		$config_file = CONF_PATH . '/router.php';
		if (is_writable($config_file)) {
			$config = require $config_file;
			$config = array_merge($config, $new_config);
			file_put_contents($config_file, "<?php \nreturn " . var_export($config, true) . ";", LOCK_EX);
			@unlink(RUNTIME_FILE);
			return true;
		} else { 
			return false;
		}
	}
	
	/**
	 +----------------------------------------------------------
	 * 上传图片
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $type
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function upload_merchant_logo_and_banner($type, $imagename){
		if ($type != 'merchantlogo' && $type != 'merchantbanner' && $type != 'merchantqrcode' && $type != 'merchant_ad' && $type != 'station_slide' && $type != 'station_product' && $type != 'station_activity'){
			throw new Exception("未知类型", 1);
		}
		$imagename = 'upload/'.$type.'/'.$imagename;
		if (!empty($imagename) && is_file($imagename)){
			//删除原图片
			$rs = @unlink($imagename);
			if (!$rs){
				throw new Exception("历史图片删除失败", 1);
				
			}
		}

		$upload = new \Think\Upload();// 实例化上传类    
		$upload->maxSize   	= 2000000 ;// 设置附件上传大小    

		$upload->exts      	= array('jpg', 'gif', 'png');// 设置附件上传类型    
		$upload->rootPath  	= STATIC_PATH;   
		$upload->savePath  	= 'upload/'.$type.'/'; // 设置附件上传目录    
		$upload->saveName 	= array('uniqid','');
		$upload->autoSub 	= false;
		$upload->replace 	= true;
		// 上传文件     
		$info   =   $upload->upload();    
		if(!$info) {
			// 上传错误提示错误信息          
			throw new Exception($upload->getError(), 1);
		}

		$info['fileToUpload']['imagename'] = $info['fileToUpload']['savename'];
		return $info['fileToUpload'];
	}
	
}