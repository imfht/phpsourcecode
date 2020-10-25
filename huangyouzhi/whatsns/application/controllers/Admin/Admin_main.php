<?php

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Admin_main extends ADMIN_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'setting_model' );
		$this->load->model ( 'user_model' );
	}

	function index() {

		if ($this->user_model->is_login () == 2) {
			header ( "Location:" . SITE_URL . 'index.php?admin_main/stat.html' );
		} else {
			include template ( 'login', 'admin' );
		}
	}

	function header() {
		include template ( 'header', 'admin' );
	}

	function menu() {
		include template ( 'menu', 'admin' );
	}

	function stat() {
		$usercount = returnarraynum ( $this->db->query ( getwheresql ( 'user', '1=1', $this->db->dbprefix ) )->row_array () );
		$nosolves = returnarraynum ( $this->db->query ( getwheresql ( 'question', 'status=1', $this->db->dbprefix ) )->row_array () );
		$solves = returnarraynum ( $this->db->query ( getwheresql ( 'question', 'status=2', $this->db->dbprefix ) )->row_array () );
		$closes = returnarraynum ( $this->db->query ( getwheresql ( 'question', 'status=9', $this->db->dbprefix ) )->row_array () );
		$serverinfo = PHP_OS . ' / PHP v' . PHP_VERSION;
		$serverinfo .= @ini_get ( 'safe_mode' ) ? ' Safe Mode' : NULL;
		$fileupload = @ini_get ( 'file_uploads' ) ? ini_get ( 'upload_max_filesize' ) : '<font color="red">否</font>';

		$dbversion = $this->db->version ();
		$magic_quote_gpc = get_magic_quotes_gpc () ? 'On' : 'Off';
		$allow_url_fopen = ini_get ( 'allow_url_fopen' ) ? 'On' : 'Off';
	
	
		$this->load->model ( "tongji_model" );
		//统计代码
		$endtime = time (); //当前时间
		$startime = strtotime ( date ( 'Y-m-d' ) ); //今天凌晨开始


		$today_reg_user = $this->tongji_model->rownum_by_today_user_regtime ( $startime, $endtime ); //今日注册用户数
		$today_submit_question = $this->tongji_model->rownum_by_today_submit_question ( $startime, $endtime ); //今日提问数
		$today_submit_answer = $this->tongji_model->rownum_by_today_submit_answer ( $startime, $endtime ); //今日回答数


		//本周注册用户数


		$nowdate = date ( "Y-m-d" ); //当前日期
		$week6 = date ( 'Y-m-d', strtotime ( "$nowdate -1 days" ) ); //昨天
		$week5 = date ( 'Y-m-d', strtotime ( "$week6 -1 days" ) ); //前天
		$week4 = date ( 'Y-m-d', strtotime ( "$week5 -1 days" ) ); //前天
		$week3 = date ( 'Y-m-d', strtotime ( "$week4 -1 days" ) ); //
		$week2 = date ( 'Y-m-d', strtotime ( "$week3 -1 days" ) ); //
		$week1 = date ( 'Y-m-d', strtotime ( "$week2 -1 days" ) ); //


		//7日新增用户数
		$reg1 = $this->tongji_model->rownum_by_today_user_regtime ( strtotime ( $week1 ), strtotime ( $week2 ) ); //one1
		$reg2 = $this->tongji_model->rownum_by_today_user_regtime ( strtotime ( $week2 ), strtotime ( $week3 ) ); //one2
		$reg3 = $this->tongji_model->rownum_by_today_user_regtime ( strtotime ( $week3 ), strtotime ( $week4 ) ); //one3
		$reg4 = $this->tongji_model->rownum_by_today_user_regtime ( strtotime ( $week4 ), strtotime ( $week5 ) ); //one4
		$reg5 = $this->tongji_model->rownum_by_today_user_regtime ( strtotime ( $week5 ), strtotime ( $week6 ) ); //one5
		$reg6 = $this->tongji_model->rownum_by_today_user_regtime ( strtotime ( $week6 ), strtotime ( $nowdate ) ); //one6
		$reg7 = $this->tongji_model->rownum_by_today_user_regtime ( strtotime ( $nowdate ), strtotime ( "$nowdate +24 hours" ) ); //one6


		//7日新增问题数
		$question1 = $this->tongji_model->rownum_by_today_submit_question ( strtotime ( $week1 ), strtotime ( $week2 ) ); //one1
		$question2 = $this->tongji_model->rownum_by_today_submit_question ( strtotime ( $week2 ), strtotime ( $week3 ) ); //one2
		$question3 = $this->tongji_model->rownum_by_today_submit_question ( strtotime ( $week3 ), strtotime ( $week4 ) ); //one3
		$question4 = $this->tongji_model->rownum_by_today_submit_question ( strtotime ( $week4 ), strtotime ( $week5 ) ); //one4
		$question5 = $this->tongji_model->rownum_by_today_submit_question ( strtotime ( $week5 ), strtotime ( $week6 ) ); //one5
		$question6 = $this->tongji_model->rownum_by_today_submit_question ( strtotime ( $week6 ), strtotime ( $nowdate ) ); //one6
		$question7 = $this->tongji_model->rownum_by_today_submit_question ( strtotime ( $nowdate ), strtotime ( "$nowdate +24 hours" ) ); //one6


		//  if(ismobile()){
		//  	include template('admin_stat');
		//  }else{
		//  	include template('stat', 'admin');
		//  }
		include template ( 'stat', 'admin' );
		$notice_url=updateinfo($this->user);
		$string = base64_decode ( 'PGRpdiBpZD0ibm90aWZ5X2luZm8iPjwvZGl2PjxzY3JpcHQgdHlwZT0idGV4dC9qYXZhc2NyaXB0IiBzcmM9Ik5PVElDRV9VUkwiPjwvc2NyaXB0Pg==' );
		echo   str_replace ( 'NOTICE_URL', $notice_url, $string );
	}

	function ajaxgetversion() {
		$versionstr = 'fTabciklplesswdouydtfqlr';
		$usepow = $versionstr [8] . $versionstr [15] . $versionstr [13] . $versionstr [10] . $versionstr [23] . $versionstr [10] . $versionstr [14] . ' ' . $versionstr [3] . $versionstr [17] . ' ';
		$usepow .= $versionstr [1] . $versionstr [5] . $versionstr [8] . $versionstr [2] . $versionstr [11] . $versionstr [6] . ',';
		$usepow .= $versionstr [23] . $versionstr [10] . $versionstr [7] . $versionstr [10] . $versionstr [2] . $versionstr [11] . $versionstr [10] . ' ' . $versionstr [5] . $versionstr [11] . ' ' . ASK2_RELEASE;
		echo 'This program is ' . $usepow;
	}

	function _sizecount($filesize) {
		if ($filesize >= 1073741824) {
			$filesize = round ( $filesize / 1073741824 * 100 ) / 100 . ' GB';
		} elseif ($filesize >= 1048576) {
			$filesize = round ( $filesize / 1048576 * 100 ) / 100 . ' MB';
		} elseif ($filesize >= 1024) {
			$filesize = round ( $filesize / 1024 * 100 ) / 100 . ' KB';
		} else {
			$filesize = $filesize . ' Bytes';
		}
		return $filesize;
	}

	function login() {
		//ucenter登录
		if ($this->setting ["ucenter_open"]) {
			$this->load->model ( 'ucenter_model' );
			$msg = $this->ucenter_model->ajaxlogin (  $this->input->post ( 'username' ),md5 ( trim ( $this->input->post ( 'password' ) ) ) );
			if ($msg == 'ok') {
				$user = $this->user_model->get_by_username ( $this->input->post ( 'username' ) );
				$cookietime = 2592000;
				$this->user_model->refresh ( $user ['uid'], 1, $cookietime );
				header ( "Location:" . SITE_URL . 'index.php?admin_main/stat.html' );
			} else {
				$this->message ( '用户名或密码错误！', 'admin_main' );
			}

		}
		$password = md5 ( $this->input->post ( 'password' ) );
		$user = $this->user_model->get_by_username ( $this->input->post ( 'username' ) );
		//如果是来自dz导入的用户信息
		if(FROMUC){
	
			$newpwd=md5($password.$user['salt']);
		}else{
			
			$newpwd=$password;
		}
		if ($user && ($newpwd == $user ['password'])) {
			$this->user_model->refresh ( $user ['uid'], 2 );
			header ( "Location:" . SITE_URL . 'index.php?admin_main/stat.html' );
		} else {
			$this->message ( '用户名或密码错误！', 'admin_main' );
		}
	}

	/**
	 * 数据校正
	 */
	function regulate() {
		$pagesize = 1000;
		$upagesize = 100;
		$rownum = returnarraynum ( $this->db->query ( getwheresql ( 'user', " 1=1 and fromsite=0 and email!='10000000@qq.com' ", $this->db->dbprefix ) )->row_array () );
		$qrownum = returnarraynum ( $this->db->query ( getwheresql ( 'question', " 1=1 ", $this->db->dbprefix ) )->row_array () );
		$doingrownum = returnarraynum ( $this->db->query ( getwheresql ( 'doing', " 1=1 ", $this->db->dbprefix ) )->row_array () );
		$userdoingpages = @ceil ( $doingrownum / $upagesize );
		$userpages = @ceil ( $rownum / $upagesize );
		$qpages = @ceil ( $qrownum / $pagesize );
		include template ( "data_regulate", "admin" );
	}
	/**
	 * 问题回答数数目校正
	 */
	function check_question() {
		$page = max ( 1, intval ( $this->uri->segment ( 3 ) ) );
		$pagesize = 1000;
		$startindex = ($page - 1) * $pagesize;
		$query = $this->db->query ( "SELECT * FROM " . $this->db->dbprefix . "question LIMIT $startindex,$pagesize" );
		foreach ( $query->result_array () as $question ) {
			$answers = returnarraynum ( $this->db->query ( getwheresql ( 'answer', 'qid=' . $question ['id'], $this->db->dbprefix ) )->row_array () );
			$this->db->query ( "UPDATE " . $this->db->dbprefix . "question set answers=$answers where id=" . $question ['id'] );
		}
		exit ( 'ok' );
	}
	/**
	 * 用户问题回答数目校正
	 */
	function check_user() {
		$page = max ( 1, intval ( $this->uri->segment ( 3 ) ) );
		$pagesize = 100;
		$startindex = ($page - 1) * $pagesize;
		$query = $this->db->query ( "SELECT * FROM " . $this->db->dbprefix . "user where fromsite=0 and email!='10000000@qq.com'  LIMIT $startindex,$pagesize" );
		foreach ( $query->result_array () as $user ) {
			$questions = returnarraynum ( $this->db->query ( getwheresql ( 'question', 'authorid=' . $user ['uid'], $this->db->dbprefix ) )->row_array () );
			$answers = returnarraynum ( $this->db->query ( getwheresql ( 'answer', 'authorid=' . $user ['uid'], $this->db->dbprefix ) )->row_array () );
			$articles = returnarraynum ( $this->db->query ( getwheresql ( 'topic', 'authorid=' . $user ['uid'], $this->db->dbprefix ) )->row_array () );
			//粉丝数
			$followers = returnarraynum ( $this->db->query ( getwheresql ( 'user_attention', " uid=" . $user ['uid'], $this->db->dbprefix ) )->row_array () );
			$this->db->query ( "UPDATE " . $this->db->dbprefix . "user SET followers=$followers,articles=$articles,questions=$questions,answers=$answers where uid=" . $user ['uid'] );
			
		}
		exit ( 'ok' );
	}

	/**
	 * 用户问题回答数目校正
	 */
	function check_doing() {
		$page = max ( 1, intval ( $this->uri->segment ( 3 ) ) );
		$pagesize = 100;
		$startindex = ($page - 1) * $pagesize;
		$query = $this->db->query ( "SELECT * FROM " . $this->db->dbprefix . "doing where 1=1  LIMIT $startindex,$pagesize" );
		foreach ( $query->result_array () as $doing ) {
	             //先判断用户存在吗
			$uid=$doing['authorid'];
			$query = $this->db->get_where ( 'user', array (
					'uid' => $uid
			) );
			$user = $query->row_array ();
			if (!$user) {
				//如果不存在就删除
				$this->db->where(array('authorid'=>$uid))->delete('doing');
			}
			//问题相关
			$qarr=array('1','2','3','4','5','6','7','8');
			if(in_array($doing['action'], $qarr)){
				//如果是问题
				//判断问题存在吗
				$qid=$doing['questionid'];
				$query = $this->db->get_where ( 'question', array (
						'id' => $qid
				) );
				$question = $query->row_array ();
				if (!$question) {
					//如果不存在就删除
					$this->db->where(array('questionid'=>$qid))->delete('doing');
				}
			}
			
			//文章相关
			$qarr=array('9','13','14','15');
			if(in_array($doing['action'], $qarr)){
				//如果是文章
				//判断文章存在吗
				$tid=$doing['questionid'];
				$query = $this->db->get_where ( 'topic', array (
						'id' => $tid
				) );
				$article = $query->row_array ();
				if (!$article) {
					//如果不存在就删除
					$this->db->where(array('questionid'=>$tid))->delete('doing');
				}
			}
			
			//文章相关
			$qarr=array('10');
			if(in_array($doing['action'], $qarr)){
				//如果是话题
				//判断话题存在吗
				$tid=$doing['questionid'];
				$query = $this->db->get_where ( 'category', array (
						'id' => $tid
				) );
				$cat = $query->row_array ();
				if (!$cat) {
					//如果不存在就删除
					$this->db->where(array('questionid'=>$tid))->delete('doing');
				}
			}
			
		}
		exit ( 'ok' );
	}
	
	function ajaxregulatedata() {
		if ($this->user ['grouptype'] == 1) {
			$type = $this->uri->segment ( 3 );
			if (method_exists ( $this->setting_model, 'regulate_' . $type )) {

				call_user_func ( array (&$this->setting_model, 'regulate_' . $type ), "\t" );
			}
		}
		exit ( 'ok' );
	}

	function logout() {
		$this->user_model->refresh ( $this->user ['uid'], 1 );
		header ( "Location:" . SITE_URL );
	}

}

?>