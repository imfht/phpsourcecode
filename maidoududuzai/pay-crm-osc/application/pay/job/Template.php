<?php

namespace app\pay\job;

use \think\Db;
use \think\queue\Job;

class Template
{

	public function fire(Job $job, $data = [])
	{

	}

	public function alipay(Job $job, $data = [])
	{
		//AlipayTemplate
	}

	public function weixin(Job $job, $data = [])
	{
		echo Tool::show($data);
		$WeChat = \app\common\WeChatConsole::init();
		$Template = $WeChat->load('Template');
		if(isset($data['out_trade_no'])) {
			$trade = Db::name('trade')
				->alias('t')
				->join('merchant m', 't.merchant_id = m.merchant_id')
				->join('store_person sp', 't.person_id = sp.person_id')
				->where('t.out_trade_no', '=', $data['out_trade_no'])
				->field('m.merchant_name, sp.openid, t.trade_gate, t.trade_type, t.out_trade_no, t.total_amount, t.time_create')
				->find();
			$template_id = model('\app\common\model\Config')->config('weixin_trade_success', null, true);
			if($template_id && !empty($trade['openid'])) {
				$template_info = [
					//'url' => 'add a link for message template',
					'first' => '您好，您有一笔成功的收款',
					'keyword1' => $trade['merchant_name'],
					'keyword2' => model('\app\common\model\Trade')->getGate($trade['trade_gate']) . '(' . model('\app\common\model\Trade')->get_type($trade['trade_type']) . ')',
					'keyword3' => $trade['total_amount'],
					'keyword4' => $trade['out_trade_no'],
					'keyword5' => gsdate('Y-m-d H:i:s', $trade['time_create']),
					'remark' => '如有疑问，请联系我们！',
				];
				$template_info = $WeChat->make_template($trade['openid'], $template_id, $template_info);
				try {
					$res = $Template->send($template_info);
					echo Tool::show($res);
				} catch (\Exception $e) {
					echo Tool::show($e->getMessage());
				}
			}
			$job->delete();
		}
		if(isset($data['out_refund_no'])) {
			$refund = Db::name('refund')
				->alias('r')
				->join('merchant m', 'r.merchant_id = m.merchant_id')
				->join('store_person sp', 'r.person_id = sp.person_id')
				->where('r.out_refund_no', '=', $data['out_refund_no'])
				->field('m.merchant_name, sp.openid, r.out_trade_no, r.out_refund_no, r.refund_amount, r.time_create')
				->find();
			$template_id = model('\app\common\model\Config')->config('weixin_refund_success', null, true);
			if($template_id && !empty($refund['openid'])) {
				$template_info = [
					//'url' => 'add a link for message template',
					'first' => '您好，您有一笔成功的退款',
					'keyword1' => $refund['merchant_name'],
					'keyword2' => $refund['refund_amount'],
					'keyword3' => $refund['out_trade_no'],
					'keyword4' => $refund['out_refund_no'],
					'keyword5' => gsdate('Y-m-d H:i:s', $refund['time_create']),
					'remark' => '如有疑问，请联系我们！',
				];
				$template_info = $WeChat->make_template($refund['openid'], $template_id, $template_info);
				try {
					$res = $Template->send($template_info);
					echo Tool::show($res);
				} catch (\Exception $e) {
					echo Tool::show($e->getMessage());
				}
			}
			$job->delete();
		}
	}

}

