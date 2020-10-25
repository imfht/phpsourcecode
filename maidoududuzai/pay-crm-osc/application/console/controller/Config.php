<?php

namespace app\console\controller;

use \think\Db;
use \app\common\Pay;

class Config
{

	public $admin;

	public function __construct()
	{
		$this->admin = model('Admin')->checkLoginAdmin();
	}

	public function index()
	{
		$sys_cfg = model('Config')->list(null, true);
		include \befen\view();
	}

	public function agent()
	{
		if(request()->isPost()) {
			if(model('Trade')->count()) {
				return make_json(0, '系统运行后不支持设置此项');
			}
			$level_id = input('post.level_id/a');
			$level_name = input('post.level_name/a');
			$join_cost = input('post.join_cost/a');
			$join_rates = input('post.join_rates/a');
			$trade_rates = input('post.trade_rates/a');
			foreach($level_id as $key => $val) {
				Db::name('agent_level')->where('level_id', '=', $val)->update([
					'level_name'  => input('post.level_name/a')[$key],
					'join_cost' => input('post.join_cost/a')[$key],
					'join_rates' => input('post.join_rates/a')[$key],
					'trade_rates' => input('post.trade_rates/a')[$key],
				]);
			}
			return make_json(1, '操作成功');
		}
		$list = model('AgentLevel')->get_all();
		include \befen\view();
	}

	public function notify()
	{
		if(request()->isPost()) {
			$post = input('post.');
			foreach($post as $key => $value) {
				if(preg_match('/^(sms_|mail_)/', $key)) {
					Db::name('config')->where('key', '=', $key)->update(['value' => $value]);
				}
			}
			model('Config')->list(null, true);
			return make_json(1, '操作成功');
		}
		$sys_cfg = model('Config')->list(null, true);
		include \befen\view();
	}

	public function other()
	{
		if(request()->isPost()) {
			$post = input('post.');
			foreach($post as $key => $value) {
				if(in_array($key, ['aliyun_appcode', 'weixin_iot_appid', 'weixin_iot_appsecret'])) {
					Db::name('config')->where('key', '=', $key)->update(['value' => $value]);
				}
			}
			model('Config')->list(null, true);
			return make_json(1, '操作成功');
		}
		$sys_cfg = model('Config')->list(null, true);
		include \befen\view();
	}

	public function alipay_template()
	{
		if(request()->isPost()) {
			$post = input('post.');
			foreach($post as $key => $value) {
				Db::name('config')->where('key', '=', $key)->update(['value' => $value]);
			}
			model('Config')->list(null, true);
			return make_json(1, '操作成功');
		}
		$sys_cfg = model('Config')->list(null, true);
		include \befen\view();
	}

	public function weixin_template()
	{
		if(request()->isPost()) {
			$post = input('post.');
			foreach($post as $key => $value) {
				Db::name('config')->where('key', '=', $key)->update(['value' => $value]);
			}
			model('Config')->list(null, true);
			return make_json(1, '操作成功');
		}
		$sys_cfg = model('Config')->list(null, true);
		include \befen\view();
	}

	public function set_weixin_industry()
	{
		$WeChat = \app\common\WeChatConsole::init();
		$Template = $WeChat->load('Template');
		try {
			$res = $Template->getIndustry();
		} catch (\Exception $e) {
			return $e->getMessage();
		}
		if($res['primary_industry']['first_class'] != 'IT科技' || $res['primary_industry']['second_class'] != '互联网|电子商务' || $res['secondary_industry']['first_class'] != '餐饮' || $res['secondary_industry']['second_class'] != '餐饮') {
			try {
				$res = $Template->setIndustry(1, 10);
			} catch (\Exception $e) {
				return $e->getMessage();
			}
		}
		return $Template;
	}

	public function add_weixin_trade_success()
	{
		$Template = $this->set_weixin_industry();
		if(is_string($Template)) {
			return make_json(0, $Template);
		}
		try {
			$res = $Template->addTemplate('OPENTM418147123');
			Db::name('config')->where('key', '=', 'weixin_trade_success')->update(['value' => $res['template_id']]);
			return make_json(1, 'ok', $res);
		} catch (\Exception $e) {
			return make_json(0, $e->getMessage());
		}
	}

	public function add_weixin_refund_success()
	{
		$Template = $this->set_weixin_industry();
		if(is_string($Template)) {
			return make_json(0, $Template);
		}
		try {
			$res = $Template->addTemplate('OPENTM418145263');
			Db::name('config')->where('key', '=', 'weixin_refund_success')->update(['value' => $res['template_id']]);
			return make_json(1, 'ok', $res);
		} catch (\Exception $e) {
			return make_json(0, $e->getMessage());
		}
	}

}

