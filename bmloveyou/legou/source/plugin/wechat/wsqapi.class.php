<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: wsqapi.class.php 34716 2014-07-14 08:28:32Z nemohou $
 */

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class WSQAPI {

	function forumdisplay_variables(&$variables) {
		global $_G;
		if(!$_G['wechat']['setting']['wsq_allow'] || !$_G['wechat']['setting']['showactivity']['tids']) {
			return;
		}
		$tids = array();
		foreach ($variables['forum_threadlist'] as &$thread) {
			if(in_array($thread['tid'], $_G['wechat']['setting']['showactivity']['tids'])) {
				$thread['showactivity'] = 1;
				$tids[] = $thread['tid'];
			}
		}
		$activities = C::t('forum_activity')->fetch_all($tids);
		foreach($activities as $tid => $activity) {
			$variables['showactivity'][$tid]['starttimefrom'] = dgmdate($activities[$tid]['starttimefrom']);
			$variables['showactivity'][$tid]['expiration'] = dgmdate($activities[$tid]['expiration']);
			$variables['showactivity'][$tid]['applynumber'] = $activities[$tid]['applynumber'];
			$variables['showactivity'][$tid]['thumb'] = $activity['aid'] ? $_G['siteurl'].getforumimg($activity['aid'], 0, 400, 400) : '';
		}
	}

	function viewthread_variables(&$variables) {
		if(!showActivity::init()) {
			return;
		}
		global $_G;
		$variables['thread']['showactivity'] = 1;
		$variables['special_activity']['thumb'] = preg_match('/^http:\//', $GLOBALS['activity']['thumb']) ? $GLOBALS['activity']['thumb'] : $_G['siteurl'].$GLOBALS['activity']['thumb'];
		unset($variables['special_activity']['attachurl']);
		$posts = DB::fetch_all("SELECT pid, voters FROM %t WHERE tid=%d", array('forum_debatepost', $_G['tid']), 'pid');
		$voters = array();
		foreach($variables['postlist'] as &$post) {
			$post['voters'] = intval($posts[$post['pid']]['voters']);
		}
		require_once libfile('function/attachment');
		if(empty($_GET['viewnew']) && empty($_GET['viewpid'])) {
			foreach($posts as $vpost) {
				if($vpost['voters'] > 0) {
					$voters[$vpost['pid']] = $vpost['voters'];
				}
			}
			arsort($voters);
			$voters = array_slice($voters, 0, 10, 1);
			$vpids = array_keys($voters);
			$toparr = C::t('forum_post')->fetch_all('tid:'.$_G['tid'], $vpids, false);
			$top = 1;
			foreach($voters as $pid => &$data) {
				$toparr[$pid] = array(
				    'pid' => $pid,
				    'author' => $toparr[$pid]['author'],
				    'authorid' => $toparr[$pid]['authorid'],
				    'voters' => $data,
				    'top' => $top++,
				);
				$data = $toparr[$pid];
			}
			$variables['special_activity']['top_postlist'] = $voters;
			parseattach($vpids, array(), $variables['special_activity']['top_postlist']);
			$variables['special_activity']['top_postlist'] = array_values($variables['special_activity']['top_postlist']);
		}
		if(!empty($_GET['viewpid'])) {
			$comments = array();
			foreach($GLOBALS['comments'][$_GET['viewpid']] as $comment) {
				$comments[] = array(
					'author' => $comment['author'],
					'authorid' => $comment['authorid'],
					'avatar' => avatar($comment['authorid'], 'small', 1),
					'message' => $comment['comment'],
					'dateline' => strip_tags(dgmdate($comment['dateline'], 'u')),
				);
			}
			$variables['postlist'] = array_merge($variables['postlist'], $comments);
		}
	}

}