<?php

/*
	[UCenter] (C)2001-2099 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: sms.php 1059 2011-03-01 07:25:09Z pmonkey_w $
*/

!defined('IN_UC') && exit('Access Denied');

class control extends adminbase {

	var $apps = array();
	var $operations = array();

	function __construct() {
		$this->control();
	}

	function control() {
		parent::__construct();
		$this->check_priv();
		$this->load('sms');
		$this->check_priv();
	}

	function onls() {
		$page = getgpc('page');
		$delete = getgpc('delete', 'P');
		$status = 0;
		if(!empty($delete)) {
			$_ENV['sms']->delete_sms($delete);
			$status = 2;
			$this->writelog('sms_delete', "delete=".implode(',', $delete));
		}

		$num = $_ENV['sms']->get_total_num();
		$smslist = $_ENV['sms']->get_list($page, UC_PPP, $num);
		$multipage = $this->page($num, UC_PPP, $page, 'admin.php?m=sms&a=ls');

		$this->view->assign('status', $status);
		$this->view->assign('smslist', $smslist);
		$this->view->assign('multipage', $multipage);

		$this->view->display('admin_sms');
	}

	function onsend() {
		$smsid = intval(getgpc('smsid'));
		$appid = intval(getgpc('appid'));
		$noteid = intval(getgpc('noteid'));
		$result = $_ENV['sms']->send_by_id($smsid);
		if($result) {
			$this->writelog('sms_send', "appid=$appid&noteid=$noteid");
			$this->message('sms_succeed', $_SERVER['HTTP_REFERER']);
		} else {
			$this->writelog('sms_send', 'failed');
			$this->message('sms_false', $_SERVER['HTTP_REFERER']);
		}

	}

	function _note_status($status, $appid, $noteid, $args, $operation) {
		if($status > 0) {
			return '<font color="green">'.$this->lang['note_succeed'].'</font>';
		} elseif($status == 0) {
			$url = 'admin.php?m=note&a=send&appid='.$appid.'&noteid='.$noteid;
			return '<a href="'.$url.'" class="red">'.$this->lang['note_na'].'</a>';
		} elseif($status < 0) {
			$url = 'admin.php?m=note&a=send&appid='.$appid.'&noteid='.$noteid;
			return '<a href="'.$url.'"><font color="red">'.$this->lang['note_false'].(-$status).$this->lang['note_times'].'</font></a>';
		}
	}

	function _format_smslist(&$smslist) {
		if(is_array($smslist)) {
			foreach($smslist AS $key => $note) {
				$maillist[$key]['operation'] = $this->lang['note_'.$note['operation']];//$this->operations[$note['operation']][0];
				foreach($this->apps AS $appid => $app) {
					$maillist[$key]['status'][$appid] = $this->_note_status($note['app'.$appid], $appid, $note['noteid'], $note['args'], $note['operation']);
				}
			}
		}
	}

}

?>