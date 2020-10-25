<?php

/*
 * Copyright (C) xiuno.com
 */

class mmisc extends base_model {

	function __construct(&$conf) {
		parent::__construct($conf);
		
		// hook mmisc_construct_end.php
	}
		
	public function sendmail($username, $email, $subject, $message) {
		
		$mailconf = $this->kv->get('mail_conf');
		
		if($mailconf['sendtype'] == 0) {
			
			$subject = iconv('UTF-8', 'GBK', $subject);
			$message = iconv('UTF-8', 'GBK', $message);
			mail($email, $subject, $message, NULL, NULL);
			
		} elseif($mailconf['sendtype'] == 1) {
			
			$key = array_rand($mailconf['smtplist']);
			
			$smtp = $mailconf['smtplist'][$key];
			
			$message = str_replace("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />", "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=GBK\" />", $message);
			$subject = iconv('UTF-8', 'GBK', $subject);
			$message = iconv('UTF-8', 'GBK', $message);
			$username = iconv('UTF-8', 'GBK', $username);
			return xn_mail::send($smtp, $username, $email, $subject, $message);
			
		}
	}
	
	public function get_email_site($str) {	
		$email = array('url'=>'', 'name'=>'');		
		switch($str) {
			case '163.com':
				$email['url'] = 'http://mail.163.com/';
				$email['name'] = '163';
				break;
			case '126.com':
				$email['url'] = 'http://mail.163.com/';
				$email['name'] = '163';
				break;
			case 'yeah.net':
				$email['url'] = 'http://mail.163.com/';
				$email['name'] = '163';
				break;
			case 'qq.com':
				$email['url'] = 'http://mail.qq.com/';
				$email['name'] = 'QQ';
				break;
			case 'yahoo.cn':
				$email['url'] = 'http://mail.cn.yahoo.com/';
				$email['name'] = 'Yahoo';
				break;
			case 'yahoo.com.cn':
				$email['url'] = 'http://mail.cn.yahoo.com/';
				$email['name'] = 'Yahoo';
				break;
			case 'sina.com':
				$email['url'] = 'http://mail.sina.com.cn/';
				$email['name'] = 'sina';
				break;
			case 'sina.cn':
				$email['url'] = 'http://mail.sina.com.cn/';
				$email['name'] = 'sina';
				break;
			case 'hotmail.com':
				$email['url'] = 'http://www.hotmail.com/';
				$email['name'] = 'Hotmail';
				break;
			case 'live.cn':
				$email['url'] = 'http://www.hotmail.com/';
				$email['name'] = 'Hotmail';
				break;
			case 'live.com':
				$email['url'] = 'http://www.hotmail.com/';
				$email['name'] = 'Hotmail';
				break;
			case 'gmail.com':
				$email['url'] = 'https://accounts.google.com/ServiceLogin?service=mail';
				$email['name'] = 'Gmail';
				break;
			case 'sohu.com':
				$email['url'] = 'http://mail.sohu.com/';
				$email['name'] = 'sohu';
				break;
			case '21cn.com':
				$email['url'] = 'http://mail.21cn.com/';
				$email['name'] = '21cn';
				break;
			case 'eyou.com':
				$email['url'] = 'http://www.eyou.com/';
				$email['name'] = 'eyou';
				break;
			case '188.com':
				$email['url'] = 'http://www.188.com/';
				$email['name'] = '188';
				break;
			case '263.net':
				$email['url'] = 'http://www.263.net/';
				$email['name'] = '263';
				break;
			case '139.com':
				$email['url'] = 'http://mail.10086.cn/';
				$email['name'] = '139';
				break;
			case 'tom.com':
				$email['url'] = 'http://mail.tom.com/';
				$email['name'] = 'Tom';
				break;
			case 'sogou.com':
				$email['url'] = 'http://mail.sogou.com/';
				$email['name'] = 'sogou';
				break;
			case 'foxmail.com':
				$email['url'] = 'http://www.foxmail.com/';
				$email['name'] = 'foxmail';
				break;
			case 'wo.com.cn':
				$email['url'] = 'http://mail.wo.com.cn/';
				$email['name'] = 'mail.wo.com.cn';
				break;	
			default: 
				$email['url'] = "http://www.".$str;
				$email['name'] = $str;
				break;											
		}
		return $email;
	}
	
	// 检测是否含有关键词
	public function have_badword($s) {
		if(empty($this->conf['badword_on'])) {
			return '';
		}
		$badword = $this->kv->get('badword');
		if(!empty($badword)) {
			foreach($badword as $k=>$v) {
				if($k && strpos($s, $k) !== FALSE) {
					return $k;
				}
			}
		}
		return '';
	}
	
	// 检测并且过滤
	public function check_badword(&$s) {
		if(empty($this->conf['badword_on'])) {
			return '';
		}
		$badword = $this->kv->get('badword');
		if(!empty($badword)) {
			foreach($badword as $k=>$v) {
				if($v == '#' && $k && strpos($s, $k) !== FALSE) {
					return '包含有敏感词：'.$k.'';
				}
			}
			$keys = array_keys($badword);
			$values = array_values($badword);
			$s = str_replace($keys, $values, $s);
		}
		return '';
	}
}
?>