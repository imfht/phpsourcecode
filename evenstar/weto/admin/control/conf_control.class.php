<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'admin/control/admin_control.class.php';

class conf_control extends admin_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->check_admin_group();
	}
	
	public function on_index() {
		
		$this->on_base();
	}
	
	public function on_base() {
		$conf = $this->conf;
		
		$input = array();
		$kvconf = $this->kv->xget('conf') + $this->kv->xget('conf_ext');
		$error = $post = array();
		if($this->form_submit()) {
			$post['app_name'] = core::gpc('app_name', 'P');
			$post['credits_policy_post'] = intval(core::gpc('credits_policy_post', 'P'));
			$post['credits_policy_thread'] = intval(core::gpc('credits_policy_thread', 'P'));
			$post['credits_policy_digest_1'] = intval(core::gpc('credits_policy_digest_1', 'P'));
			$post['credits_policy_digest_2'] = intval(core::gpc('credits_policy_digest_2', 'P'));
			$post['credits_policy_digest_3'] = intval(core::gpc('credits_policy_digest_3', 'P'));
			$post['golds_policy_post'] = intval(core::gpc('golds_policy_post', 'P'));
			$post['golds_policy_thread'] = intval(core::gpc('golds_policy_thread', 'P'));
			$post['golds_policy_digest_1'] = intval(core::gpc('golds_policy_digest_1', 'P'));
			$post['golds_policy_digest_2'] = intval(core::gpc('golds_policy_digest_2', 'P'));
			$post['golds_policy_digest_3'] = intval(core::gpc('golds_policy_digest_3', 'P'));
			
			$post['reg_email_on'] = intval(core::gpc('reg_email_on', 'P'));
			$post['reg_init_golds'] = intval(core::gpc('reg_init_golds', 'P'));
			$post['resetpw_on'] = intval(core::gpc('resetpw_on', 'P'));
			$post['app_copyright'] = core::gpc('app_copyright', 'P');
			$post['seo_title'] = core::gpc('seo_title', 'P');
			$post['seo_keywords'] = core::gpc('seo_keywords', 'P');
			$post['seo_description'] = core::gpc('seo_description', 'P');
			$post['threadlist_hotviews'] = intval(core::gpc('threadlist_hotviews', 'P'));
			$post['post_update_expiry'] = intval(core::gpc('post_update_expiry', 'P'));
			$post['search_type'] = core::gpc('search_type', 'P');
			$post['sphinx_host'] = core::gpc('sphinx_host', 'P');
			$post['sphinx_port'] = core::gpc('sphinx_port', 'P');
			$post['sphinx_datasrc'] = core::gpc('sphinx_datasrc', 'P');
			$post['sphinx_deltasrc'] = core::gpc('sphinx_deltasrc', 'P');
			$post['china_icp'] = core::gpc('china_icp', 'P');
			$post['footer_js'] = core::gpc('footer_js', 'P');
			$post['site_pv'] = intval(core::gpc('site_pv', 'P'));
			$post['site_runlevel'] = intval(core::gpc('site_runlevel', 'P'));
			$post['forum_index_pagesize'] = intval(core::gpc('forum_index_pagesize', 'P'));
			$post['reg_on'] = intval(core::gpc('reg_on', 'P'));
			
			// hook admin_conf_base_gpc_after.php
			
			// check 数据格式
			$error['app_name'] = $this->check_app_name($post['app_name']);
			
			if(!array_filter($error)) {
				$error = array();
				
				// 全局的，加载合并到 runtime, 每次请求都从 runtime 加载。
				foreach(array('app_name', 'app_copyright', 'seo_title', 'seo_keywords', 'seo_description', 
					'threadlist_hotviews', 'post_update_expiry', 'china_icp', 'footer_js', 'site_pv', 'site_runlevel', 'forum_index_pagesize','reg_on', 'search_type'
					) as $k) {
					$this->runtime->xset($k, $post[$k], 'runtime');
					$this->kv->xset($k, $post[$k], 'conf');
				}
				
				// 局部的，按需加载 conf_ext
				foreach(array('credits_policy_post', 'credits_policy_thread', 
					'credits_policy_digest_1', 'credits_policy_digest_2', 'credits_policy_digest_3',
					'golds_policy_post', 'golds_policy_thread',
					'golds_policy_digest_1', 'golds_policy_digest_2', 'golds_policy_digest_3',
					'reg_email_on', 'reg_init_golds', 'resetpw_on',
					'sphinx_host', 'sphinx_port', 'sphinx_datasrc', 'sphinx_deltasrc',
					) as $k) {
					$this->kv->xset($k, $post[$k], 'conf_ext');
				}
				
				// $this->kv->xsave();
				
				// hook admin_conf_base_set_after.php
			}
			
		}
		
		// 用 $post 覆盖 $kvconf
		$kvconf = array_merge($kvconf, $post);
		
		$input['app_name'] = form::get_text('app_name', htmlspecialchars($kvconf['app_name']), 300);
		$input['app_copyright'] = form::get_text('app_copyright', htmlspecialchars($kvconf['app_copyright']), 300);
		$input['credits_policy_post'] = form::get_text('credits_policy_post', $kvconf['credits_policy_post'], 50);
		$input['credits_policy_thread'] = form::get_text('credits_policy_thread', $kvconf['credits_policy_thread'], 50);
		$input['credits_policy_digest_1'] = form::get_text('credits_policy_digest_1', $kvconf['credits_policy_digest_1'], 50);
		$input['credits_policy_digest_2'] = form::get_text('credits_policy_digest_2', $kvconf['credits_policy_digest_2'], 50);
		$input['credits_policy_digest_3'] = form::get_text('credits_policy_digest_3', $kvconf['credits_policy_digest_3'], 50);
		$input['golds_policy_post'] = form::get_text('golds_policy_post', $kvconf['golds_policy_post'], 50);
		$input['golds_policy_thread'] = form::get_text('golds_policy_thread', $kvconf['golds_policy_thread'], 50);
		$input['golds_policy_digest_1'] = form::get_text('golds_policy_digest_1', $kvconf['golds_policy_digest_1'], 50);
		$input['golds_policy_digest_2'] = form::get_text('golds_policy_digest_2', $kvconf['golds_policy_digest_2'], 50);
		$input['golds_policy_digest_3'] = form::get_text('golds_policy_digest_3', $kvconf['golds_policy_digest_3'], 50);
		$input['reg_on'] = form::get_radio_yes_no('reg_on', $kvconf['reg_on']);
		$input['reg_email_on'] = form::get_radio_yes_no('reg_email_on', $kvconf['reg_email_on']);
		$input['reg_init_golds'] = form::get_text('reg_init_golds', $kvconf['reg_init_golds'], 50);
		$input['resetpw_on'] = form::get_radio_yes_no('resetpw_on', $kvconf['resetpw_on']);
		$input['seo_title'] = form::get_text('seo_title', htmlspecialchars($kvconf['seo_title']), 300);
		$input['seo_keywords'] = form::get_text('seo_keywords', htmlspecialchars($kvconf['seo_keywords']), 300);
		$input['seo_description'] = form::get_text('seo_description', htmlspecialchars($kvconf['seo_description']), 300);
		$input['threadlist_hotviews'] = form::get_text('threadlist_hotviews', $kvconf['threadlist_hotviews'], 50);
		$input['post_update_expiry'] = form::get_text('post_update_expiry', $kvconf['post_update_expiry'], 50);
		$input['search_type'] = form::get_radio('search_type', array(''=>'无', 'title'=>'标题', 'baidu'=>'百度', 'google'=>'谷歌', 'bing'=>'Bing', 'sphinx'=>'Sphinx'), $kvconf['search_type']);
		$input['sphinx_host'] = form::get_text('sphinx_host', $kvconf['sphinx_host'], 150);
		$input['sphinx_port'] = form::get_text('sphinx_port', $kvconf['sphinx_port'], 100);
		$input['sphinx_datasrc'] = form::get_text('sphinx_datasrc', $kvconf['sphinx_datasrc'], 100);
		$input['sphinx_deltasrc'] = form::get_text('sphinx_deltasrc', $kvconf['sphinx_deltasrc'], 100);
		$input['china_icp'] = form::get_text('china_icp', htmlspecialchars($kvconf['china_icp']), 150);
		$input['footer_js'] = form::get_text('footer_js', htmlspecialchars($kvconf['footer_js']), 300);
		$input['site_pv'] = form::get_text('site_pv', $kvconf['site_pv'], 70);
		$input['site_runlevel'] = form::get_radio('site_runlevel', array(0=>'所有人可访问', 1=>'会员可访问', 2=>'版主可访问', 3=>'管理员可访问', 4=>'全站只读'), $kvconf['site_runlevel']);
		$input['forum_index_pagesize'] =  form::get_text('forum_index_pagesize', $kvconf['forum_index_pagesize'], 50);
		
		// hook admin_conf_base_input_after.php
		
		$this->view->assign('input', $input);
		$this->view->assign('kvconf', $kvconf);
		$this->view->assign('error', $error);
		$this->view->display('conf_base.htm');
	}
	
	private function replace_key_value($k, $v, $s) {
		$s = preg_replace('#\$'.$k.'\s*=\s*(\d+?);#ism', "\$$k = $v;", $s);
		$s = preg_replace('#\$'.$k.'\s*=\s*\'(.*?)\';#ism', "\$$k = '$v';", $s);
		return $s;
	}
	
	// 设置 SMTP 账号
	public function on_mail() {
		$error = array();
		
		$mailconf = $this->kv->get('mail_conf');
		
		$sendtype = &$mailconf['sendtype'];
		$smtplist = &$mailconf['smtplist'];
		if($this->form_submit()) {
			$email = (array)core::gpc('email', 'P');
			$host = (array)core::gpc('host', 'P');
			$port = (array)core::gpc('port', 'P');
			$user = (array)core::gpc('user', 'P');
			$pass = (array)core::gpc('pass', 'P');
			$delete = (array)core::gpc('delete', 'P');
			$sendtype = intval(core::gpc('sendtype', 'P'));
			$smtplist = array();
			foreach($email as $k=>$v) {
				empty($port[$k]) && $port[$k] = 25;
				if(in_array($k, $delete)) continue;
				if(empty($email[$k]) || empty($host[$k]) || empty($user[$k])) continue;
				$smtplist[$k] = array('email'=>$email[$k], 'host'=>$host[$k], 'port'=>$port[$k], 'user'=>$user[$k], 'pass'=>$pass[$k]);
			}
			
			$this->kv->set('mail_conf', $mailconf);
			
			$mail_smtplist = $smtplist;
			
			// hook admin_conf_mail_view_before.php
			
		}
		
		$input = array();
		$input['sendtype'] = form::get_radio('sendtype', array(0=>'PHP内置mail函数 ', 1=>'SMTP 方式'), $sendtype);
		
		$this->view->assign('error', $error);
		$this->view->assign('smtplist', $smtplist);
		$this->view->assign('input', $input);
		
		// hook admin_conf_mail_view_before.php
		
		$this->view->display('conf_mail.htm');
	}
	
	// 关键词过滤
	public function on_badword() {
		$badword = $this->kv->get('badword');
		$badword === FALSE && $badword = array();
		$badword_on = $this->conf['badword_on'];
		$error = $input = array();
		if($this->form_submit()) {
			$badword = trim(core::gpc('badword', 'P'));
			$badword = str_replace("　 ", ' ', $badword);
			$badword = str_replace("：", ':', $badword);
			$badword = str_replace(": ", ':', $badword);
			$badword = preg_replace('#\s+#is', ' ', $badword);
			$badword_on = intval(core::gpc('badword_on', 'P'));
			// $error['badword'] = $badword;
			$badword = misc::explode(':', ' ', $badword);
			//$badword = array_filter($badword);

			$this->kv->set('badword', $badword);
			$this->kv->xset('badword_on', $badword_on);
			$this->runtime->xset('badword_on', $badword_on);
			$this->conf['badword_on'] = $badword_on;
		}
		$input['badword_on'] = form::get_radio_yes_no('badword_on', $this->conf['badword_on']);
		core::htmlspecialchars($badword);
		$badword = misc::implode(':', ' ', $badword);
		$this->view->assign('badword', $badword);
		$this->view->assign('input', $input);
		$this->view->assign('error', $error);
		$this->view->display('conf_badword.htm');
	}

	public function on_cache() {
		
		$tmp = core::gpc('tmp', 'P');
		$runtime = core::gpc('runtime', 'P');
		$forum = core::gpc('forum', 'P');
		$thread_new = core::gpc('thread_new', 'P');
		$count_maxid = core::gpc('count_maxid', 'P');
		
		// tmp
		if($tmp) {
			$this->clear_tmp();
		}
		
		// 清空版块缓存
		if($forum) {
			$forumarr = $this->conf['forumarr'];
			foreach($forumarr as $fid=>$name) {
				$this->mcache->clear('forum', $fid);	
			}
			$this->runtime->xupdate('forumarr');
		}
		
		// 最新发帖
		if($thread_new) {
			// 从 thread 表中获取最新数据，取两天内的主题，可能会超时？内存不够？
			$newlist = $this->thread->index_fetch(array('dateline' => array('>'=>$_SERVER['time'] - 86400 * 2)), array(), 0, 10000);
			if(count($newlist) < 30) {
				$newlist = $this->thread->index_fetch(array('dateline' => array('>'=>$_SERVER['time'] - 86400 * 30)), array(), 0, 10000);
			}
			foreach($newlist as $new) {
				$this->thread_new->create(array('fid'=>$new['fid'], 'tid'=>$new['tid'], 'dateline'=>$new['dateline'], 'lastpost'=>$new['lastpost']));
			}
			
			unset($newlist);
		}
		
		// 校对 framework 的 count, maxid
		if($count_maxid) {

                        // copy  from install_mongodb           
                        $maxs = array(
                                'group'=>'groupid',
                                'user'=>'uid',
                                'user_access'=>'uid',
                                'forum'=>'fid',
                                'forum_access'=>'fid',
                                'thread'=>'tid',
                                'post'=>'pid',
                                'attach'=>'aid',
                                'pm'=>'pmid'
                        );

                        foreach($maxs as $table=>$maxcol) {
                                if(isset($this->$table->maxcol)) {
                                        $m = $this->$table->index_maxid();
                                        $this->$table->maxid($m);
                                }
                                
                                $n = $this->$table->index_count();
                                $this->$table->count($n);
                        }
                        
                        // online 比较特殊
                        $n = $this->online->index_count();
                        $this->online->count($n);
                        $this->runtime->xset('onlines', $n);
                        
                        $posts = $this->post->count();
                        $users = $this->user->count();
                        $this->kv->xset('posts', $posts);
                        $this->kv->xset('users', $users);
                        $this->runtime->xset('posts', $posts);
                        $this->runtime->xset('users', $users);
                }
                
                // 清空 runtime
		if($runtime) {
			$this->runtime->truncate();
		}
                
		// hook admin_conf_cache_view_before.php
		
		$this->view->display('conf_cache.htm');
	}

	private function check_app_name(&$app_name) {
		if(utf8::strlen($app_name) > 32) {
			return '站点名称不能超过32个字符: '.$app_name.'<br />';
		}
		return '';
	}

	//hook admin_conf_control_after.php

}

?>