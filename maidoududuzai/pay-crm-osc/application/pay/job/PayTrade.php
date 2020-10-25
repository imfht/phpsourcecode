<?php

namespace app\pay\job;

use \think\Db;
use \think\queue\Job;

use \app\common\Pay;
use \app\common\PayAction;
use \app\pay\controller\Index;

class PayTrade
{

	public function fire(Job $job, $data = [])
	{

	}

	// 查询交易状态
	public function query(Job $job, $data)
	{
		echo Tool::show($data);
		$trade = Db::name('trade')->where('out_trade_no', '=', $data['out_trade_no'])->find();
		if(!$trade) {
			$job->delete();
		} else {
			$run_time = _time() - $trade['time_create'];
			$later = 7 * 24 * 60 * 60;
			if($run_time <= 60) {
				if(!empty($trade['qrcode_id'])) {
					// Sleep
					if($job->attempts() == 1) {
						sleep(3);
					}
					$merchant = Pay::merchant($trade['merchant_id']);
					$res = Index::query($merchant, $data['out_trade_no']);
					echo Tool::show($res);
					if(empty($res['contents'])) {
						if($job->attempts() < 6) {
							$job->release(1);
						} else {
							$job->release(10);
						}
					} else {
						if(in_array($res['message'], ['SUCCESS', 'TRADE_SUCCESS'])) {
							if(!empty($trade['qrcode_id'])) {
								$openid = Db::name('store_person')->where('person_id', '=', $trade['person_id'])->value('openid');
								if($openid) {
									\think\Queue::push('\app\pay\job\Template@weixin', ['out_trade_no' => $data['out_trade_no']]);
								}
								$device_id = Db::name('qrcode')->where('id', '=', $trade['qrcode_id'])->value('device_id');
								if($device_id) {
									\think\Queue::push('\app\pay\job\Iot@qrcode_speech', [
										'device_id' => $device_id,
										'text' => '收款成功',
										'amount' => $trade['total_amount'],
									]);
								}
							}
							\think\Queue::later($later, '\app\pay\job\PayTrade@profit', ['out_trade_no' => $data['out_trade_no']]);
							$job->delete();
						} else {
							if($job->attempts() < 6) {
								$job->release(1);
							} else {
								$job->release(10);
							}
						}
					}
				}
			} else {
				if(empty($trade['trade_gate']) && 0 == number($trade['total_amount'])) {
					$this->trade_delete($data['out_trade_no']);
					$this->order_delete($data['out_trade_no']);
					$job->delete();
				} else {
					if(in_array($trade['trade_status'], ['SUCCESS', 'TRADE_SUCCESS'])) {
						\think\Queue::later($later, '\app\pay\job\PayTrade@profit', ['out_trade_no' => $data['out_trade_no']]);
						$job->delete();
					} else {
						$merchant = Pay::merchant($trade['merchant_id']);
						$res = Index::query($merchant, $data['out_trade_no']);
						echo Tool::show($res);
						if(!empty($res['contents'])) {
							if(in_array($res['message'], ['SUCCESS', 'TRADE_SUCCESS'])) {
								\think\Queue::later($later, '\app\pay\job\PayTrade@profit', ['out_trade_no' => $data['out_trade_no']]);
							} else {
								\think\Queue::later($later, '\app\pay\job\PayTrade@db_clean', ['out_trade_no' => $data['out_trade_no']]);
							}
							$job->delete();
						}
					}
				}
			}
		}
	}

	// 查询退款状态
	public function refund(Job $job, $data)
	{
		echo Tool::show($data);
		$trade = Db::name('trade')->where('out_trade_no', '=', $data['out_trade_no'])->find();
		if(!$trade) {
			$job->delete();
		} else {
			$merchant = Pay::merchant($trade['merchant_id']);
			$refund = Db::name('refund')
				->alias('r')
				->join('trade t', 't.out_trade_no = r.out_trade_no', 'LEFT')
				->where('r.out_refund_no', '=', $data['out_refund_no'])
				->field('r.refund_status')
				->find();
			if(!$refund) {
				$res = Index::query_refund($merchant, $data['out_trade_no'], $data['out_refund_no']);
				echo Tool::show($res);
				if(!empty($res['contents'])) {
					$job->delete();
				}
			} else {
				if($refund['refund_status'] == 1) {
					$job->delete();
				} else {
					$res = Index::query_refund($merchant, $data['out_trade_no'], $data['out_refund_no']);
					echo Tool::show($res);
					if(!empty($res['contents'])) {
						if($res['status'] == 1) {
							$refund_status = Db::name('refund')->where('out_refund_no', '=', $data['out_refund_no'])->value('refund_status');
							if($refund_status != -1) {
								$job->delete();
							}
						} else {
							$job->delete();
						}
					}
				}
			}
		}
	}

	// 计算交易分润
	public function profit(Job $job, $data)
	{
		echo Tool::show($data);
		if(!empty($data['out_trade_no'])) {
			Pay::trade_profit($data['out_trade_no']);
		}
		$job->delete();
	}

	// 清理无效数据
	public function db_clean(Job $job, $data)
	{
		echo Tool::show($data);
		if(!empty($data['out_trade_no'])) {
			$this->trade_delete($data['out_trade_no']);
			$this->order_delete($data['out_trade_no']);
		}
		$job->delete();
	}

	// 删除交易记录
	public function trade_delete($out_trade_no)
	{
		model('\app\common\model\Trade')->_delete($out_trade_no);
	}

	// 删除订单记录
	public function order_delete($out_trade_no)
	{
		$order = Db::name('order')->where('out_trade_no', '=', $out_trade_no)->find();
		if($order) {
			Db::name('order')->where('order_id', '=', $order['order_id'])->delete();
			Db::name('order_detail')->where('order_id', '=', $order['order_id'])->delete();
		}
	}

}

