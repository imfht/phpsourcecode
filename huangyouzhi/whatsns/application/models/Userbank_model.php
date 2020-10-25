<?php

class Userbank_model extends CI_Model {
	function __construct() {
		parent::__construct ();
		$this->load->database ();
	}
	function getlistbytouid($touid, $start, $limit) {
		$recargelist = array ();
		$query = $this->db->query ( "SELECT * FROM " . $this->db->dbprefix . "userbank where touid=$touid ORDER BY time DESC LIMIT $start,$limit" );
		foreach ( $query->result_array () as $money ) {
			$fromuser = $this->getuser ( $money ['fromuid'] );

			$money ['fromusername'] = $fromuser ['username'];
			$money ['format_time'] = tdate ( $money ['time'] );

			$recargelist [] = $money;
		}
		return $recargelist;
	}
	function getuser($uid) {
		$user = $this->db->query ( "SELECT * FROM " . $this->db->dbprefix . "user WHERE uid='$uid'" )->row_array ();
		return $user;
	}
	function getsummoneybytouid($touid) {

		$mrmb = $this->db->query ( "SELECT sum(money) as rmb FROM " . $this->db->dbprefix . "userbank WHERE touid=$touid " )->row_array ();
		return $mrmb;

	}

	function getmymoney($touid, $start, $limit) {
		$recargelist = array ();
		$query = $this->db->query ( "SELECT DISTINCT `transaction_id`,`time_end`,`openid`,`out_trade_no`,`cash_fee`, `type`,`typeid`,`touid`,`haspay`,`trade_type`,`trade_state` FROM " . $this->db->dbprefix . "weixin_notify where touid=$touid  ORDER BY time_end DESC ,haspay desc LIMIT $start,$limit" );
		$suffix = '?';
		if ($this->base->setting ['seo_on']) {
			$suffix = '';
		}
		foreach ( $query->result_array () as $money ) {
			$money ['cash_fee'] = $money ['cash_fee'] / 100;
			if ($money ['haspay'] == 0) {
				$money ['msg'] = "可提现";
			} else {
				$money ['msg'] = "已经提现";
			}
			// $money['fromusername'] =$fromuser['username'];
			$money ['format_time'] = tdate ( $money ['time_end'] );
			switch ($money ['type']) {
				case 'shoudongchonzhi' :
					$money ['operation'] = '管理员手动修改用户金额';
					
					$money ['money'] = "后台修改金额" . $money ['money'] . "元";
					
					$money ['content'] = "后台管理员手动修改用户金额";
					break;
				case 'coursebuy' :
					$money ['operation'] = '购买课程';
					$course=$this->db->get_where('category',array('id'=>$money['typeid']))->row_array();
					$money ['money'] = "用户支付" . $money ['money'] . "元购买课程";
					$c_name=$course['name'];
					$money ['content'] = "购买课程[$c_name]";
					break;
				case 'confirmtixian' :
					$money ['operation'] = '管理员审核提现请求';
					
					$money ['money'] = "用户微信零钱收到" . $money ['money'] . "元";
					
					$money ['content'] = "后台管理员确认用户提现请求";
					break;
				case 'wxqbuytaocan' :
					$money ['operation'] = '购买套餐';
					$taocan=$this->db->get_where('weixiaoqiang_taocanbuy',array('taocanid'=>$money['typeid']))->row_array();
					$money ['money'] = "用户支付" . $money ['money'] . "元购买套餐";
					$c_name=$taocan['taocanname'];
					$money ['content'] = "购买套餐[$c_name]";
					break;
				case 'viewaid' :
					$money ['operation'] = '用户付费偷看';

					$mod = $this->getanswer ( $money ['typeid'] );

					$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['qid'], 2 );
					$money ['content'] = "偷看回答的问题:<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";

					break;
				case 'closeqid' :
					$money ['operation'] = '问题被关闭退还悬赏金额';
					$money ['money'] = "收入" . $money ['money'] . "元";
					$mod = $this->getquestion ( $money ['typeid'] );
					$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['id'], 2 );
					$money ['content'] = "关闭标题-><a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";

					break;
				case 'topayarticle' :
					$money ['operation'] = '用户付费阅读';
					
					$money ['money'] = "支出" . $money ['money'] . "元";
					$mod = $this->gettopic ( $money ['typeid'] );
					$viewurl = SITE_URL . $suffix . urlmap ( 'topic/getone/' . $mod ['id'], 2 );
					$money ['content'] = "您付费阅读了文章：<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					
					break;
				case 'appbuyshouhuo' :
					$money ['operation'] = '应用商店购买商品';
					
					$money ['money'] = "支出" . $money ['money'] . "元";
					
					$money ['content'] = "来自应用商店消费";
					break;
					
				case 'vertify' :
					$money ['operation'] = '用户认证';
					
					$money ['money'] = "支出" . $money ['money'] . "元";
					
					$money ['content'] = "来自用户认证费用";
					break;
				case 'payarticle' :
					$money ['operation'] = '现金付费阅读';
					$money ['money'] = "支出" . $money ['money'] . "元";
					$mod = $this->gettopic ( $money ['typeid'] );
					$viewurl = SITE_URL . $suffix . urlmap ( 'topic/getone/' . $mod ['id'], 2 );
					$money ['content'] = "<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					
					break;
				case 'topayarticle' :
					$money ['operation'] =  $money ['fromuser'] ['username'] .'现金付费阅读';
					$money ['money'] = "收入" . $money ['money'] . "元";
					$mod = $this->gettopic ( $money ['typeid'] );
					$viewurl = SITE_URL . $suffix . urlmap ( 'topic/getone/' . $mod ['id'], 2 );
					$money ['content'] = "<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					
					break;
				case 'payarticle' :
					$money ['operation'] = '用户付费阅读';
					
					$money ['money'] = "收入" . $money ['money'] . "元";
					
					$mod = $this->gettopic ( $money ['typeid'] );
					$viewurl = SITE_URL . $suffix . urlmap ( 'topic/getone/' . $mod ['id'], 2 );
					$money ['content'] = "用户付费阅读了您的文章：<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					
					break;
				case 'vertify' :
					$money ['operation'] = '用户付费认证专家';
					
					$money ['money'] = "支出" . $money ['money'] . "元";
					
					$money ['content'] = "来自用户付费认证专家";
					break;
				case 'myviewaid' :
					$money ['operation'] = '我的偷看回答';

					$mod = $this->getanswer ( $money ['typeid'] );

					$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['qid'], 2 );
					$money ['content'] = "付费偷看回答的问题:<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";

