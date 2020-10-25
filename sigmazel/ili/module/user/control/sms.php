<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\control;

use admin\model\_log;
use user\model\_sms;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/user/lang.php';

//短信
class sms{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_sms = new _sms();
		
		$search = $_sms->search();
		
		if($_var['gp_do'] == 'delete'){
			$sms = $_sms->get_by_id($_var['gp_id'] + 0);
			
			if($sms){
				$_sms->delete($sms['SMSID']);
				
				$_log->insert($GLOBALS['lang']['user.sms.list.log.delete']."({$sms[MOBILE]})", $GLOBALS['lang']['user.sms']);
			}
			
			unset($sms);
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$sms_mobiles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$sms = $_sms->get_by_id($val);
				if(!$sms) continue;
				
				$_sms->delete($sms['SMSID']);
				
				$sms_mobiles .= $sms['MOBILE'].'， ';
				
				unset($sms);
			}
			
			if($sms_mobiles) $_log->insert($GLOBALS['lang']['user.sms.list.log.delete.list']."({$sms_mobiles})", $GLOBALS['lang']['user.sms']);
		}
		
		if($_var['gp_do'] == 'send'){
			$sms = $_sms->get_by_id($_var['gp_id'] + 0);
			
			if($sms){
				if(substr($sms['MESSAGE'], 0, 10) == 'CHECKCODE:'){
					$sms['MESSAGE'] =  str_replace('{CHECKCODE}', substr($sms['MESSAGE'], 10), $GLOBALS['lang']['misc.sms.message.checkcode']);
				}
				
				$status = $_sms->send($sms['MOBILE'], $sms['MESSAGE']);
				if($status) $_sms->update($sms['SMSID'], array('STATUS' => $status));
				
				$_log->insert($GLOBALS['lang']['user.sms.list.log.send']."({$sms[MOBILE]})", $GLOBALS['lang']['user.sms']);
			}
		}
		
		$smses = array();
		$count = $_sms->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$sms_list = $_sms->get_list($start, $perpage, $search['wheresql']);
			foreach ($sms_list as $key => $sms) {
				if(substr($sms['MESSAGE'], 0, 10) == 'CHECKCODE:'){
					$sms['MESSAGE'] =  str_replace('{CHECKCODE}', substr($sms['MESSAGE'], 10), $GLOBALS['lang']['misc.sms.message.checkcode']);
				}
				
				$smses[] = $sms;
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/user/sms{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/user/view/sms');
	}
	
	//添加
	public function _add(){
		global $_var;
		
		$_log = new _log();
		$_sms = new _sms();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtMobile'])) $_var['msg'] .= $GLOBALS['lang']['user.sms_edit.validate.mobile']."<br/>";
			if(!is_mobile($_var['gp_txtMobile'])) $_var['msg'] .= $GLOBALS['lang']['user.sms_edit.validate.mobile.format']."<br/>";
			
			if(empty($_var['gp_txtMessage'])) $_var['msg'] .= $GLOBALS['lang']['user.sms_edit.validate.message']."<br/>";
			$_var['gp_txtMessage'] = utf8substr($_var['gp_txtMessage'], 0, 70);
			
			if(empty($_var['msg'])){
				$status = $_sms->send($_var['gp_txtMobile'], $_var['gp_txtMessage']);
				
				$_sms->insert(array(
				'MOBILE' => $_var['gp_txtMobile'], 
				'MESSAGE' => $_var['gp_txtMessage'], 
				'TIMES' => 1, 
				'CREATETIME' => date('Y-m-d H:i:s'), 
				'STATUS' => $status
				));
				
				$_log->insert($GLOBALS['lang']['user.sms_edit.log.send']."({$_var[gp_txtMobile]})", $GLOBALS['lang']['user.sms']);	
				
				show_message($GLOBALS['lang']['user.sms_edit.message.send'], "{ADMIN_SCRIPT}/user/sms");
			}
		}
		
		include_once view('/module/user/view/sms_edit');
	}
}
?>