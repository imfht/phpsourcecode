<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_setting;
use admin\model\_manager;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//后台入口
class index{
	//登录
	public function index(){
		global $_var, $setting, $ADMIN_SCRIPT;
		
		$_setting = new _setting();
		$_manager = new _manager();
		
		if(isset($_SESSION['_input_count'])) $input_limit = $_SESSION['_input_count'] + 0;
		else $_SESSION['_input_count'] = 0;
		
		if($_var['gp_formsubmit']){
			if(empty($_var['gp_txtUserName'])) $_var['msg'] = $GLOBALS['lang']['admin.login.validate.username'];
			elseif($input_limit >= 3){
				if(empty($_var['gp_txtSeccode'])) $_var['msg'] = $GLOBALS['lang']['admin.login.validate.seccode'];
				elseif(strtolower($_var['gp_txtSeccode']) != $_SESSION['_seccode']) $_var['msg'] = $GLOBALS['lang']['admin.login.validate.seccode.error'];
			}
			
			if(empty($_var['msg'])){
				$_var['current'] = null;
				if($_var['gp_txtUserName'] == 'administrator'){
					if(md5($_var['gp_txtPassword']) == $setting['AdminPassword']){
						$salt = '-1,'.random(64);
						
						$_var['current'] = array(
						'U_TYPE' => 1, 
						'USERID' => -1, 
						'USERNAME' => 'administrator', 
						'PASSWD' => $setting['AdminPassword'], 
						'REALNAME' => $GLOBALS['lang']['admin.login.administrator'], 
						'LOGINTIME' => date('Y-m-d H:i:s'), 
						'ISMANAGER' => 1, 
						'SALT' => $salt);
						
						$_setting->set('SALT', $salt);
					}else $_var['msg'] = $GLOBALS['lang']['admin.login.validate.username.error'];
				}else{
					$manager = $_manager->get_by_name($_var['gp_txtUserName']);
					
					if($manager && $manager['ISMANAGER']){
						if(md5($_var['gp_txtPassword']) == $manager['PASSWD']){
							$random = random(64);
							
							$_var['current'] = $manager;
							$_var['current']['U_TYPE'] = 2; 
							$_var['current']['SALT'] = $manager['USERID'].','.$random; 
							
							$_manager->flash_state($manager['USERID'], $random);
						}else $_var['msg'] = $GLOBALS['lang']['admin.login.validate.username.error'];
					}else $_var['msg'] = $GLOBALS['lang']['admin.login.validate.username.error'];
				}
				
				if(empty($_var['msg'])){
					if($_var['current']){
						$_var['current']['xauth'] = str_encrypt($_var['current']['USERID'].'|'.$_var['current']['PASSWD'].'|'.$_var['current']['SALT']);
						$_SESSION['_current'] = serialize($_var['current']);
					}
					
					if($_var['gp_cbxRemember']){
						cookie_set('auth_member', str_encrypt($_var['current']['USERID'].'|'.$_var['current']['PASSWD'].'|'.$_var['current']['SALT']), time() + 86400 * 3000);
					}
					
					$_SESSION['_input_count'] = 0;
					
					header("location:{$ADMIN_SCRIPT}/admin/frame");
					exit(0);
				}else{
					$_SESSION['_input_count'] = $_SESSION['_input_count'] + 1;
				}
			}
		}
		
		if($_var['current']){
			if($_var['current']['USERID'] < 0){
				$salt = '-1,'.random(64);
				
				$_setting->set('SALT', $salt);
			}else{
				$random = random(64);
				$salt = $_var['current']['USERID'].','.$random; 
				
				$_manager->flash_state($_var['current']['USERID'], $random);
			}
			
			$_var['current']['SALT'] = $salt; 
			$_SESSION['_current'] = serialize($_var['current']);
			
			header("location:{$ADMIN_SCRIPT}/admin/frame");
			exit(0);
		}
		
		include_once view('/module/admin/view/login');
	}
	
	//注销
	public function logout(){
		$_manager = new _manager();
		$_manager->unset_state();
		
		$GLOBALS['lang']['show_message.thin'] = true;
		
		show_message($GLOBALS['lang']['admin.logout.message'], "{ADMIN_SCRIPT}");
	}
	
}
?>