					break;
				case 'chongzhi' :
					$money ['operation'] = '用户充值';

					$money ['content'] = "来自用户充值付款";
					break;
				case 'creditchongzhi' :
					$money ['operation'] = '用户财富积分充值';

					$money ['content'] = "来自用户财富积分充值付款";
					break;
					case 'appbuy' :
					$money ['operation'] = '购买应用';

					$money ['content'] = "来自应用商店付款付款";
					break;
				case 'aid' :
					$money ['operation'] = '回答打赏';
					$mod = $this->getanswer ( $money ['typeid'] );

					$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['qid'], 2 );
					$money ['content'] = "<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					break;
				case 'tid' :
					$money ['operation'] = '文章打赏';
					$mod = $this->gettopic ( $money ['typeid'] );
					$viewurl = SITE_URL . $suffix . urlmap ( 'topic/getone/' . $mod ['id'], 2 );
					$money ['content'] = "<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";

					break;
				case 'qid' :
					$money ['operation'] = '提问悬赏';
					break;
			}

			$recargelist [] = $money;
		}
		return $recargelist;
	}
	function getzhangdan($touid, $start, $limit) {
		$recargelist = array ();
		$query = $this->db->query ( "SELECT * FROM " . $this->db->dbprefix . "paylog where touid=$touid and type not in('paysite_zhuanjia','paysite_xuanshang','paysite_toukan')  ORDER BY time DESC  LIMIT $start,$limit" );
		$suffix = '?';
		if ($this->base->setting ['seo_on']) {
			$suffix = '';
		}
		foreach ( $query->result_array () as $money ) {
			$money ['time'] = tdate ( $money ['time'] );
			switch ($money ['type']) {
				case 'shoudongchonzhi' :
					$money ['operation'] = '管理员手动修改用户金额';
					
					$money ['money'] = "后台修改金额" . $money ['money'] . "元";
					
					$money ['content'] = "后台管理员手动修改用户金额";
					break;
				case 'coursebuy' :
					$money ['operation'] = '购买课程';
					$course=$this->db->get_where('category',array('id'=>$money['typeid']))->row_array();
					$money ['money'] = "用户支付" . $money ['money'] . "元购买课程";
					$c_name=$course['name'];
					$money ['content'] = "购买课程[$c_name]";
					break;
				case 'confirmtixian' :
					$money ['operation'] = '管理员审核提现请求';
					
					$money ['money'] = "用户微信零钱收到" . $money ['money'] . "元";
					
					$money ['content'] = "后台管理员确认用户提现请求";
					break;
				case 'viewaid' :
					$money ['operation'] = '用户付费偷看';
					$money ['money'] = "收入" . $money ['money'] . "元";
					$mod = $this->getanswer ( $money ['typeid'] );

					$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['qid'], 2 );
					$money ['content'] = "偷看回答的问题:<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";

					break;
				case 'closeqid' :
					$money ['operation'] = '问题被关闭退还悬赏金额';
					$money ['money'] = "收入" . $money ['money'] . "元";
					$mod = $this->getquestion ( $money ['typeid'] );
					$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['id'], 2 );
					$money ['content'] = "关闭标题-><a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";

					break;
				case 'topayarticle' :
					$money ['operation'] = '用户付费阅读';
					
					$money ['money'] = "支出" . $money ['money'] . "元";
					$mod = $this->gettopic ( $money ['typeid'] );
					$viewurl = SITE_URL . $suffix . urlmap ( 'topic/getone/' . $mod ['id'], 2 );
					$money ['content'] = "您付费阅读了文章：<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
				
					break;
				case 'wxqbuytaocan' :
					$money ['operation'] = '购买套餐';
					$taocan=$this->db->get_where('weixiaoqiang_taocanbuy',array('taocanid'=>$money['typeid']))->row_array();
					$money ['money'] = "用户支付" . $money ['money'] . "元购买套餐";
					$c_name=$taocan['taocanname'];
					$money ['content'] = "购买套餐[$c_name]";
					break;
				case 'payarticle' :
					$money ['operation'] = '用户付费阅读';
					
					$money ['money'] = "收入" . $money ['money'] . "元";

					$mod = $this->gettopic ( $money ['typeid'] );
					$viewurl = SITE_URL . $suffix . urlmap ( 'topic/getone/' . $mod ['id'], 2 );
					$money ['content'] = "用户付费阅读了您的文章：<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					
					break;
				case 'vertify' :
					$money ['operation'] = '用户付费认证专家';
					
					$money ['money'] = "支出" . $money ['money'] . "元";
					
					$money ['content'] = "来自用户付费认证专家";
					break;
				case 'appbuyshouhuo' :
					$money ['operation'] = '应用商店购买商品';
					
					$money ['money'] = "支出" . $money ['money'] . "元";
					
					$money ['content'] = "来自应用商店消费";
					break;
					
	
					
				case 'myviewaid' :
					$money ['operation'] = '我的偷看回答';
					$money ['money'] = "支出" . $money ['money'] . "元";
					$mod = $this->getanswer ( $money ['typeid'] );

					$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['qid'], 2 );
					$money ['content'] = "付费偷看回答的问题:<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";

					break;
				case 'usertixian' :
					$money ['operation'] = '用户提现申请';

					$money ['money'] = "支出" . $money ['money'] . "元";

					$money ['content'] = "来自用户提现申请";
					break;
				case 'thusertixian' :
					$money ['operation'] = '返回用户提现金额';

					$money ['money'] = "收入" . $money ['money'] . "元";

					$money ['content'] = "返回用户提现金额到用户钱包里";
					break;
				case 'chongzhi' :
					$money ['operation'] = '用户充值';

					$money ['money'] = "收入" . $money ['money'] . "元";

					$money ['content'] = "来自用户充值付款";
					break;
					case 'appbuy' :
					$money ['operation'] = '购买应用';
                   $money ['money'] = "支出" . $money ['money'] . "元";
					$money ['content'] = "来自应用商店付款付款";
					break;
				case 'creditchongzhi' :
					$money ['operation'] = '用户财富积分充值';
					$credit2 = $money ['money'] * $this->base->setting ['recharge_rate'];

					$money ['money'] = "获得" . $credit2 . "积分";

					$money ['content'] = "来自用户财富积分充值付款";
					break;
				case 'fufeitiwen' :
					$money ['operation'] = '用户付费提问';
					$mod = $this->getquestion ( $money ['typeid'] );
					$money ['money'] = "支出" . $money ['money'] . "元";
					if ($mod == null) {
						$money ['content'] = "此付费提问的问题被删除，问题qid=" . $money ['typeid'];
					} else {
						$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['id'], 2 );
						$money ['content'] = "付费提问标题-><a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					}

					break;
				case 'aid' :
					$money ['operation'] = '回答打赏';
					$mod = $this->getanswer ( $money ['typeid'] );
					$money ['money'] = "收入" . $money ['money'] . "元";
					$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['qid'], 2 );
					$money ['content'] = "<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					break;
				case 'tid' :
					$money ['operation'] = '文章打赏';
					$mod = $this->gettopic ( $money ['typeid'] );
					$viewurl = SITE_URL . $suffix . urlmap ( 'topic/getone/' . $mod ['id'], 2 );
					$money ['content'] = "<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";

					break;
				case 'wtxuanshang' :
					$money ['operation'] = '提问悬赏';
					$mod = $this->getquestion ( $money ['typeid'] );
					$money ['money'] = "支出" . $money ['money'] . "元";
					if ($mod == null) {
						$money ['content'] = "此悬赏问题被删除，问题qid=" . $money ['typeid'];
					} else {
						$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['id'], 2 );
						$money ['content'] = "悬赏标题-><a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					}
					break;
				case 'adoptqid' :
					$money ['operation'] = '回答被采纳';
					$money ['money'] = "收入" . $money ['money'] . "元";
					$mod = $this->getquestion ( $money ['typeid'] );
					$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['id'], 2 );
					$money ['content'] = "<a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					break;
				case 'thqid' :
					$money ['operation'] = '问题被删除退还悬赏金额';
					$money ['money'] = "收入" . $money ['money'] . "元";

					$money ['content'] = "此删除问题qid=" . $money ['typeid'];
					break;
				case 'theqid' :
					$money ['operation'] = '退还对专家付费提问金额';
					$money ['money'] = "收入" . $money ['money'] . "元";
					$mod = $this->getquestion ( $money ['typeid'] );
					if ($mod == null) {
						$money ['content'] = "此问题被删除，问题qid=" . $money ['typeid'];
					} else {
						$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['id'], 2 );
						$money ['content'] = "标题-><a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					}

					break;
				case 'eqid' :
					$money ['operation'] = '用户对专家提问采纳收入';
					$money ['money'] = "收入" . $money ['money'] . "元";
					$mod = $this->getquestion ( $money ['typeid'] );
					if ($mod == null) {
						$money ['content'] = "此问题被删除，问题qid=" . $money ['typeid'];
					} else {
						$viewurl = SITE_URL . $suffix . urlmap ( 'question/view/' . $mod ['id'], 2 );
						$money ['content'] = "标题-><a href='" . $viewurl . ".html'>" . $mod ['title'] . "</a>";
					}

					break;
			}

			$recargelist [] = $money;
		}
		return $recargelist;
	}
	function getanswer($id) {
		$answer = $this->db->query ( "SELECT * FROM " . $this->db->dbprefix . "answer WHERE id='$id'" )->row_array ();

		if ($answer) {

			$answer ['title'] = checkwordsglobal ( $answer ['title'] );
			$answer ['content'] = checkwordsglobal ( $answer ['content'] );
		}
		return $answer;
	}
	function getmysummoneybytouid($touid) {

		$mrmb = $this->db->query ( "SELECT sum(cash_fee) as rmb FROM " . $this->db->dbprefix . "weixin_notify WHERE touid=$touid and haspay=0 " )->row_array ();
		return $mrmb;

	}
	function gethasmysummoneybytouid($touid) {

		$mrmb = $this->db->query ( "SELECT sum(cash_fee) as rmb FROM " . $this->db->dbprefix . "weixin_notify WHERE touid=$touid and haspay=1 " )->row_array ();
		return $mrmb;

	}
	function getquestion($id) {
		$question = $this->db->query ( "SELECT * FROM " . $this->db->dbprefix . "question WHERE id='$id'" )->row_array ();
		if ($question) {

			$question ['title'] = checkwordsglobal ( $question ['title'] );

		}
		return $question;
	}
	function gettopic($id) {
		$topic = $this->db->query ( "SELECT * FROM " . $this->db->dbprefix . "topic WHERE id='$id'" )->row_array ();

		if ($topic) {

			$topic ['title'] = checkwordsglobal ( $topic ['title'] );

		}
		return $topic;
	}
}