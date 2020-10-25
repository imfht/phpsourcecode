<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_log;
use admin\model\_manager;
use admin\model\_setting;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//个人账号
class account{
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_manager = new _manager();
		$_setting = new _setting();
		
		if($_var['current']['USERID'] < 0) $manager = $_var['current'];
		else $manager = $_manager->get_by_id($_var['current']['USERID']);
		
		if($_var['gp_formsubmit']){
			if($_var['current']['USERID'] < 0){
				//超级管理员，更新系统表中的密码
				
				if($_var['gp_txtPassword']){
					$_setting->set('AdminPassword', md5($_var['gp_txtPassword']));
				}
				
				$_log->insert($GLOBALS['lang']['admin.account.log.update'], $GLOBALS['lang']['admin.account']);

                cache_delete('setting');
				show_message($GLOBALS['lang']['admin.account.message.update'], "{ADMIN_SCRIPT}/admin/account");
			}else{
				//管理员，更新用户表中的密码
				$_manager->update($_var['current']['USERID'], array(
				'EMAIL' => utf8substr($_var['gp_txtEmail'], 0, 50), 
				'PASSWD' => $_var['gp_txtPassword'] ? md5($_var['gp_txtPassword']) : $manager['PASSWD']
				));
				
				$_log->insert($GLOBALS['lang']['admin.account.log.manager.update'], $GLOBALS['lang']['admin.account']);
				
				show_message($GLOBALS['lang']['admin.account.message.update'], "{ADMIN_SCRIPT}/admin/account");
			}
		}
		
		include_once view('/module/admin/view/account');
	}
	
}
?>