<?php

namespace app\mo\controller;

use \think\Db;
use \think\Session;

class Index
{
	public $agent;
	
	public function __construct()
	{
		if(!model('Agent')->getLoginAgent() && preg_match('/MicroMessenger/', input('server.HTTP_USER_AGENT'))) {
			$WeChat = \app\common\WeChatConsole::init();
			$Oauth = $WeChat->Oauth();
			$code = input('param.code');
			if(empty($code)) {
				$the_url = url('/mo', null, null, true);
				return \befen\redirect($Oauth->getOauthRedirect($the_url, null, 'snsapi_base'));
			} else {
				try {
					$res = $Oauth->getOauthAccessToken();
				} catch (\Exception $e) {
					$res = null;
				}
				if(!empty($res['openid'])) {
					$openid = $res['openid'];
					try {
						$User = $WeChat->load('User');
						$UserInfo = $User->getUserInfo($openid);
					} catch (\Exception $e) {
						return $e->getMessage();
					}
					if($UserInfo['subscribe']) {
						if(0 == Db::name('wx_user')->where('openid', '=', $openid)->count()) {
							Db::name('wx_user')->insert([
								'openid' => $UserInfo['openid'],
								'unionid' => $UserInfo['unionid'] ?? '',
								'nickname' => $UserInfo['nickname'],
								'sex' => $UserInfo['sex'],
								'headimgurl' => $UserInfo['headimgurl'],
								'subscribe' => 1,
								'subscribe_time' => $UserInfo['subscribe_time'],
								'groupid' => $UserInfo['groupid'],
							]);
						}
					}
					$this->agent = Db::name('agent')->where('agent_status', '=', 1)->where('openid', '=', $openid)->find();
					if($this->agent) {
						Session::set('agent', $this->agent);
					}
				}
			}
		}
		$this->agent = model('Agent')->checkLoginAgent();
	}

	public function index()
	{
		//$beg_time = gstime('Sunday -6 day', _time());
		//$end_time = $beg_time + 60 * 60 * 24 * 7;
		$end_time = gstime(gsdate('Y-m-d')) + 60 * 60 * 24;
		$beg_time = $end_time - 60 * 60 * 24 * 7;
		$where = [];
		$where['trade_status'] = ['IN', ['SUCCESS', 'TRADE_SUCCESS']];
		$where['time_create'] = ['BETWEEN', [$beg_time, $end_time]];
		$list = Db::name('trade')
			->where($where)
			->where('agent_id', '=', $this->agent['agent_id'])
			->field('trade_gate, total_amount, time_create')
			->select();
		$_week = [];
		$cur_time = $beg_time;
		for($i=0; $i<7; $i++) {
			$_week[] = gsdate('m-d', $cur_time);
			$cur_time += 60 * 60 * 24;
		}
		$_count = [
			'today' => 0,
			'alipay' => 0,
			'weixin' => 0,
		];
		$_amount = [
			'today' => 0,
			'alipay' => 0,
			'weixin' => 0,
		];
		$_week_count = [
			'alipay' => [],
			'weixin' => [],
		];
		$_week_amount = [
			'alipay' => [],
			'weixin' => [],
		];
		foreach($list as $val) {
			if(gsdate('m-d') == gsdate('m-d', $val['time_create'])) {
				$_count['today']++;
				$_amount['today'] += $val['total_amount'];
				$_amount[$val['trade_gate']] += $val['total_amount'];
			}
			foreach($_week as $_date) {
				if(!isset($_week_count[$val['trade_gate']][$_date]) || !isset($_week_amount[$val['trade_gate']][$_date])) {
					$_week_count[$val['trade_gate']][$_date] = 0;
					$_week_amount[$val['trade_gate']][$_date] = 0;
				}
				if($_date == gsdate('m-d', $val['time_create'])) {
					$_week_count[$val['trade_gate']][$_date]++;
					$_week_amount[$val['trade_gate']][$_date] += $val['total_amount'];
				}
			}
		}
		$_week = json_encode($_week);
		$_week_count['alipay'] = json_encode(array_values($_week_count['alipay']));
		$_week_count['weixin'] = json_encode(array_values($_week_count['weixin']));
		foreach($_week_amount['alipay'] as $key => $val) {
			$_week_amount['alipay'][$key] = number($val);
		}
		foreach($_week_amount['weixin'] as $key => $val) {
			$_week_amount['weixin'][$key] = number($val);
		}
		$_week_amount['alipay'] = json_encode(array_values($_week_amount['alipay']));
		$_week_amount['weixin'] = json_encode(array_values($_week_amount['weixin']));
		include \befen\view();
	}

}

