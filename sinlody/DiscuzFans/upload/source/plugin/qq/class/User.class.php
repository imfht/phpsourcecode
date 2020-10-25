<?php

/**
 * Wikin! [ Discuz!应用专家，维清互联旗下最新品牌 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: User.class.php 2015-5-13 21:52:56Z $
 */
class User {

	public static function register($username, $return = 0) {

		global $_G;
		if (!$username) {
			return;
		}

		$setting = $_G['cache']['plugin']['qq'];

		loaducenter();
		$groupid = $setting['newusergroupid'] ? $setting['newusergroupid'] : $_G['setting']['newusergroupid'];
		$password = md5(random(10));
		$email = 'qq_' . strtolower(random(10)) . '@null.null';

		$usernamelen = dstrlen($username);

		if ($usernamelen > 96) {
			if (!$return) {
				showmessage('profile_username_toolong');
			} else {
				return;
			}
		}
		$censorexp = '/^(' . str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($_G['setting']['censoruser'] = trim($_G['setting']['censoruser'])), '/')) . ')$/i';

		if ($_G['setting']['censoruser'] && @preg_match($censorexp, $username)) {
			if (!$return) {
				showmessage('profile_username_protect');
			} else {
				return;
			}
		}

		if (!$setting['disableregrule']) {
			loadcache('ipctrl');
			if ($_G['cache']['ipctrl']['ipregctrl']) {
				foreach (explode("\n", $_G['cache']['ipctrl']['ipregctrl']) as $ctrlip) {
					if (preg_match("/^(" . preg_quote(($ctrlip = trim($ctrlip)), '/') . ")/", $_G['clientip'])) {
						$ctrlip = $ctrlip . '%';
						$_G['setting']['regctrl'] = $_G['setting']['ipregctrltime'];
						break;
					} else {
						$ctrlip = $_G['clientip'];
					}
				}
			} else {
				$ctrlip = $_G['clientip'];
			}

			if ($_G['setting']['regctrl']) {
				if (C::t('common_regip')->count_by_ip_dateline($ctrlip, $_G['timestamp'] - $_G['setting']['regctrl'] * 3600)) {
					if (!$return) {
						showmessage('register_ctrl', NULL, array('regctrl' => $_G['setting']['regctrl']));
					} else {
						return;
					}
				}
			}

			$setregip = null;
			if ($_G['setting']['regfloodctrl']) {
				$regip = C::t('common_regip')->fetch_by_ip_dateline($_G['clientip'], $_G['timestamp'] - 86400);
				if ($regip) {
					if ($regip['count'] >= $_G['setting']['regfloodctrl']) {
						if (!$return) {
							showmessage('register_flood_ctrl', NULL, array('regfloodctrl' => $_G['setting']['regfloodctrl']));
						} else {
							return;
						}
					} else {
						$setregip = 1;
					}
				} else {
					$setregip = 2;
				}
			}

			if ($setregip !== null) {
				if ($setregip == 1) {
					C::t('common_regip')->update_count_by_ip($_G['clientip']);
				} else {
					C::t('common_regip')->insert(array('ip' => $_G['clientip'], 'count' => 1, 'dateline' => $_G['timestamp']));
				}
			}
		}

		$uid = uc_user_register(addslashes($username), $password, $email, '', '', $_G['clientip']);
		if ($uid <= 0) {
			if (!$return) {
				if ($uid == -1) {
					showmessage('profile_username_illegal');
				} elseif ($uid == -2) {
					showmessage('profile_username_protect');
				} elseif ($uid == -3) {
					showmessage('profile_username_duplicate');
				} elseif ($uid == -4) {
					showmessage('profile_email_illegal');
				} elseif ($uid == -5) {
					showmessage('profile_email_domain_illegal');
				} elseif ($uid == -6) {
					showmessage('profile_email_duplicate');
				} else {
					showmessage('undefined_action');
				}
			} else {
				return;
			}
		}

		$init_arr = array('credits' => explode(',', $_G['setting']['initcredits']));
		C::t('common_member')->insert($uid, $username, $password, $email, $_G['clientip'], $groupid, $init_arr);

		if ($_G['setting']['regctrl'] || $_G['setting']['regfloodctrl']) {
			C::t('common_regip')->delete_by_dateline($_G['timestamp'] - ($_G['setting']['regctrl'] > 72 ? $_G['setting']['regctrl'] : 72) * 3600);
			if ($_G['setting']['regctrl']) {
				C::t('common_regip')->insert(array('ip' => $_G['clientip'], 'count' => -1, 'dateline' => $_G['timestamp']));
			}
		}

		if ($_G['setting']['regverify'] == 2) {
			C::t('common_member_validate')->insert(array(
				'uid' => $uid,
				'submitdate' => $_G['timestamp'],
				'moddate' => 0,
				'admin' => '',
				'submittimes' => 1,
				'status' => 0,
				'message' => '',
				'remark' => '',
					), false, true);
			manage_addnotify('verifyuser');
		}

		require_once libfile('function/member');
		$cookietime = 1296000;
		setloginstatus(array(
			'uid' => $uid,
			'username' => $username,
			'password' => $password,
			'groupid' => $groupid,
				), $cookietime);

		dsetcookie('isqquser', 1, $cookietime);

		include_once libfile('function/stat');
		updatestat('register');

		include_once libfile('cache/userstats', 'function');
		build_cache_userstats();

		return $uid;
	}

	public static function login($member) {
		global $_G;

		if (!($member = getuserbyuid($member['uid'], 1))) {
			return false;
		} else {
			if (isset($member['_inarchive'])) {
				C::t('common_member_archive')->move_to_master($member['uid']);
			}
		}
		require_once libfile('function/member');
		$cookietime = 1296000;
		setloginstatus($member, $cookietime);

		dsetcookie('isqquser', 1, $cookietime);
		return true;
	}

	public static function uploadAvatar($uid, $localFile) {

		global $_G;
		if (!$uid || !$localFile) {
			return false;
		}

		include_once libfile('function_qqlogin', 'plugin/qq/function');
		$localFile = save_avatar($localFile);

		list($width, $height, $type, $attr) = getimagesize($localFile);
		if (!$width) {
			return false;
		}

		if ($width < 10 || $height < 10 || $type == 4) {
			return false;
		}

		$imageType = array(1 => '.gif', 2 => '.jpg', 3 => '.png');
		$fileType = $imgType[$type];
		if (!$fileType) {
			$fileType = '.jpg';
		}
		$avatarPath = $_G['setting']['attachdir'];
		$tmpAvatar = $avatarPath . './temp/upload' . $uid . $fileType;
		file_exists($tmpAvatar) && @unlink($tmpAvatar);
		file_put_contents($tmpAvatar, file_get_contents($localFile));

		if (!is_file($tmpAvatar)) {
			return false;
		}

		$tmpAvatarBig = './temp/upload' . $uid . 'big' . $fileType;
		$tmpAvatarMiddle = './temp/upload' . $uid . 'middle' . $fileType;
		$tmpAvatarSmall = './temp/upload' . $uid . 'small' . $fileType;

		$image = new image;
		if ($image->Thumb($tmpAvatar, $tmpAvatarBig, 200, 250, 1) <= 0) {
			return false;
		}
		if ($image->Thumb($tmpAvatar, $tmpAvatarMiddle, 120, 120, 1) <= 0) {
			return false;
		}
		if ($image->Thumb($tmpAvatar, $tmpAvatarSmall, 48, 48, 2) <= 0) {
			return false;
		}

		$tmpAvatarBig = $avatarPath . $tmpAvatarBig;
		$tmpAvatarMiddle = $avatarPath . $tmpAvatarMiddle;
		$tmpAvatarSmall = $avatarPath . $tmpAvatarSmall;

		$avatar1 = self::byte2hex(file_get_contents($tmpAvatarBig));
		$avatar2 = self::byte2hex(file_get_contents($tmpAvatarMiddle));
		$avatar3 = self::byte2hex(file_get_contents($tmpAvatarSmall));

		$extra = '&avatar1=' . $avatar1 . '&avatar2=' . $avatar2 . '&avatar3=' . $avatar3;
		$result = self::uc_api_post_ex('user', 'rectavatar', array('uid' => $uid), $extra);

		@unlink($tmpAvatar);
		@unlink($tmpAvatarBig);
		@unlink($tmpAvatarMiddle);
		@unlink($tmpAvatarSmall);
		@unlink($localFile);
		return true;
	}

	public static function byte2hex($string) {
		$buffer = '';
		$value = unpack('H*', $string);
		$value = str_split($value[1], 2);
		$b = '';
		foreach ($value as $k => $v) {
			$b .= strtoupper($v);
		}

		return $b;
	}

	public static function uc_api_post_ex($module, $action, $arg = array(), $extra = '') {
		$s = $sep = '';
		foreach ($arg as $k => $v) {
			$k = urlencode($k);
			if (is_array($v)) {
				$s2 = $sep2 = '';
				foreach ($v as $k2 => $v2) {
					$k2 = urlencode($k2);
					$s2 .= "$sep2{$k}[$k2]=" . urlencode(uc_stripslashes($v2));
					$sep2 = '&';
				}
				$s .= $sep . $s2;
			} else {
				$s .= "$sep$k=" . urlencode(uc_stripslashes($v));
			}
			$sep = '&';
		}
		$postdata = uc_api_requestdata($module, $action, $s, $extra);
		return uc_fopen2(UC_API . '/index.php', 500000, $postdata, '', TRUE, UC_IP, 20);
	}

}

?>