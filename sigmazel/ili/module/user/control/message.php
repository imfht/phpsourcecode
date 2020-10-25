<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\control;

use admin\model\_log;
use user\model\_user;
use user\model\_message;
use user\model\_message_tpl;
use wx\model\_wx_setting;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/user/lang.php';

//消息
class message{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_message = new _message();
		$_user = new _user();
		
		$_wx_setting = new _wx_setting();
		$wx_setting = $_wx_setting->get();
		
		$search = $_message->search();
		
		if($_var['gp_do'] == 'send'){
			$message = $_message->get_by_id($_var['gp_id'] + 0);
			$user = $message ? $_user->get_by_id($message['READID']) : null;
			
			if($message && $user){
				if($message['TEMPLATE'] == 'WX'){
					$message['touser'] = $user['USERNAME'];
					$message['serial'] = 'OPENTM204650588';
					$message['first'] = $message['TITLE']."\r\n".$message['MESSAGE'];
					$message['keyword1'] = '消息通知';
					$message['keyword2'] = date('Y-m-d H:i:s');
				}elseif($message['TEMPLATE'] == 'EMAIL'){
					$message['EMAIL'] = $user['EMAIL'];
				}elseif($message['TEMPLATE'] == 'SMS'){
					$message['MOBILE'] = $user['MOBILE'];
				}
				
				$return = $_message->send($message);
				if($return){
					$_message->update($message['MESSAGEID'], array('RETURN' => $return));
					$_log->insert($GLOBALS['lang']['user.message.log.send']."({$message[TITLE]})", $GLOBALS['lang']['user.message']);
				}
			}
		}
		
		if($_var['gp_do'] == 'delete'){
			$message = $_message->get_by_id($_var['gp_id'] + 0);
			if($message){
				$_message->clear($message);
				
				$_log->insert($GLOBALS['lang']['user.message.log.delete']."({$message[TITLE]})", $GLOBALS['lang']['user.message']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$message_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$message = $_message->get_by_id($val);
				if($message){
					$_message->clear($message);
					
					$message_titles .= $message['TITLE'].'， ';
				}
				
				unset($message);
			}
			
			if($message_titles) $_log->insert($GLOBALS['lang']['user.message.log.delete.list']."({$message_titles})", $GLOBALS['lang']['user.message']);
		}
		
		$userids = array();
		$messages = array();
		$count = $_message->get_count("AND a.PARENTID = -1 {$search[wheresql]}");
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$message_list = $_message->get_list($start, $perpage, "AND a.PARENTID = -1 {$search[wheresql]}");
			foreach ($message_list as $key => $message){
				$message['_TEMPLATE'] = $message['TEMPLATE'];
				$message['TEMPLATE'] = $GLOBALS['lang']['user.message.view.td.type.local'];
				
				if($message['_TEMPLATE'] == 'WX') $message['TEMPLATE'] = $GLOBALS['lang']['user.message.view.td.type.wx'];
				elseif($message['_TEMPLATE'] == 'EMAIL') $message['TEMPLATE'] = $GLOBALS['lang']['user.message.view.td.type.email'];
				elseif($message['_TEMPLATE'] == 'SMS') $message['TEMPLATE'] = $GLOBALS['lang']['user.message.view.td.type.sms'];
				
				if($message['READID'] && !in_array($message['READID'], $userids)) $userids[] = $message['READID'];
				
				$messages[] = $message;
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/user/message{$search[querystring]}", $perpage);
		}
		
		$user_list = $_user->get_list(0, 0, "AND a.USERID IN(".eimplode($userids).")");
		foreach ($user_list as $key => $user){
			foreach($messages as $key => $message){
				if($user['USERID'] == $message['READID']){
					$message['READER'] = $user['REALNAME'];
					!$message['READER'] && $message['READER'] = $user['EMAIL'];
					!$message['READER'] && $message['READER'] = $user['MOBILE'];
					
					$messages[$key]['READER'] = $message['READER'];
					
					break;
				}
			}
		}
		
		include_once view('/module/user/view/message');
	}
	
