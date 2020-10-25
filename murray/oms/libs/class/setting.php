<?php 
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 系统配置类
*/

defined('INPOP') or exit('Access Denied');

class setting extends Model{

	//初始化
    public function __construct(){
		parent::__construct("settings", "skey");
    }
	
	//获取值
	public function getvalue($key){
		if(!$key) return false;
		$setting = $this->getOne($key);
		return $setting['svalue'];
	}
	
	//设置值
	public function setValue($key, $value){
		if(!$key) return false;
		$setting = $this->getOne($key, false);
		if(empty($setting)){
			$_add = array();
			$_add['skey'] = $key;
			$_add['svalue'] = $value;
			$done = $this->add($_add);
		}else{
			$_edit = array();
			$_edit['svalue'] = $value;
			$this->keyId = $key;
			$done = $this->edit($_edit);
		}
		return $done;
	}

}
?>