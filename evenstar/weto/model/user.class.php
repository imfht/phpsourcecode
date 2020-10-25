<?php

/*
 * Copyright (C) xiuno.com
 */

class user extends base_model{
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'user';
		$this->primarykey = array('uid');
		$this->maxcol = 'uid';
		
		// hook user_construct_end.php
	}
	
	// 关联创建用户
	public function xcreate($arr) {
		
		if(!isset($this->conf['reg_init_golds'])) {
			$this->conf += $this->kv->xget('conf_ext');
		}
		
		empty($arr['uid']) && $arr['uid'] = $this->maxid('+1');
		$arr['regdate'] = $_SERVER['time'];
		$arr['regip'] = ip2long($_SERVER['ip']);
		$arr['threads'] = 0;
		$arr['posts'] = 0;
		$arr['myposts'] = 0;
		$arr['avatar'] = 0;
		$arr['credits'] = 0;
		$arr['golds'] = $this->conf['reg_init_golds'];
		$arr['digests'] = 0;
		$arr['follows'] = 0;
		$arr['followeds'] = 0;
		$arr['newpms'] = 0;
		$arr['newfeeds'] = 0;
		$arr['homepage'] = '';
		$arr['accesson'] = 0;
		$arr['onlinetime'] = 0;
		$arr['lastactive'] = $_SERVER['time'];
		
		// hook user_model_xcreate_end.php
		
		return $this->create($arr);
	}
	
	public function get_list($cond = array(), $start = 0, $limit = 20, $total = 0) {
		
		// 优化大数据翻页，倒排
		if($cond) {
			$arrlist = $this->index_fetch($cond, array('uid'=>-1), $start, $limit);
			return $arrlist;
		} else {
			if($start > 1000 && $total > 2000 && $start > $total / 2) {
				$start = $total - $start;
				$arrlist = $this->index_fetch(array(), array('uid'=>1), max(0, $start - $limit), $limit);
				$arrlist = array_reverse($arrlist, TRUE);
				return $arrlist;
			} else {
				$arrlist = $this->index_fetch(array(), array('uid'=>-1), $start, $limit);
				return $arrlist;
			}
		}
	}
	
	public function update_password($uid, $newpw) {
		$user = $this->read($uid);
		$user['password'] = $this->md5_md5($newpw, $user['salt']);
		$this->update($user);
	}
	
	// 更新用户用户组，只针对普通用户组。
	public function update_group($user, $cookie_groupid = 0) {
		// 普通用户组范围 11 - 100
		if($user['groupid'] < 11 || $user['groupid'] > 1000) {
			return FALSE;
		}
		$groupid = $this->group->get_groupid_by_credits($user['groupid'], $user['credits']);
		if($groupid != $user['groupid'] || ($cookie_groupid && $cookie_groupid != $user['groupid'])) {
			$this->set_login_cookie($user);
			return TRUE;
		}
		return FALSE;
	}

	// 根据 username 获取 uid
	public function get_uid_by_email($email) {
		$user = $this->get_user_by_email($email);
		if(!empty($user)) {
			return $user['uid'];
		} else {
			return FALSE;
		}
	}

	// 结果为唯一
	public function get_user_by_email($email) {
		$userlist = $this->index_fetch(array('email'=>$email), array(), 0, 1);
		return $userlist ? array_pop($userlist) : array();
	}
	
	public function get_user_by_username($username) {
		// 根据非主键取数据
		$userlist = $this->index_fetch( array('username'=>$username), array(), 0, 1);
		return $userlist ? array_pop($userlist) : array();
	}
	
	public function get_xn_auth($user) {
		if(empty($user)) {
			return '';
		}
		$xn_auth = $this->encrypt_auth($user['uid'], $user['username'], $user['password'], $user['groupid'], $user['accesson']);
		return $xn_auth;
	}
	
	public function set_login_cookie($user, $time = 0) {
		// 包含登录信息，重要。HTTP_ONLY
		empty($time) && $time = $_SERVER['time'] + 864000; // 默认设置为10天
		$xn_auth = $this->get_xn_auth($user);
		misc::setcookie($this->conf['cookie_pre'].'auth', $xn_auth, $time, $this->conf['cookie_path'], $this->conf['cookie_domain'], TRUE);
	}
	
	// ----------------------> 其他杂项
	public function check_email(&$email) {
		$emaildefault = array('admin', 'system');
		if(empty($email)) {
			return 'EMAIL 不能为空';
		//} elseif(utf8::strlen($email) > 32) {
		//	return 'Email 长度不能大于 32 个字符。';
		} elseif(!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)) {
			return 'Email 格式不对';
		} elseif(utf8::strlen($email) < 6) {
			return 'Email 太短';
		//} elseif(str_replace($emaildefault, '', $email) != $email) {
		//	return 'Email 含有非法关键词';
		}
		
		// hook usre_model_check_email_end.php
		return '';
	}
	
	public function check_email_exists($email) {
		if($this->get_uid_by_email($email)) {
			return 'Email 已经被注册';
		}
		return '';
	}
	
	public function check_username_exists($username) {
		if($this->get_user_by_username($username)) {
			return '用户名已经存在';
		}
		return '';
	}
	
	public function check_username(&$username) {
		$username = trim($username);
		if(empty($username)) {
			return '用户名不能为空。';
		} elseif(utf8::strlen($username) > 16) {
			return '用户名太长:'.utf8::strlen($username);
		} elseif(str_replace(array("\t", "\r", "\n", ' ', '　', ',', '，', '-'), '', $username) != $username) {
			return '用户名中不能含有空格和 , - 等字符';
		//} elseif(!preg_match('#^[\w\'\-\x7f-\xff]+$#', $username)) {
		//	return '用户名必须为中文';
		} elseif(htmlspecialchars($username) != $username) {
			return '用户名中不能含有HTML字符（尖括号）';
		}
		if(($error = $this->mmisc->have_badword($username))) {
			return '包含敏感词：'.$error;
		}
		
		// hook usre_model_check_username_end.php
		return '';
	}
	
	public function safe_username(&$username) {
		$username = str_replace(array("\t", "\r", "\n", ' ', '　', ',', '，', '-'), '', $username);
		$username = htmlspecialchars($username);
	}
	
	public function check_password(&$password) {
		if(empty($password)) {
			return '密码不能为空';
		} elseif(utf8::strlen($password) > 32) {
			return '密码不能超过 32 个字符';
		}
		return '';
	}
	
	public function check_password2(&$password, &$password2) {
		if(empty($password2)) {
			return '重复输入密码不能为空';
		} elseif($password != $password2) {
			return '两次输入的密码不符合';
		}
		return '';
	}
	
	public function check_homepage(&$homepage) {
		if(!empty($homepage)) {
			core::htmlspecialchars($homepage);
			if(utf8::strlen($homepage) > 40) {
				return '网址不能超过 40 个字符';
			} elseif(!check::is_url($homepage)) {
				return '网址格式不正确！';
			}
		}
		return '';
	}
	
	// 用来显示给用户
	public function format(&$user) {
		if(!$user) return;
		$user['regdate_fmt'] = date('Y-n-j', $user['regdate']);
		$user['regip'] = long2ip($user['regip']);
		$dir = image::get_dir($user['uid']);
		if($user['avatar']) {
			$user['avatar_small'] = $this->conf['upload_url'].'avatar/'.$dir.'/'.$user['uid'].'_small.gif?'.$user['avatar'];
			$user['avatar_middle'] = $this->conf['upload_url'].'avatar/'.$dir.'/'.$user['uid'].'_middle.gif?'.$user['avatar'];
			$user['avatar_big'] = $this->conf['upload_url'].'avatar/'.$dir.'/'.$user['uid'].'_big.gif?'.$user['avatar'];
			$user['avatar_huge'] = $this->conf['upload_url'].'avatar/'.$dir.'/'.$user['uid'].'_huge.gif?'.$user['avatar'];
		} else {
			$user['avatar_small'] = '';
			$user['avatar_middle'] = '';
			$user['avatar_big'] = '';
			$user['avatar_huge'] = '';
		}
		
		$user['lastactive_fmt'] = misc::humandate($user['lastactive']);
		$user['isonline'] = $_SERVER['time'] - $user['lastactive'] < $this->conf['online_hold_time'] ? 1 : 0;
		
		$user['groupname'] = $this->conf['grouparr'][$user['groupid']];
		
		// hook user_model_format_end.php
	}
	
	// followstatus: 0: 加关注, 1: 取消关注, 2: 互相关注, 3:取消相互关注
	public function format_follow(&$user, $myuid, $uid) {
		if($uid != $myuid) {
			$user['followstatus'] = $this->follow->check_follow($myuid, $uid);
		}
	}
	
	// ----------------------> 加密解密
	public function verify_password($password1, $password2, $salt) {
		return $this->md5_md5($password1, $salt) == $password2;
	}
	
	public function md5_md5($s, $salt = '') {
		return md5(md5($s).$salt);
	}
	
	public function encrypt_auth($uid, $username, $password, $groupid, $accesson) {
		$password = substr($password, 0, 8);
		
		$time = $_SERVER['time'];
		$ip = $_SERVER['ip'];
		
		// 所有项目中，不允许有\t，否则可能会被伪造
		$xn_auth = encrypt("$uid	$username	$groupid	$password	$ip	$time	$accesson", $this->conf['auth_key']);
		return $xn_auth;
	}
	
	public function decrypt_auth($xn_auth) {
		$s = decrypt($xn_auth, $this->conf['auth_key']);
		$return =  array('uid'=>0, 'username'=>'', 'groupid'=>0, 'password'=>'', 'ip_right'=>FALSE, 'cookietime'=>0, 'accesson'=>0);
		if(!$s) {
			return $return;
		}
		$arr = explode("\t", $s);
		
		if(count($arr) < 7) {
			return $return;
		}
		$return = array (
			'uid'=>intval($arr[0]),
			'username'=>$arr[1],
			'groupid'=>intval($arr[2]),
			'password'=>$arr[3],
			'ip_right'=>$_SERVER['ip'] == $arr[4],
			'cookietime'=>$arr[5],
			'accesson'=>$arr[6],
		);
		return $return;
	}
	
	// 每次清理掉2000个相关帖子，调用多次
	// 如果超时则 pagesize / 4
	// $keepuser: 给 $user 留一个尸体
	public function xdelete($uid, $keepuser = FALSE) {
		
		// 每次遍历的mypost数
		$pagesize = 2000;
		
		// 防止超时
		$lastpagesize = $this->kv->get('delete_user_pagesize');
		!empty($lastpagesize) && $lastpagesize > 4 && $pagesize = ceil($lastpagesize / 4);
		$this->kv->set('delete_user_pagesize', $lastpagesize);
		
		$user = $this->user->read($uid);
		
		// 遍历所有参与过的主题。
		$mypostlist = $this->mypost->get_list_by_uid($uid, 1, $pagesize);
		
		$thread_return = array();
		$post_return = array();
		//$return = array('forum'=>array(), 'user'=>array());
		foreach($mypostlist as &$post) {
			$fid = $post['fid'];
			$tid = $post['tid'];
			$pid = $post['pid'];
			$thread = $this->thread->read($fid, $tid);
			
			//!isset($return['forum'][$fid]) && $return['forum'][$fid] = array('threads'=>0, 'posts'=>0);
			//!isset($return['user'][$uid]) && $return['user'][$uid] = array('threads'=>0, 'posts'=>0, 'credits'=>0);
			//$rforum = &$return['forum'][$fid];
			//$ruser = &$return['user'][$uid];
		
			// 主题
			if($thread['firstpid'] == $pid) {
				// 删除所有回复。。。更新受影响用户的发帖数，积分。这个比较麻烦。
				$return = $this->thread->xdelete($fid, $tid, FALSE);
				$this->thread->xdelete_merge_return($thread_return, $return);
			// 回复
			} else {
				// 遍历所有页码，删除，不整理楼层！ todo: 对于高楼，这里会有性能问题！
				//if($thread['posts'] > 1000000) continue; // 为了保险，跳过高层，万恶的高楼~ 
				$totalpage = ceil($thread['posts'] / $this->conf['pagesize']);
				for($i = 1; $i <= $totalpage; $i++) {
					$postlist = $this->post->index_fetch(array('fid'=>$fid, 'tid'=>$tid, 'page'=>$i), array(), 0, $this->conf['pagesize']);
					foreach($postlist as $post) {
						if($post['uid'] != $uid) continue;
						$return = $this->post->xdelete($fid, $post['pid'], FALSE);
						$this->post->xdelete_merge_return($post_return, $return);
					}
				}
			}
			$this->mypost->delete($post['uid'], $post['fid'], $post['pid']);
		}
		
		$this->thread->xdelete_update($thread_return);
		$this->post->xdelete_update($post_return);
		
		$n  = count($mypostlist);
		if($n < $pagesize) {
			
			// 清理 follow
			$this->follow->index_delete(array('uid'=>$uid));
			$this->follow->index_delete(array('fuid'=>$uid));
			$this->pm->index_delete(array('uid1'=>$uid));
			$this->pm->index_delete(array('uid2'=>$uid));
			$this->pmcount->index_delete(array('uid1'=>$uid));
			$this->pmcount->index_delete(array('uid2'=>$uid));
			//$this->pmnew->index_delete(array('recvuid'=>$uid)); // 强制清除可能导致消息提示不停的闪动
			//$this->pmnew->index_delete(array('senduid'=>$uid)); // 强制清除可能导致消息提示不停的闪动
			$this->user_access->delete($uid);
			// 清理精华
			
			// 清理 pm
			if($keepuser) {
				$user['groupid'] = 7;
				$user['threads'] = 0;
				$user['posts'] = 0;
				$user['myposts'] = 0;
				$user['credits'] = 0;
				$user['golds'] = 0;
				$user['newpms'] = 0;
				$user['newfeeds'] = 0;
				$user['follows'] = 0;
				$user['followeds'] = 0;
				$this->update($user);
			} else {
				$this->delete($uid);
				$this->runtime->xset('users', '-1');
			}
				
			// 删除其主题
			// 删除回帖
			// 删除精华
			// 删除附件
			// 不调整楼层
			// 清除首页缓存
			// 更新版块主题数，回复数，精华数
			// xdelete 里面已经搞定上述操作
		}
		
		$this->kv->delete('delete_user_pagesize');
		
		// hook user_model_xdelete_end.php
		
		return $n < $pagesize;
	}
	
	// hook user_model_end.php
}
?>