	//添加
	public function _add(){
		global $_var, $setting;
		
		$_log = new _log();
		$_user = new _user();
		$_message = new _message();
		$_message_tpl = new _message_tpl();
		
		$_wx_setting = new _wx_setting();
		$wx_setting = $_wx_setting->get();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['user.message_edit.validate.title']."<br/>";
			if(empty($_var['gp_txtMessage'])) $_var['msg'] .= $GLOBALS['lang']['user.message_edit.validate.message']."<br/>";
			
			$user = null;
			
			if($_var['gp_txtUserName']){
				$user = $_user->get_by_id($_var['gp_txtUserName']);
				if($user == null) $user = $_user->get_by_email($_var['gp_txtUserName']);
				if($user == null) $user = $_user->get_by_mobile($_var['gp_txtUserName']);
				
				if(!$user) $_var['msg'] .= $GLOBALS['lang']['user.message_edit.validate.user']."<br/>";
			}
			
			//检查消息类型数据
			if(empty($_var['msg'])){
				if($_var['gp_rdoType'] == 1 && !is_email($user['EMAIL'])) $_var['msg'] .= $GLOBALS['lang']['user.message_edit.validate.user.email']."<br/>"; 
				elseif($_var['gp_rdoType'] == 2 && !is_mobile($user['MOBILE'])) $_var['msg'] .= $GLOBALS['lang']['user.message_edit.validate.user.mobile']."<br/>"; 
				elseif($_var['gp_rdoType'] == 3 && (!$user['USERNAME'] || !$user['WX_FANSID'] || $user['COMMENT'] != '微信登录')) $_var['msg'] .= $GLOBALS['lang']['user.message_edit.validate.user.openid']."<br/>"; 
			}
			
			if(empty($_var['msg'])){
				$template = 'MSG';
				$return = '200';
				
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtMessage'] = utf8substr($_var['gp_txtMessage'], 0, 200);
				
				if($_var['gp_rdoType'] == 3){
					//微信模板消息
					$message_tpl = $_message_tpl->get_by_serial('OPENTM204650588');
					
					if(!$message_tpl || !$wx_setting['WX_OPEN'] || !$wx_setting['WX_MSGTPL']) $template = 'MSG';
					else $template = 'WX';
				}elseif($_var['gp_rdoType'] == 2 && $setting['ThirdSms']){
					//短信
					$template = 'SMS';
				}elseif($_var['gp_rdoType'] == 1 && $setting['ThirdSMTP']){
					//邮件
					$template = 'EMAIL';
				}
				
				if($template == 'WX'){
					$message['touser'] = $user['USERNAME'];
					$message['serial'] = 'OPENTM204650588';
					$message['first'] = $_var['gp_txtTitle']."\r\n".$_var['gp_txtMessage'];
					$message['keyword1'] = '消息通知';
					$message['keyword2'] = date('Y-m-d H:i:s');
				}elseif($template == 'EMAIL'){
					$message['EMAIL'] = $user['EMAIL'];
					$message['TITLE'] = $_var['gp_txtTitle'];
					$message['MESSAGE'] = $_var['gp_txtMessage'];
				}elseif($template == 'SMS'){
					$message['MOBILE'] = $user['MOBILE'];
					$message['MESSAGE'] = $_var['gp_txtMessage'];
				}
				
				//模板类型
				$message['TEMPLATE'] = $template;
				//微信参数
				$message['WX_SETTING'] = $wx_setting;
				
				$return = $_message->send($message);
				
				/**
				 * @see 模板消息字段说明
				 * READID 收件人ID，空表示系统消息
				 * PARENTID 父级消息ID，后台发送为-1
				 */
				
				$_message->insert(array(
				'TITLE' => $_var['gp_txtTitle'],
				'MESSAGE' => $_var['gp_txtMessage'],
				'READID' => $user ? $user['USERID'] : 0, 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'PARENTID' => -1, 
				'STATUS' => 0, 
				'TEMPLATE' => $template, 
				'RETURN' => $return
				));
				
				$_log->insert($GLOBALS['lang']['user.message_edit.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['user.message']);
				
				show_message($GLOBALS['lang']['user.message_edit.message.add'], "{ADMIN_SCRIPT}/user/message");
			}
		}
		
		include_once view('/module/user/view/message_edit');
	}
	
	//模板
	public function _tpl(){
		global $_var;
		
		$_log = new _log();
		$_message_tpl = new _message_tpl();
		
		if(empty($_var['gp_do'])){
			if($_var['gp_cmd'] == 'delete' && $_var['gp_id'] > 0){
				$message_tpl = $_message_tpl->get_by_id($_var['gp_id']);
				if($message_tpl){
					$_message_tpl->delete($message_tpl['MESSAGE_TPLID']);
					
					$_log->insert($GLOBALS['lang']['user.message_tpl.log.delete']."({$message_tpl[TITLE]})", $GLOBALS['lang']['user.message_tpl']);
				}
			}elseif($_var['gp_cmd'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
				$message_tpl_titles = '';
				
				foreach ($_var['gp_cbxItem'] as $key => $val){
					$message_tpl = $_message_tpl->get_by_id($val);
					if(!$message_tpl) continue;
					
					$_message_tpl->delete($message_tpl['MESSAGE_TPLID']);
					
					$message_tpl_titles .= $message_tpl['TITLE'].',';
					
					unset($message_tpl);
				}
				
				if($message_tpl_titles) $_log->insert($GLOBALS['lang']['user.message_tpl.log.delete.list']."({$message_tpl_titles})", $GLOBALS['lang']['user.message_tpl']);
			}elseif($_var['gp_cmd'] == 'enable_list' && is_array($_var['gp_cbxItem'])){
				$message_tpl_titles = '';
				
				foreach ($_var['gp_cbxItem'] as $key => $val){
					$message_tpl = $_message_tpl->get_by_id($val);
					if(!$message_tpl) continue;
					
					$_message_tpl->update($message_tpl['MESSAGE_TPLID'], array('ENABLED' => 1));
					
					$message_tpl_titles .= $message_tpl['TITLE'].',';
					
					unset($message_tpl);
				}
				
				if($message_tpl_titles) $_log->insert($GLOBALS['lang']['user.message_tpl.log.enable.list']."({$message_tpl_titles})", $GLOBALS['lang']['user.message_tpl']);
			}elseif($_var['gp_cmd'] == 'disable_list' && is_array($_var['gp_cbxItem'])){
				$message_tpl_titles = '';
				
				foreach ($_var['gp_cbxItem'] as $key => $val){
					$message_tpl = $_message_tpl->get_by_id($val);
					if(!$message_tpl) continue;
					
					$_message_tpl->update($message_tpl['MESSAGE_TPLID'], array('ENABLED' => 0));
					
					$message_tpl_titles .= $message_tpl['TITLE'].',';
					
					unset($message_tpl);
				}
				
				if($message_tpl_titles) $_log->insert($GLOBALS['lang']['user.message_tpl.log.disable.list']."({$message_tpl_titles})", $GLOBALS['lang']['user.message_tpl']);
			}
			
			$message_tpls = $_message_tpl->get_all();
			
			include_once view('/module/user/view/message_tpl');
		}elseif($_var['gp_do'] == 'add'){
			$message_tpls = $_message_tpl->get_default();
			
			$message_tpl_added = array();
			$message_tpl_list = $_message_tpl->get_all();
			foreach ($message_tpl_list as $key => $item){
				$message_tpl_added[$item['SERIAL']] = 1;
			}
			
			if(count($message_tpls) == count($message_tpl_added)){
				show_message($GLOBALS['lang']['user.message_tpl_edit.message.added'], "{ADMIN_SCRIPT}/user/message/_tpl");
			}
			
			if($_var['gp_formsubmit']){
				$_var['msg'] = '';
				
				if($message_tpl_added[$_var['gp_rdoMsgTpl']]) $_var['msg'] .= $GLOBALS['lang']['user.message_tpl_edit.validate.exists']."<br/>";
				if(empty($_var['gp_rdoMsgTpl']) || !$message_tpls[$_var['gp_rdoMsgTpl']]) $_var['msg'] .= $GLOBALS['lang']['user.message_tpl_edit.validate.template']."<br/>";
				if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['user.message_tpl_edit.validate.identity']."<br/>";
				if(empty($_var['gp_txtRemark'])) $_var['msg'] .= $GLOBALS['lang']['user.message_tpl_edit.validate.remark']."<br/>";
				
				if(empty($_var['msg'])){
					$message_tpl = $message_tpls[$_var['gp_rdoMsgTpl']];
					
					$_message_tpl->insert(array(
					'SERIAL' => $message_tpl['SERIAL'],
					'TITLE' => $message_tpl['TITLE'],
					'CONTENT' => $message_tpl['CONTENT'], 
					'IDENTITY' => utf8substr($_var['gp_txtIdentity'], 0, 50), 
					'REMARK' => utf8substr(str_replace("\n", '\n', $_var['gp_txtRemark']), 0, 200), 
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['current']['USERNAME'],
					'EDITTIME' => date('Y-m-d H:i:s'),
					'ENABLED' => 1
					));
					
					$_log->insert($GLOBALS['lang']['user.message_tpl_edit.log.add'], $GLOBALS['lang']['user.message_tpl']);
					
					show_message($GLOBALS['lang']['user.message_tpl_edit.message.add'], "{ADMIN_SCRIPT}/user/message/_tpl");
				}
			}
			
			include_once view('/module/user/view/message_tpl_edit');
		}elseif($_var['gp_do'] == 'update'){
			$id = $_var['gp_id'] + 0;
			if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/user/message/_tpl");
			
			$message_tpl = $_message_tpl->get_by_id($id);
			if(!$message_tpl) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/user/message/_tpl");
			
			if($_var['gp_formsubmit']){
				$_var['msg'] = '';
				
				if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['user.message_tpl_edit.validate.identity']."<br/>";
				if(empty($_var['gp_txtRemark'])) $_var['msg'] .= $GLOBALS['lang']['user.message_tpl_edit.validate.remark']."<br/>";
				
				if(empty($_var['msg'])){
					$_message_tpl->update($message_tpl['MESSAGE_TPLID'], array(
					'IDENTITY' => utf8substr($_var['gp_txtIdentity'], 0, 50), 
					'REMARK' => utf8substr(str_replace("\n", '\n', $_var['gp_txtRemark']), 0, 200), 
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['current']['USERNAME'],
					'EDITTIME' => date('Y-m-d H:i:s')
					));
					
					$_log->insert($GLOBALS['lang']['user.message_tpl_edit.log.update'], $GLOBALS['lang']['user.message_tpl']);
					
					show_message($GLOBALS['lang']['user.message_tpl_edit.message.update'], "{ADMIN_SCRIPT}/user/message/_tpl");
				}
			}
			
			include_once view('/module/user/view/message_tpl_edit');
		}
	}
}
?>