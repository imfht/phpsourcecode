<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'admin/control/admin_control.class.php';

class banip_control extends admin_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->check_admin_group();
	}
	
	// 考虑默认过期时间
	public function on_index() {
	
		$this->_checked['black'] = ' class="checked"';
		
		$input = $error = array();
		
		if($this->form_submit()) {
			$banips = (array)core::gpc('banip', 'P');
			$newbanip = (array)core::gpc('newbanip', 'P');
			$delete = (array)core::gpc('delete', 'P');
			foreach($delete as $v) {
				$v = intval($v);
				$this->banip->delete($v);
				unset($banips[$v]);
			}
			
			$this->conf['iptable_on'] = intval(core::gpc('iptable_on', 'P'));
			
			// newbanip
			foreach($newbanip['ip0'] as $k=>$v) {
                    		$ip0 = $this->ipintval($newbanip['ip0'][$k]);
                    		$ip1 = $this->ipintval($newbanip['ip1'][$k]);
                    		$ip2 = $this->ipintval($newbanip['ip2'][$k]);
                    		$ip3 = $this->ipintval($newbanip['ip3'][$k]);
                    		if($ip0 || $ip1 || $ip2 || $ip3) {
	                    		$expiry = strtotime($newbanip['expiry_fmt'][$k]);
					$ip = "$ip0.$ip1.$ip2.$ip3";
					$this->banip->add_banip($ip, $this->_user['uid'], $expiry);
                    		}
			}
			
			// update ip
			foreach($banips as $banid=>$banip) {
				$ip0 = $this->ipintval($banip['ip0']);
				$ip1 = $this->ipintval($banip['ip1']);
				$ip2 = $this->ipintval($banip['ip2']);
				$ip3 = $this->ipintval($banip['ip3']);
				$ip = "$ip0.$ip1.$ip2.$ip3";
				$this->banip->add_banip($ip, $this->_user['uid'], strtotime($banip['expiry_fmt']));
			}
			
			// iptable_on 保存在全局
			$this->kv->xset('iptable_on', $this->conf['iptable_on']);
			$this->runtime->xset('iptable_on', $this->conf['iptable_on']);
		}
		
		$currtime = date('Y-n-j', $_SERVER['time']);
		$nexttime = date('Y-n-j', $_SERVER['time'] + 86400 * 3);
		
		$input['iptable_on'] = form::get_radio_yes_no('iptable_on', $this->conf['iptable_on']);
		$baniplist = $this->banip->get_list();
		$this->view->assign('currtime', $currtime);
		$this->view->assign('nexttime', $nexttime);
		$this->view->assign('baniplist', $baniplist);
		$this->view->assign('input', $input);
		$this->view->assign('error', $error);
		
		// hook admin_banip_view_before.php
		
		$this->view->display('banip.htm');
	}
	
	private function ipintval($v) {
		if($v == '*') return -1;
		return intval($v);
	}
	
	//hook admin_banip_control_after.php
}

?>