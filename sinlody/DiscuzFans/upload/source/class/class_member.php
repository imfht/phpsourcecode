<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class_member.php 34156 2013-10-25 01:10:00Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class logging_ctl {

	function logging_ctl() {
		require_once libfile('function/misc');
		loaducenter();
	}

	function logging_more($questionexist, $secchecklogin2 = 0) {
		global $_G;
		if(empty($_GET['lssubmit'])) {
			return;
		}
		$auth = authcode($_GET['username']."\t".$_GET['password']."\t".($questionexist ? 1 : 0), 'ENCODE', $_G['config']['security']['authkey']);
		$js = '<script type="text/javascript">showWindow(\'login\', \'member.php?mod=logging&action=login&auth='.rawurlencode($auth).'&referer='.rawurlencode(dreferer()).(!empty($_GET['cookietime']) ? '&cookietime=1' : '').'\')</script>';
		showmessage('location_login', '', array('type' => 1), array('extrajs' => $js));
	}

	function on_login() {
		global $_G;
		if($_G['uid']) {
			$referer = dreferer();
			$ucsynlogin = $this->setting['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';
			$param = array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['member']['uid']);
			showmessage('login_succeed', $referer ? $referer : './', $param, array('showdialog' => 1, 'locationtime' => true, 'extrajs' => $ucsynlogin));
		}

		list($seccodecheck) = seccheck('login');
		if(!empty($_GET['auth'])) {
			$dauth = authcode($_GET['auth'], 'DECODE', $_G['config']['security']['authkey']);
			list(,,,$secchecklogin2) = explode("\t", $dauth);
			if($secchecklogin2) {
				$seccodecheck = true;
			}
		}
		$seccodestatus = !empty($_GET['lssubmit']) ? false : $seccodecheck;
		$invite = getinvite();

		if(!submitcheck('loginsubmit', 1, $seccodestatus)) {

			$auth = '';
			$username = !empty($_G['cookie']['loginuser']) ? dhtmlspecialchars($_G['cookie']['loginuser']) : '';

			if(!empty($_GET['auth'])) {
				list($username, $password, $questionexist) = explode("\t", authcode($_GET['auth'], 'DECODE', $_G['config']['security']['authkey']));
				$username = dhtmlspecialchars($username);
				$auth = dhtmlspecialchars($_GET['auth']);
			}

			$cookietimecheck = !empty($_G['cookie']['cookietime']) || !empty($_GET['cookietime']) ? 'checked="checked"' : '';

			if($seccodecheck) {
				$seccode = random(6, 1) + $seccode{0} * 1000000;
			}

			if($this->extrafile && file_exists($this->extrafile)) {
				require_once $this->extrafile;
			}

			$navtitle = lang('core', 'title_login');
			include template($this->template);

		} else {

			if(!empty($_GET['auth'])) {
				list($_GET['username'], $_GET['password']) = daddslashes(explode("\t", authcode($_GET['auth'], 'DECODE', $_G['config']['security']['authkey'])));
			}

			$loginhash = !empty($_GET['loginhash']) && preg_match('/^\w+$/', $_GET['loginhash']) ? $_GET['loginhash'] : '';

			if(!($_G['member_loginperm'] = logincheck($_GET['username']))) {
				captcha::report($_G['clientip']);
				showmessage('login_strike');
			}
			if($_GET['fastloginfield']) {
				$_GET['loginfield'] = $_GET['fastloginfield'];
			}
			$_G['uid'] = $_G['member']['uid'] = 0;
			$_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';
			if(!$_GET['password'] || $_GET['password'] != addslashes($_GET['password'])) {
				showmessage('profile_passwd_illegal');
			}
			$result = userlogin($_GET['username'], $_GET['password'], $_GET['questionid'], $_GET['answer'], $this->setting['autoidselect'] ? 'auto' : $_GET['loginfield'], $_G['clientip']);
			$uid = $result['ucresult']['uid'];

			if(!empty($_GET['lssubmit']) && ($result['ucresult']['uid'] == -3 || $seccodecheck)) {
				$_GET['username'] = $result['ucresult']['username'];
				$this->logging_more($result['ucresult']['uid'] == -3);
			}

			if($result['status'] == -1) {
				if(!$this->setting['fastactivation']) {
					$auth = authcode($result['ucresult']['username']."\t".FORMHASH, 'ENCODE');
					showmessage('location_activation', 'member.php?mod='.$this->setting['regname'].'&action=activation&auth='.rawurlencode($auth).'&referer='.rawurlencode(dreferer()), array(), array('location' => true));
				} else {
					$init_arr = explode(',', $this->setting['initcredits']);
					$groupid = $this->setting['regverify'] ? 8 : $this->setting['newusergroupid'];

					C::t('common_member')->insert_new($uid, $result['ucresult']['username'], md5(random(10)), $result['ucresult']['email'], $result['ucresult']['sms'], $_G['clientip'], $groupid, $init_arr);
					$result['member'] = getuserbyuid($uid);
					$result['status'] = 1;
				}
			}

			if($result['status'] > 0) {

				if($this->extrafile && file_exists($this->extrafile)) {
					require_once $this->extrafile;
				}

				setloginstatus($result['member'], $_GET['cookietime'] ? 2592000 : 0);

				if($_G['setting']['ec_wxpay_appid'] && ($wxlogin_openid = getcookie('wxlogin_openid'))){
				    if(!C::t('common_member_openid')->fetch_first_by_uid_type($_G['uid'], 'wx')) {
				        C::t('common_member_openid')->insert(array('uid' => $_G['uid'], 'type'=>'wx', 'openid' => $wxlogin_openid, 'dateline' => TIMESTAMP), false, true);
				    }else{
				        C::t('common_member_openid')->update_by_uid_type($_G['uid'], 'wx', array('openid' => $wxlogin_openid, 'dateline' => TIMESTAMP));
				    }
				    dsetcookie('wxlogin_openid', '');
				}


				checkfollowfeed();
				if($_G['group']['forcelogin']) {
					if($_G['group']['forcelogin'] == 1) {
						clearcookies();
						showmessage('location_login_force_qq');
					} elseif($_G['group']['forcelogin'] == 2 && $_GET['loginfield'] != 'email') {
						clearcookies();
						showmessage('location_login_force_mail');
					} elseif($_G['group']['forcelogin'] == 3 && $_GET['loginfield'] != 'sms') {
						clearcookies();
						showmessage('location_login_force_sms');
					}
				}

				if($_G['member']['lastip'] && $_G['member']['lastvisit']) {
					dsetcookie('lip', $_G['member']['lastip'].','.$_G['member']['lastvisit']);
				}
				C::t('common_member_status')->update($_G['uid'], array('lastip' => $_G['clientip'], 'port' => $_G['remoteport'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));
				$ucsynlogin = $this->setting['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';

				$pwold = false;
				if($this->setting['strongpw'] && !$this->setting['pwdsafety']) {
					if(in_array(1, $this->setting['strongpw']) && !preg_match("/\d+/", $_GET['password'])) {
						$pwold = true;
					}
					if(in_array(2, $this->setting['strongpw']) && !preg_match("/[a-z]+/", $_GET['password'])) {
						$pwold = true;
					}
					if(in_array(3, $this->setting['strongpw']) && !preg_match("/[A-Z]+/", $_GET['password'])) {
						$pwold = true;
					}
					if(in_array(4, $this->setting['strongpw']) && !preg_match("/[^a-zA-z0-9]+/", $_GET['password'])) {
						$pwold = true;
					}
				}

				if($_G['member']['adminid'] != 1) {
					if($this->setting['accountguard']['loginoutofdate'] && $_G['member']['lastvisit'] && TIMESTAMP - $_G['member']['lastvisit'] > 90 * 86400) {
						C::t('common_member')->update($_G['uid'], array('freeze' => 2));
						C::t('common_member_validate')->insert(array(
							'uid' => $_G['uid'],
							'submitdate' => TIMESTAMP,
							'moddate' => 0,
							'admin' => '',
							'submittimes' => 1,
							'status' => 0,
							'message' => '',
							'remark' => '',
						), false, true);
						manage_addnotify('verifyuser');
						showmessage('location_login_outofdate', 'home.php?mod=spacecp&ac=profile&op=password&resend=1', array('type' => 1), array('showdialog' => true, 'striptags' => false, 'locationtime' => true));
					}

					if($this->setting['accountguard']['loginpwcheck'] && $pwold) {
						$freeze = $pwold;
						if($this->setting['accountguard']['loginpwcheck'] == 2 && $freeze) {
							C::t('common_member')->update($_G['uid'], array('freeze' => 1));
						}
					}
				}

				$seccheckrule = & $_G['setting']['seccodedata']['rule']['login'];
				if($seccheckrule['allow'] == 2) {
					if($seccheckrule['nolocal']) {
						require_once libfile('function/misc');
						$lastipConvert = process_ipnotice(convertip($_G['member']['lastip']));
						$nowipConvert = process_ipnotice(convertip($_G['clientip']));
						if($lastipConvert != $nowipConvert && stripos($lastipConvert, $nowipConvert) == false && stripos($nowipConvert, $lastipConvert) == false) {
							$seccodecheck = true;
						}
					}
					if(!$seccodecheck && $seccheckrule['pwsimple'] && $pwold) {
						$seccodecheck = true;
					}
					if(!$seccodecheck && $seccheckrule['outofday'] && $_G['member']['lastvisit'] && TIMESTAMP - $_G['member']['lastvisit'] > $seccheckrule['outofday'] * 86400) {
						$seccodecheck = true;
					}
					if(!$seccodecheck && $_G['member_loginperm'] < 4) {
						$seccodecheck = true;
					}
					if(!$seccodecheck && $seccheckrule['numiptry']) {
						$seccodecheck = failedipcheck($seccheckrule['numiptry'], $seccheckrule['timeiptry']);
					}
					if($seccodecheck && !$secchecklogin2) {
						clearcookies();
						$auth = authcode($_GET['username']."\t".$_GET['password']."\t".($_GET['questionid'] ? 1 : 0)."\t1", 'ENCODE', $_G['config']['security']['authkey']);
						$location = 'member.php?mod=logging&action=login&auth='.rawurlencode($auth).'&referer='.rawurlencode(dreferer()).(!empty($_GET['cookietime']) ? '&cookietime=1' : '');
						if(defined('IN_MOBILE')) {
							showmessage('login_seccheck2', $location);
						} else {
							$js = '<script type="text/javascript">location.href=\''.$location.'\'</script>';
							showmessage('login_seccheck2', '', array('type' => 1), array('extrajs' => $js));
						}
					}
				}

				if($invite['id']) {
					$result = C::t('common_invite')->count_by_uid_fuid($invite['uid'], $uid);
					if(!$result) {
						C::t('common_invite')->update($invite['id'], array('fuid'=>$uid, 'fusername'=>$_G['username']));
						updatestat('invite');
					} else {
						$invite = array();
					}
				}
				if($invite['uid']) {
					require_once libfile('function/friend');
					friend_make($invite['uid'], $invite['username'], false);
					dsetcookie('invite_auth', '');
					if($invite['appid']) {
						updatestat('appinvite');
					}
				}

				$param = array(
					'username' => $result['ucresult']['username'],
					'usergroup' => $_G['group']['grouptitle'],
					'uid' => $_G['member']['uid'],
					'groupid' => $_G['groupid'],
					'syn' => $ucsynlogin ? 1 : 0
				);

				$extra = array(
					'showdialog' => true,
					'locationtime' => true,
					'extrajs' => $ucsynlogin
				);

				if(!$freeze || !$this->setting['accountguard']['loginpwcheck']) {
					$loginmessage = $_G['groupid'] == 8 ? 'login_succeed_inactive_member' : 'login_succeed';
					$location = $invite || $_G['groupid'] == 8 ? 'home.php?mod=space&do=home' : dreferer();
				} else {
					$loginmessage = 'login_succeed_password_change';
					$location = 'home.php?mod=spacecp&ac=profile&op=password';
					$_GET['lssubmit'] = 0;
				}
				if(empty($_GET['handlekey']) || !empty($_GET['lssubmit'])) {
					if(defined('IN_MOBILE')) {
						showmessage($loginmessage, $location, $param, array('location' => true));
					} else {
						if(!empty($_GET['lssubmit'])) {
							if(!$ucsynlogin) {
								$extra['location'] = true;
							}
							showmessage($loginmessage, $location, $param, $extra);
						} else {
							$href = str_replace("'", "\'", $location);
							showmessage('location_login_succeed', $location, array(),
								array(
									'showid' => 'succeedmessage',
									'extrajs' => '<script type="text/javascript">'.
										'setTimeout("window.location.href =\''.$href.'\';", 3000);'.
										'$(\'succeedmessage_href\').href = \''.$href.'\';'.
										'$(\'main_message\').style.display = \'none\';'.
										'$(\'main_succeed\').style.display = \'\';'.
										'$(\'succeedlocation\').innerHTML = \''.lang('message', $loginmessage, $param).'\';</script>'.$ucsynlogin,
									'striptags' => false,
									'showdialog' => true
								)
							);
						}
					}
				} else {
					showmessage($loginmessage, $location, $param, $extra);
				}
			} else {
				$password = preg_replace("/^(.{".round(strlen($_GET['password']) / 4)."})(.+?)(.{".round(strlen($_GET['password']) / 6)."})$/s", "\\1***\\3", $_GET['password']);
				$errorlog = dhtmlspecialchars(
					TIMESTAMP."\t".
					($result['ucresult']['username'] ? $result['ucresult']['username'] : $_GET['username'])."\t".
					$password."\t".
					"Ques #".intval($_GET['questionid'])."\t".
					$_G['clientip']);
				writelog('illegallog', $errorlog);
				loginfailed($_GET['username']);
				failedip();
				$fmsg = $result['ucresult']['uid'] == '-3' ? (empty($_GET['questionid']) || $answer == '' ? 'login_question_empty' : 'login_question_invalid') : 'login_invalid';
				if($_G['member_loginperm'] > 1) {
					showmessage($fmsg, '', array('loginperm' => $_G['member_loginperm'] - 1));
				} elseif($_G['member_loginperm'] == -1) {
					showmessage('login_password_invalid');
				} else {
					showmessage('login_strike');
				}
			}

		}

	}

	function on_logout() {
		global $_G;

		$ucsynlogout = $this->setting['allowsynlogin'] ? uc_user_synlogout() : '';

		if($_GET['formhash'] != $_G['formhash']) {
			showmessage('logout_succeed', dreferer(), array('formhash' => FORMHASH, 'ucsynlogout' => $ucsynlogout, 'referer' => rawurlencode(dreferer())));
		}

		clearcookies();
		$_G['groupid'] = $_G['member']['groupid'] = 7;
		$_G['uid'] = $_G['member']['uid'] = 0;
		$_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';
		$_G['setting']['styleid'] = $this->setting['styleid'];

		if(defined('IN_MOBILE')) {
			showmessage('location_logout_succeed_mobile', dreferer(), array('formhash' => FORMHASH, 'referer' => rawurlencode(dreferer())));
		} else {
			showmessage('logout_succeed', dreferer(), array('formhash' => FORMHASH, 'ucsynlogout' => $ucsynlogout, 'referer' => rawurlencode(dreferer())));
		}
	}

}

class register_ctl {

	var $showregisterform = 1;

	function register_ctl() {
		global $_G;
		if($_G['setting']['bbclosed']) {
			if(($_GET['action'] != 'activation' && !$_GET['activationauth']) || !$_G['setting']['closedallowactivation'] ) {
				showmessage('register_disable', NULL, array(), array('login' => 1));
			}
		}

		loadcache(array('modreasons', 'stamptypeid', 'fields_required', 'fields_optional', 'fields_register', 'ipctrl'));
		require_once libfile('function/misc');
		require_once libfile('function/profile');
		if(!function_exists('sendmail')) {
			include libfile('function/mail');
		}
		include libfile('function/sms');
		loaducenter();
	}

	function on_register() {
		global $_G;

		$_GET['username'] = trim($_GET[''.$this->setting['reginput']['username']]);
		$_GET['password'] = $_GET[''.$this->setting['reginput']['password']];
		$_GET['password2'] = $_GET[''.$this->setting['reginput']['password2']];
		$_GET['email'] = $_GET[''.$this->setting['reginput']['email']];
		$_GET['sms'] = $_GET[''.$this->setting['reginput']['sms']];
		$_GET['smscode'] = $_GET[''.$this->setting['reginput']['smscode']];

		if($_G['uid']) {
			$ucsynlogin = $this->setting['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';
			$url_forward = dreferer();
			if(strpos($url_forward, $this->setting['regname']) !== false) {
				$url_forward = 'forum.php';
			}
			showmessage('login_succeed', $url_forward ? $url_forward : './', array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['uid']), array('extrajs' => $ucsynlogin));
		} elseif(!$this->setting['regclosed'] && (!$this->setting['regstatus'] || !$this->setting['ucactivation'])) {
			if($_GET['action'] == 'activation' || $_GET['activationauth']) {
				if(!$this->setting['ucactivation'] && !$this->setting['closedallowactivation']) {
					showmessage('register_disable_activation');
				}
			} elseif(!$this->setting['regstatus']) {
				if($this->setting['regconnect']) {
					dheader('location:connect.php?mod=login&op=init&referer=forum.php&statfrom=login_simple');
				}
				showmessage(!$this->setting['regclosemessage'] ? 'register_disable' : str_replace(array("\r", "\n"), '', $this->setting['regclosemessage']));
			}
		}

		$bbrules = & $this->setting['bbrules'];
		$bbrulesforce = & $this->setting['bbrulesforce'];
		$bbrulestxt = & $this->setting['bbrulestxt'];
		$welcomemsg = & $this->setting['welcomemsg'];
		$welcomemsgtitle = & $this->setting['welcomemsgtitle'];
		$welcomemsgtxt = & $this->setting['welcomemsgtxt'];
		$regname = $this->setting['regname'];

		if($this->setting['regverify']) {
			if($this->setting['areaverifywhite']) {
				$location = $whitearea = '';
				$location = trim(convertip($_G['clientip'], "./"));
				if($location) {
					$whitearea = preg_quote(trim($this->setting['areaverifywhite']), '/');
					$whitearea = str_replace(array("\\*"), array('.*'), $whitearea);
					$whitearea = '.*'.$whitearea.'.*';
					$whitearea = '/^('.str_replace(array("\r\n", ' '), array('.*|.*', ''), $whitearea).')$/i';
					if(@preg_match($whitearea, $location)) {
						$this->setting['regverify'] = 0;
					}
				}
			}

			if($_G['cache']['ipctrl']['ipverifywhite']) {
				foreach(explode("\n", $_G['cache']['ipctrl']['ipverifywhite']) as $ctrlip) {
					if(preg_match("/^(".preg_quote(($ctrlip = trim($ctrlip)), '/').")/", $_G['clientip'])) {
						$this->setting['regverify'] = 0;
						break;
					}
				}
			}
		}

		$invitestatus = false;
		if($this->setting['regstatus'] == 2) {
			if($this->setting['inviteconfig']['inviteareawhite']) {
				$location = $whitearea = '';
				$location = trim(convertip($_G['clientip'], "./"));
				if($location) {
					$whitearea = preg_quote(trim($this->setting['inviteconfig']['inviteareawhite']), '/');
					$whitearea = str_replace(array("\\*"), array('.*'), $whitearea);
					$whitearea = '.*'.$whitearea.'.*';
					$whitearea = '/^('.str_replace(array("\r\n", ' '), array('.*|.*', ''), $whitearea).')$/i';
					if(@preg_match($whitearea, $location)) {
						$invitestatus = true;
					}
				}
			}

			if($this->setting['inviteconfig']['inviteipwhite']) {
				foreach(explode("\n", $this->setting['inviteconfig']['inviteipwhite']) as $ctrlip) {
					if(preg_match("/^(".preg_quote(($ctrlip = trim($ctrlip)), '/').")/", $_G['clientip'])) {
						$invitestatus = true;
						break;
					}
				}
			}
		}

		$groupinfo = array();
		if($this->setting['regverify']) {
			$groupinfo['groupid'] = 8;
		} else {
			$groupinfo['groupid'] = $this->setting['newusergroupid'];
		}

		list($seccodecheck, $secqaacheck) = seccheck('register');
		$fromuid = !empty($_G['cookie']['promotion']) && $this->setting['creditspolicy']['promotion_register'] ? intval($_G['cookie']['promotion']) : 0;
		$username = isset($_GET['username']) ? $_GET['username'] : '';
		$bbrulehash = $bbrules ? substr(md5(FORMHASH), 0, 8) : '';
		$auth = $_GET['auth'];

		if(!$invitestatus) {
			$invite = getinvite();
		}
		$sendurl = $this->setting['sendregisterverify'] == 1 ? true : false;
		if($sendurl) {
			if(!empty($_GET['hash'])) {
				$_GET['hash'] = preg_replace("/[^\[A-Za-z0-9_\]%\s+-\/=]/", '', $_GET['hash']);
				$hash = explode("\t", authcode($_GET['hash'], 'DECODE', $_G['config']['security']['authkey']));
				if(is_array($hash) && isemail($hash[0]) && TIMESTAMP - $hash[1] < 259200) {
					$sendurl = false;
				}
			}
		}
		$sendsms = $this->setting['sendregisterverify'] == 2 ? true : false;
		if(!submitcheck('regsubmit', 0, $seccodecheck, $secqaacheck)) {

			if($_GET['action'] == 'activation') {
				$auth = explode("\t", authcode($auth, 'DECODE'));
				if(FORMHASH != $auth[1]) {
					showmessage('register_activation_invalid', 'member.php?mod=logging&action=login');
				}
				$username = $auth[0];
				$activationauth = authcode("$auth[0]\t".FORMHASH, 'ENCODE');
				$sendurl = false;
			}

			if(!$sendurl) {

				if($fromuid) {
					$member = getuserbyuid($fromuid);
					if(!empty($member)) {
						$fromuser = dhtmlspecialchars($member['username']);
					} else {
						dsetcookie('promotion');
					}
				}

				if($_GET['action'] == 'activation') {
					$auth = dhtmlspecialchars($auth);
				}

				if($seccodecheck) {
					$seccode = random(6, 1);
				}

				$username = dhtmlspecialchars($username);

				$htmls = $settings = array();
				foreach($_G['cache']['fields_register'] as $field) {
					$fieldid = $field['fieldid'];
					$html = profile_setting($fieldid, array(), false, false, true);
					if($html) {
						$settings[$fieldid] = $_G['cache']['profilesetting'][$fieldid];
						$htmls[$fieldid] = $html;
					}
				}

				$navtitle = $this->setting['reglinkname'];

				if($this->extrafile && file_exists($this->extrafile)) {
					require_once $this->extrafile;
				}
			}
			$bbrulestxt = nl2br("\n$bbrulestxt\n\n");
			$dreferer = dreferer();

			include template($this->template);

		} else {

			if(C::t('common_member_login')->getUid($username)) {
				showmessage('profile_username_duplicate');
			}

			$activationauth = array();
			if(isset($_GET['activationauth']) && $_GET['activationauth']) {
				$activationauth = explode("\t", authcode($_GET['activationauth'], 'DECODE'));
				if($activationauth[1] != FORMHASH) {
					showmessage('register_activation_invalid', 'member.php?mod=logging&action=login');
				}
				$sendurl = false;
			}
			if(!$activationauth && $sendurl) {
				checkemail($_GET['email']);
			}
			if($sendurl) {
				$hashstr = urlencode(authcode("$_GET[email]\t$_G[timestamp]", 'ENCODE', $_G['config']['security']['authkey']));
				$registerurl = "{$_G[siteurl]}member.php?mod=".$this->setting['regname']."&amp;hash={$hashstr}&amp;email={$_GET[email]}";
				$email_register_message = lang('email', 'email_register_message', array(
					'bbname' => $this->setting['bbname'],
					'siteurl' => $_G['siteurl'],
					'url' => $registerurl
				));
				if(!sendmail("$_GET[email] <$_GET[email]>", lang('email', 'email_register_subject'), $email_register_message)) {
					runlog('sendmail', "$_GET[email] sendmail failed.");
				}
				showmessage('register_email_send_succeed', dreferer(), array('bbname' => $this->setting['bbname']), array('showdialog' => false, 'msgtype' => 3, 'closetime' => 10));
			}
			$emailstatus = 0;
			if($this->setting['sendregisterverify'] == 1 && !$sendurl) {
				$_GET['email'] = strtolower($hash[0]);
				if($this->setting['regverify'] == 1) {
					$groupinfo['groupid'] = $this->setting['newusergroupid'];
				}
				$emailstatus = 1;
			}

			$smsstatus = 0;
			if($sendsms) {
			    checksms($_GET['sms']);

			    if(!check_smscode($_GET['smscode'],$_GET['sms'])){
			        showmessage('register_sms_check_fail');
			    }

				if($this->setting['regverify'] == 3) {
					$groupinfo['groupid'] = $this->setting['newusergroupid'];
				}
				$smsstatus = 1;
			}

			if($this->setting['regstatus'] == 2 && empty($invite) && !$invitestatus) {
				showmessage('not_open_registration_invite');
			}

			if($bbrules && $bbrulehash != $_POST['agreebbrule']) {
				showmessage('register_rules_agree');
			}

			$activation = array();
			if(isset($_GET['activationauth']) && $activationauth && is_array($activationauth)) {
				if($activationauth[1] == FORMHASH && !($activation = uc_get_user_new($activationauth[0]))) {
					showmessage('register_activation_invalid', 'member.php?mod=logging&action=login');
				}
			}

			if(!$activation) {
				$usernamelen = dstrlen($username);
				if($usernamelen < 3) {
					showmessage('profile_username_tooshort');
				} elseif($usernamelen > 15) {
					showmessage('profile_username_toolong');
				}
				if(uc_get_user(addslashes($username)) && !C::t('common_member')->fetch_uid_by_username($username) && !C::t('common_member_archive')->fetch_uid_by_username($username)) {
					if($_G['inajax']) {
						showmessage('profile_username_duplicate');
					} else {
						showmessage('register_activation_message', 'member.php?mod=logging&action=login', array('username' => $username));
					}
				}
				if($this->setting['pwlength']) {
					if(strlen($_GET['password']) < $this->setting['pwlength']) {
						showmessage('profile_password_tooshort', '', array('pwlength' => $this->setting['pwlength']));
					}
				}
				if($this->setting['strongpw']) {
					$strongpw_str = array();
					if(in_array(1, $this->setting['strongpw']) && !preg_match("/\d+/", $_GET['password'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_1');
					}
					if(in_array(2, $this->setting['strongpw']) && !preg_match("/[a-z]+/", $_GET['password'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_2');
					}
					if(in_array(3, $this->setting['strongpw']) && !preg_match("/[A-Z]+/", $_GET['password'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_3');
					}
					if(in_array(4, $this->setting['strongpw']) && !preg_match("/[^a-zA-z0-9]+/", $_GET['password'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_4');
					}
					if($strongpw_str) {
						showmessage(lang('member/template', 'password_weak').implode(',', $strongpw_str));
					}
				}
				$email = strtolower(trim($_GET['email']));
				$sms = trim($_GET['sms']);
				if(empty($this->setting['ignorepassword'])) {
					if($_GET['password'] !== $_GET['password2']) {
						showmessage('profile_passwd_notmatch');
					}

					if(!$_GET['password'] || $_GET['password'] != addslashes($_GET['password'])) {
						showmessage('profile_passwd_illegal');
					}
					$password = $_GET['password'];
				} else {
					$password = md5(random(10));
				}
			}

			$censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($this->setting['censoruser'] = trim($this->setting['censoruser'])), '/')).')$/i';

			if($this->setting['censoruser'] && @preg_match($censorexp, $username)) {
				showmessage('profile_username_protect');
			}

			if($this->setting['regverify'] == 2 && !trim($_GET['regmessage'])) {
				showmessage('profile_required_info_invalid');
			}

			if($_G['cache']['ipctrl']['ipregctrl']) {
				foreach(explode("\n", $_G['cache']['ipctrl']['ipregctrl']) as $ctrlip) {
					if(preg_match("/^(".preg_quote(($ctrlip = trim($ctrlip)), '/').")/", $_G['clientip'])) {
						$ctrlip = $ctrlip.'%';
						$this->setting['regctrl'] = $this->setting['ipregctrltime'];
						break;
					} else {
						$ctrlip = $_G['clientip'];
					}
				}
			} else {
				$ctrlip = $_G['clientip'];
			}

			if($this->setting['regctrl']) {
				if(C::t('common_regip')->count_by_ip_dateline($ctrlip, $_G['timestamp']-$this->setting['regctrl']*3600)) {
					showmessage('register_ctrl', NULL, array('regctrl' => $this->setting['regctrl']));
				}
			}

			$setregip = null;
			if($this->setting['regfloodctrl']) {
				$regip = C::t('common_regip')->fetch_by_ip_dateline($_G['clientip'], $_G['timestamp']-86400);
				if($regip) {
					if($regip['count'] >= $this->setting['regfloodctrl']) {
						showmessage('register_flood_ctrl', NULL, array('regfloodctrl' => $this->setting['regfloodctrl']));
					} else {
						$setregip = 1;
					}
				} else {
					$setregip = 2;
				}
			}

			$profile = $verifyarr = array();
			foreach($_G['cache']['fields_register'] as $field) {
				if(defined('IN_MOBILE')) {
					break;
				}
				$field_key = $field['fieldid'];
				$field_val = $_GET[''.$field_key];
				if($field['formtype'] == 'file' && !empty($_FILES[$field_key]) && $_FILES[$field_key]['error'] == 0) {
					$field_val = true;
				}

				if(!profile_check($field_key, $field_val)) {
					$showid = !in_array($field['fieldid'], array('birthyear', 'birthmonth')) ? $field['fieldid'] : 'birthday';
					showmessage($field['title'].lang('message', 'profile_illegal'), '', array(), array(
						'showid' => 'chk_'.$showid,
						'extrajs' => $field['title'].lang('message', 'profile_illegal').($field['formtype'] == 'text' ? '<script type="text/javascript">'.
							'$(\'registerform\').'.$field['fieldid'].'.className = \'px er\';'.
							'$(\'registerform\').'.$field['fieldid'].'.onblur = function () { if(this.value != \'\') {this.className = \'px\';$(\'chk_'.$showid.'\').innerHTML = \'\';}}'.
							'</script>' : '')
					));
				}
				if($field['needverify']) {
					$verifyarr[$field_key] = $field_val;
				} else {
					$profile[$field_key] = $field_val;
				}
			}

			if(!$activation) {
				$uid = uc_user_register_new(addslashes($username), $password, $email, $sms, $questionid, $answer, $_G['clientip']);
				if($uid <= 0) {
					if($uid == -1) {
						showmessage('profile_username_illegal');
					} elseif($uid == -2) {
						showmessage('profile_username_protect');
					} elseif($uid == -3) {
						showmessage('profile_username_duplicate');
					} elseif($uid == -4) {
						showmessage('profile_email_illegal');
					} elseif($uid == -5) {
						showmessage('profile_email_domain_illegal');
					} elseif($uid == -6) {
						showmessage('profile_email_duplicate');
					} elseif($uid == -7) {
						showmessage('profile_sms_illegal');
					} elseif($uid == -8) {
						showmessage('profile_sms_duplicate');
					} else {
						showmessage('undefined_action');
					}
				}
			} else {
				list($uid, $username, $email, $sms) = $activation;
			}
			$_G['username'] = $username;
			if(getuserbyuid($uid, 1)) {
				if(!$activation) {
					uc_user_delete($uid);
				}
				showmessage('profile_uid_duplicate', '', array('uid' => $uid));
			}

			$password = md5(random(10));
			$secques = $questionid > 0 ? random(8) : '';

			if(isset($_POST['birthmonth']) && isset($_POST['birthday'])) {
				$profile['constellation'] = get_constellation($_POST['birthmonth'], $_POST['birthday']);
			}
			if(isset($_POST['birthyear'])) {
				$profile['zodiac'] = get_zodiac($_POST['birthyear']);
			}

			if($_FILES) {
				$upload = new discuz_upload();

				foreach($_FILES as $key => $file) {
					$field_key = 'field_'.$key;
					if(!empty($_G['cache']['fields_register'][$field_key]) && $_G['cache']['fields_register'][$field_key]['formtype'] == 'file') {

						$upload->init($file, 'profile');
						$attach = $upload->attach;

						if(!$upload->error()) {
							$upload->save();

							if(!$upload->get_image_info($attach['target'])) {
								@unlink($attach['target']);
								continue;
							}

							$attach['attachment'] = dhtmlspecialchars(trim($attach['attachment']));
							if($_G['cache']['fields_register'][$field_key]['needverify']) {
								$verifyarr[$key] = $attach['attachment'];
							} else {
								$profile[$key] = $attach['attachment'];
							}
						}
					}
				}
			}

			if($setregip !== null) {
				if($setregip == 1) {
					C::t('common_regip')->update_count_by_ip($_G['clientip']);
				} else {
					C::t('common_regip')->insert(array('ip' => $_G['clientip'], 'count' => 1, 'dateline' => $_G['timestamp']));
				}
			}

			if($invite && $this->setting['inviteconfig']['invitegroupid']) {
				$groupinfo['groupid'] = $this->setting['inviteconfig']['invitegroupid'];
			}

			$init_arr = array('credits' => explode(',', $this->setting['initcredits']), 'profile'=>$profile, 'emailstatus' => $emailstatus, 'smsstatus' => $smsstatus);

			C::t('common_member')->insert_new($uid, $username, $password, $email, $sms, $_G['clientip'], $groupinfo['groupid'], $init_arr);
			if($emailstatus) {
				updatecreditbyaction('realemail', $uid);
			}
			if($smsstatus) {
			    updatecreditbyaction('realsms', $uid);
			}
			if($verifyarr) {
				$setverify = array(
					'uid' => $uid,
					'username' => $username,
					'verifytype' => '0',
					'field' => serialize($verifyarr),
					'dateline' => TIMESTAMP,
				);
				C::t('common_member_verify_info')->insert($setverify);
				C::t('common_member_verify')->insert(array('uid' => $uid));
			}

			require_once libfile('cache/userstats', 'function');
			build_cache_userstats();

			if($this->extrafile && file_exists($this->extrafile)) {
				require_once $this->extrafile;
			}

			if($this->setting['regctrl'] || $this->setting['regfloodctrl']) {
				C::t('common_regip')->delete_by_dateline($_G['timestamp']-($this->setting['regctrl'] > 72 ? $this->setting['regctrl'] : 72)*3600);
				if($this->setting['regctrl']) {
					C::t('common_regip')->insert(array('ip' => $_G['clientip'], 'count' => -1, 'dateline' => $_G['timestamp']));
				}
			}

			$regmessage = dhtmlspecialchars($_GET['regmessage']);
			if($this->setting['regverify'] == 2) {
				C::t('common_member_validate')->insert(array(
					'uid' => $uid,
					'submitdate' => $_G['timestamp'],
					'moddate' => 0,
					'admin' => '',
					'submittimes' => 1,
					'status' => 0,
					'message' => $regmessage,
					'remark' => '',
				), false, true);
				manage_addnotify('verifyuser');
			}

			setloginstatus(array(
				'uid' => $uid,
				'username' => $_G['username'],
				'password' => $password,
				'groupid' => $groupinfo['groupid'],
			), 0);
			include_once libfile('function/stat');
			updatestat('register');

			if($invite['id']) {
				$result = C::t('common_invite')->count_by_uid_fuid($invite['uid'], $uid);
				if(!$result) {
					C::t('common_invite')->update($invite['id'], array('fuid'=>$uid, 'fusername'=>$_G['username'], 'regdateline' => $_G['timestamp'], 'status' => 2));
					updatestat('invite');
				} else {
					$invite = array();
				}
			}
			if($invite['uid']) {
				if($this->setting['inviteconfig']['inviteaddcredit']) {
					updatemembercount($uid, array($this->setting['inviteconfig']['inviterewardcredit'] => $this->setting['inviteconfig']['inviteaddcredit']));
				}
				if($this->setting['inviteconfig']['invitedaddcredit']) {
					updatemembercount($invite['uid'], array($this->setting['inviteconfig']['inviterewardcredit'] => $this->setting['inviteconfig']['invitedaddcredit']));
				}
				require_once libfile('function/friend');
				friend_make($invite['uid'], $invite['username'], false);
				notification_add($invite['uid'], 'friend', 'invite_friend', array('actor' => '<a href="home.php?mod=space&uid='.$invite['uid'].'" target="_blank">'.$invite['username'].'</a>'), 1);

				space_merge($invite, 'field_home');
				if(!empty($invite['privacy']['feed']['invite'])) {
					require_once libfile('function/feed');
					$tite_data = array('username' => '<a href="home.php?mod=space&uid='.$_G['uid'].'">'.$_G['username'].'</a>');
					feed_add('friend', 'feed_invite', $tite_data, '', array(), '', array(), array(), '', '', '', 0, 0, '', $invite['uid'], $invite['username']);
				}
				if($invite['appid']) {
					updatestat('appinvite');
				}
			}
			$welcomemsg = $welcomemsg ? dunserialize($welcomemsg) : '';
			if($welcomemsg && !empty($welcomemsgtxt)) {
				$welcomemsgtitle = replacesitevar($welcomemsgtitle);
				$welcomemsgtxt = replacesitevar($welcomemsgtxt);
				if(in_array('1', $welcomemsg)) {
					notification_add($uid, 'system', nl2br(str_replace(':', '&#58;', $welcomemsgtxt)), array('from_id' => 0, 'from_idtype' => 'welcomemsg'), 1);
				}
				if($email && in_array('2', $welcomemsg)) {
					sendmail_cron($email, $welcomemsgtitle, $welcomemsgtxt);
				}
				if($sms && in_array('3', $welcomemsg)) {
				    $smsconfig = dunserialize($_G['setting']['sms']);
				    $smstemp = $smsconfig['template']['add_member'];
				    $smsvar = array(
				        'username' => $_G['username'],
				        'bbname' => $_G['setting']['bbname'],
				        'sitename' => $_G['setting']['sitename'],
				        'siteurl' => $_G['setting']['siteurl'],
				        'password' => $password,
				    );
					sendsms_cron($sms, $smsvar, $smstemp);
				}
			}

			if($fromuid) {
				updatecreditbyaction('promotion_register', $fromuid);
				dsetcookie('promotion', '');
			}
			dsetcookie('loginuser', '');
			dsetcookie('activationauth', '');
			dsetcookie('invite_auth', '');

			$url_forward = dreferer();
			$refreshtime = 3000;
			$message = 'register_succeed';
			$locationmessage = 'register_succeed_location';
			switch($this->setting['regverify']) {
				case 1:
				    if($emailstatus) break;
					$idstring = random(6);
					$authstr = $this->setting['regverify'] == 1 ? "$_G[timestamp]\t2\t$idstring" : '';
					C::t('common_member_field_forum')->update($_G['uid'], array('authstr' => $authstr));
					$verifyurl = "{$_G[siteurl]}member.php?mod=activate&amp;uid={$_G[uid]}&amp;id=$idstring";
					$email_verify_message = lang('email', 'email_verify_message', array(
						'username' => $_G['member']['username'],
						'bbname' => $this->setting['bbname'],
						'siteurl' => $_G['siteurl'],
						'url' => $verifyurl
					));
					if(!sendmail("$username <$email>", lang('email', 'email_verify_subject'), $email_verify_message)) {
						runlog('sendmail', "$email sendmail failed.");
					}
					$message = 'register_email_verify';
					$locationmessage = 'register_email_verify_location';
					$refreshtime = 10000;
					break;
				case 2:
					$message = 'register_manual_verify';
					$locationmessage = 'register_manual_verify_location';
					break;
				case 3:
				    if($smsstatus) break;
				    $message = 'register_sms_verify';
				    $locationmessage = 'register_sms_verify_location';
				    break;
			}
			$param = array('bbname' => $this->setting['bbname'], 'username' => $_G['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['uid']);
			if(strpos($url_forward, $this->setting['regname']) !== false || strpos($url_forward, 'buyinvitecode') !== false) {
				$url_forward = 'forum.php';
			}
			$href = str_replace("'", "\'", $url_forward);
			$extra = array(
				'showid' => 'succeedmessage',
				'extrajs' => '<script type="text/javascript">'.
					'setTimeout("window.location.href =\''.$href.'\';", '.$refreshtime.');'.
					'$(\'succeedmessage_href\').href = \''.$href.'\';'.
					'$(\'main_message\').style.display = \'none\';'.
					'$(\'main_succeed\').style.display = \'\';'.
					'$(\'succeedlocation\').innerHTML = \''.lang('message', $locationmessage).'\';'.
				'</script>',
				'striptags' => false,
			);
			showmessage($message, $url_forward, $param, $extra);
		}
	}

}

class crime_action_ctl {

	static $actions = array('all', 'crime_delpost', 'crime_warnpost', 'crime_banpost', 'crime_banspeak', 'crime_banvisit', 'crime_banstatus', 'crime_avatar', 'crime_sightml', 'crime_customstatus');

	function crime_action_ctl() {}

	function &instance() {
		static $object;
		if(empty($object)) {
			$object = new crime_action_ctl();
		}
		return $object;
	}

	function recordaction($uid, $action, $reason) {
		global $_G;

		$uid = intval($uid);
		$key = array_search($action, self::$actions);
		if($key === FALSE) {
			return false;
		}
		$insert = array(
			'uid' => $uid,
			'operatorid' => $_G['uid'],
			'operator' => $_G['username'],
			'action' => $key,
			'reason' => $reason,
			'dateline' => $_G['timestamp']
		);
		C::t('common_member_crime')->insert($insert);
		return true;
	}

	function getactionlist($uid) {
		$uid = intval($uid);
		$clist = array();
		foreach(C::t('common_member_crime')->fetch_all_by_uid($uid) as $c) {
			$c['action'] = self::$actions[$c['action']];
			$clist[] = $c;
		}
		return $clist;
	}

	function getcount($uid, $action) {
		$uid = intval($uid);
		$key = array_search($action, self::$actions);
		if($key === FALSE) {
			return 0;
		}
		return C::t('common_member_crime')->count_by_uid_action($uid, $key);
	}

	function search($action, $username, $operator, $startime, $endtime, $reason, $start, $limit) {
		$action = intval($action);
		$operator = daddslashes(trim($operator));
		$starttime = $starttime ? strtotime($starttime) : 0;
		$endtime = $endtime ? (strtotime($endtime) + 3600 * 24) : 0;
		$reason = daddslashes(trim($reason));
		$start = intval($start);
		$limit = intval($limit);

		if(!empty($username)) {
			$uid = C::t('common_member')->fetch_uid_by_username($username);
			$wheresql[] = "uid='$uid'";
		}

		if($action) {
			$wheresql[] = "action='$action'";
		}
		if($operator) {
			$wheresql[] = "operator='$operator'";
		}
		if($starttime) {
			$wheresql[] = "dateline>='$starttime'";
		}
		if($endtime) {
			$wheresql[] = "dateline<='$endtime'";
		}
		if($reason) {
			$wheresql[] = "reason LIKE '%$reason%'";
		}

		if($wheresql) {
			$wheresql = 'WHERE '.implode(' AND ', $wheresql);
		} else {
			$wheresql = '';
		}
		$clist = array();
		$count = C::t('common_member_crime')->count_by_where($wheresql);

		if($count) {
			$uids = array();
			foreach(C::t('common_member_crime')->fetch_all_by_where($wheresql, $start, $limit) as $crime) {
				$crime['action'] = self::$actions[$crime['action']];
				$clist[] = $crime;
				$uids[$crime['uid']] = $crime['uid'];
			}
			$members = C::t('common_member')->fetch_all($uids, false, 0);
			foreach($clist as $key => $crime) {
				$crime['username'] = $members[$crime['uid']]['username'];
				$clist[$key] = $crime;
			}
		}
		return array($count, $clist);
	}
}
?>