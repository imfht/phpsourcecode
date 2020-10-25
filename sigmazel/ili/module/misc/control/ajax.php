<?php
//版权所有(C) 2014 www.ilinei.com

namespace misc\control;

use user\model\_sms;
use user\model\_user;
use cms\model\_article;
use ilinei\httpclient;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//AJAX
class ajax{
	//用户
	public function member(){
		global $_var, $db;
		
		$db->connect();
		
		$_user = new _user();
		
		$json = array('userid' => 0, 'success' => false, 'message' => '');
		
		if($_var['gp_op'] == 'check_name_exists'){
			$username = $_var['gp_name'];
			$tempuser = null;
			
			if(is_email($username)){
				$userofemail = $_user->get_by_email($username);
				$userofemail && $json['success'] = true;
			}elseif(is_mobile($username)){
				$userofname = $_user->get_by_mobile($username);
				$userofname && $json['success'] = true;
			}else{
				$userofemail = $_user->get_by_name($username);
				$userofemail && $json['success'] = true;
			}
			
			exit_json($json);
		}
		
		if($_var['gp_op'] == 'check_email_exists'){
			if(is_email($_var['gp_email'])){
				$userofemail = $_user->get_by_email($_var['gp_email']);
				$userofemail && $json['success'] = $_var['gp_id'] + 0 > 0 ? $userofemail['USERID'] != $_var['gp_id'] + 0 : true;
			}
			
			exit_json($json);
		}
		
		if($_var['gp_op'] == 'check_mobile_exists'){
			if($_var['gp_mobile']){
				$userofmobile = $_user->get_by_mobile($_var['gp_mobile']);
				$userofmobile && $json['success'] = $_var['gp_id'] + 0 > 0 ? $userofmobile['USERID'] != $_var['gp_id'] + 0 : true;
			}
			
			exit_json($json);
		}
	}
	
	//短信
	public function sms(){
		global $_var, $db, $setting;
		
		$db->connect();
		
		$_sms = new _sms();
		$_user = new _user();
		
		if(!strexists($_SERVER['HTTP_REFERER'], $setting['SiteHost'])) exit_json(array('success' => false, 'message' => $GLOBALS['lang']['error']));
		
		$nowtimer = time();
		if($_SESSION['_smslasttimer'] + 0 > $nowtimer - 120) exit_json(array('success' => false, 'message' => $GLOBALS['lang']['misc.sms.message.timeout']));
		
		if(empty($_var['gp_mobile'])) exit_json(array('success' => false, 'message' => $GLOBALS['lang']['misc.sms.message.mobile.empty']));
		
		$user = $_user->get_by_mobile($_var['gp_mobile']);
		if(!$user) exit_json(array('success' => false, 'message' => $GLOBALS['lang']['misc.sms.message.mobile.error']));
		
		$sms = $_sms->check($_var['gp_mobile'], date('Y-m-d'));
		if($sms && $sms['TIMES'] >= 3) exit_json(array('success' => false, 'message' => $GLOBALS['lang']['misc.sms.message.limit.num']));
		
		$checkcode = random(6, 1);
		$status = $_sms->send($_var['gp_mobile'], str_replace('{CHECKCODE}', $checkcode, $GLOBALS['lang']['misc.sms.message.checkcode']));
		
		if($sms){
			$_sms->update($sms['SMSID'], array(
			'MESSAGE' => "CHECKCODE:{$checkcode}", 
			'TIMES' => $sms['TIMES'] + 1, 
			'STATUS' => $status
			));
		}else{
			$_sms->insert(array(
			'MOBILE' => $_var['gp_mobile'], 
			'MESSAGE' => "CHECKCODE:{$checkcode}", 
			'TIMES' => 1, 
			'CREATETIME' => date('Y-m-d H:i:s'), 
			'STATUS' => $status
			));
		}
		
		$_SESSION['_smslasttimer'] = time();
		
		exit_json(array('success' => true, 'message' => $checkcode));
	}
	
	//天气
	public function weather(){
		global $_var;
		
		if(!$_var['gp_areaid']) exit_echo('');
	
		$pageContents = httpclient::quickGet("http://www.ilinei.com/weather.do?areaid={$_var[gp_areaid]}", array());
		exit_echo($pageContents);
	}
	
	//下载
	public function download(){
		global $_var, $db;
		
		$db->connect();
		
		$_article = new _article();
		
		$_var['gp_id'] = $_var['gp_id'] + 0 ;
		if($_var['gp_id'] == 0) exit_html5('很抱歉！发生错误了。');
		
		$article = $_article->get_by_id($_var['gp_id'] + 0);
		if(!$article || $article['TYPE'] != 2 || !is_array($article['CONTENT'])) exit_html5('很抱歉！发生错误了。');
		
		$_article->flash_down($article['ARTICLEID']);
		
		header("location:{$article[CONTENT][0]}");
		exit(0);
	}
	
}
?>