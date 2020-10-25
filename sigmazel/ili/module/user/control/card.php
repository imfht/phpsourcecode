<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\control;

use admin\model\_log;
use user\model\_user;
use card\model\_card;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/user/lang.php';

//充值
class card{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_user = new _user();
		$_card = new _card();
		
		if($_var['gp_formsubmit']){
			$user = $_user->get_by_id($_var['gp_txtUserName']);
			if($user == null) $user = $_user->get_by_email($_var['gp_txtUserName']);
			if($user == null) $user = $_user->get_by_mobile($_var['gp_txtUserName']);
			
			if($user == null) show_message($GLOBALS['lang']['user.card.validate.username']);
			if(empty($_var['gp_txtCardPassword'])) show_message($GLOBALS['lang']['user.card.validate.password']); 
			
			$card = $_card->get_by_passwd(str_encrypt($_var['gp_txtCardPassword']));
			
			if(!$card) show_message($GLOBALS['lang']['user.card.validate.password.error']); 
			if($card['LIMITTYPE'] != 3) show_message($GLOBALS['lang']['user.card.validate.type']); 
			if($card['STATUS'] != 0) show_message($GLOBALS['lang']['user.card.validate.status']); 
			
			$nowtimer = strtotime(date('Y-m-d'));
			$begintimer = strtotime($card['BEGINDATE']);
			$entimer = strtotime($card['ENDDATE']);
			
			if($card['BEGINDATE'] + 0 > 0 && $nowtimer < $begintimer) show_message($GLOBALS['lang']['user.card.validate.begintime']); 
			if($card['ENDDATE'] + 0 > 0 && $entimer < $nowtimer) show_message($GLOBALS['lang']['user.card.validate.entime']); 
			
			$_card->using($user, $card);
			
			$_log->insert($GLOBALS['lang']['user.card.log.add']."；{$card[CREDIT]}，{$card[SERIAL]}。", $GLOBALS['lang']['user.card']);
			
			show_message($GLOBALS['lang']['user.card.message.add'], "{ADMIN_SCRIPT}/user/card");
		}
		
		include_once view('/module/user/view/card');
	}
	
}
?>