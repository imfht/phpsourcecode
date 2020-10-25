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
use Think\Cache;
class WebConfigModel extends Model{
	
	protected $cache;
 	function __construct() {
 		$this->cache   = Cache::getInstance();
 	}
 	/**
	 +----------------------------------------------------------
	 * 保存网站设置
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $param
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function save_website_config($param){

   		if (empty($param['pname_cn'])){
   			throw new Exception("请填写平台名称", 1);
   		}
   		if (empty($param['version_major'])){
   			throw new Exception("请填写平台版本号", 1);
   		}
   		if (empty($param['version_major'])){
   			throw new Exception("请填写平台版本号", 1);
   		}
   		if (empty($param['copyright_cn'])){
   			throw new Exception("请填写平台底部版权信息", 1);
   		}
   		if (empty($param['web_site'])){
   			throw new Exception("请填写平台当前地址", 1);
   		}
   		
   		if (empty($param['logo'])){
   			throw new Exception("请上传logo", 1);
   			
   		}
   	

   		$config = array(
   			'COPYRIGHT'	=> array(
   				'pname_cn'	=> $param['pname_cn'],
   				'copyright_cn'	=> $param['copyright_cn'],
   				'version_major'	=> $param['version_major'],
   				'logo'			=> $param['logo'],
   			),
   			'WEB_SITE'			=> $param['web_site'],
   			

   		);
  
   		//保存数据
   		$this->update_config($config);
   		
 	}
 	/**
	 +----------------------------------------------------------
	 * 保存网站设置
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $param
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function save_auth_config($param){
 		
   		
   		

   		$config = array(
   			
   		
   			'QQ_APP_ID'			=> $param['qq_app_id'],
   			'QQ_APP_KEY'			=> $param['qq_app_key'],
   			'WEIBO_APP_KEY'			=> $param['weibo_app_key'],
   			'WEIBO_APP_SECRET'			=> $param['weibo_app_secret'],
   			
   		);
  
   		//保存数据
   		$this->update_config($config);
   		
 	}
 	/**
	 +----------------------------------------------------------
	 * 保存网站设置
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $param
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
 	public function save_sms_config($param){
 		
   		if (empty($param['sms_message'])){
   			throw new Exception("请填写认证验证码短信语", 1);
   		}
   		if (empty($param['sms_reg'])){
   			throw new Exception("请填写注册验证码短信语", 1);
   		}
   		if (empty($param['sms_user'])){
   			throw new Exception("请填写短信接口用户名", 1);
   		}
   		if (empty($param['sms_password'])){
   			throw new Exception("请填写短信接口密码", 1);
   		}

   		$config = array(
   			'SMS_MESSAGE'		=> $param['sms_message'],
   			'SMS_REG'			=> $param['sms_reg'],
   			'SMS_USER'			=> $param['sms_user'],
   			'SMS_PASSWORD'		=> $param['sms_password'],
   		);
  
   		//保存数据
   		$this->update_config($config);
   		
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
	public function upload_logo($imagename, $type){
		if ($type != 'logo'){
			$type = $type.'/default';
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
 	//修改配置文件
	protected function update_config($new_config) {
		$config_file = CONF_PATH . '/site.php';
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
}