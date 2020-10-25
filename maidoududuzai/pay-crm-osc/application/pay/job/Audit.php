<?php

namespace app\pay\job;

use \think\Db;
use \think\queue\Job;

class Audit
{

	//签约状态
	public $OrderStatus = [
		'MERCHANT_INFO_HOLD' => '暂存',
		'MERCHANT_AUDITING' => '审核中',
		'MERCHANT_CONFIRM' => '待商户确认',
		'MERCHANT_CONFIRM_SUCCESS' => '商户确认成功',
		'MERCHANT_CONFIRM_TIME_OUT' => '商户超时未确认',
		'MERCHANT_APPLY_ORDER_CANCELED' => '审核失败或商户拒绝',
	];

	//签约状态
	public $ApplymentState = [
		'APPLYMENT_STATE_EDITTING' => '编辑中',
		'APPLYMENT_STATE_AUDITING' => '审核中',
		'APPLYMENT_STATE_REJECTED' => '已驳回',
		'APPLYMENT_STATE_TO_BE_CONFIRMED' => '待账户验证',
		'APPLYMENT_STATE_TO_BE_SIGNED' => '待签约',
		'APPLYMENT_STATE_SIGNING' => '开通权限中',
		'APPLYMENT_STATE_FINISHED' => '已完成',
		'APPLYMENT_STATE_CANCELED' => '已作废',
	];

	public function fire(Job $job, $data = [])
	{

	}

	public function alipay(Job $job, $data = [])
	{
		echo Tool::show($data);
		$batch_no = $data['batch_no'];
		$value = Db::name('gates')->where('batch_no', '=', $batch_no)->find();
		if(empty($value)) {
			$job->delete();
		} else {
			$AopSdk = new \app\common\AopSdk();
			$AopSdk->load('alipay.open.agent.order.query');
			$res = $AopSdk->execute([
				'batch_no' => $batch_no,
			]);
			echo Tool::show($res);
			if($res['status'] == 1 && !empty($res['contents']['order_status'])) {
				if($value['order_status'] != $res['contents']['order_status']) {
					$fields = [
						'merchant_pid' => $res['contents']['merchant_pid'],
						'order_status' => $res['contents']['order_status'],
						'time_update' => _time(),
					];
					$reject_reason = !empty($res['contents']['reject_reason']) ? $res['contents']['reject_reason'] : '';
					Db::name('gates_apply')->insert([
						'gates_id' => $value['id'],
						'gates_msg' => $this->OrderStatus[$res['contents']['order_status']],
						'dateline' => $fields['time_update'],
						'batch_no' => $batch_no,
						'reject_reason' => $reject_reason,
					]);
					Db::name('gates')->where('batch_no', '=', $batch_no)->update($fields);
				}
				if(in_array($res['contents']['order_status'], ['MERCHANT_INFO_HOLD', 'MERCHANT_CONFIRM_SUCCESS', 'MERCHANT_CONFIRM_TIME_OUT', 'MERCHANT_APPLY_ORDER_CANCELED'])) {
					$job->delete();
				}
			}
		}
	}

	public function weixin(Job $job, $data = [])
	{
		echo Tool::show($data);
		$applyment_id = $data['applyment_id'];
		$business_code = $data['business_code'];
		$value = Db::name('gates')->where('business_code', '=', $business_code)->find();
		if(empty($value)) {
			$job->delete();
		} else {
			$TenWePay = new \app\common\TenWePay();
			$client = $TenWePay->client(true);
			$res = $client->request('GET', 'https://api.mch.weixin.qq.com/v3/applyment4sub/applyment/business_code/' . $business_code, [
				'headers' => [
					'User-Agent' => 'PAY-CRM-CLIENT',
					'Accept' => 'application/json',
					'Content-Type' => 'application/json',
					'Wechatpay-Serial' => $TenWePay->wechatpaySerialNumber,
				],
			]);
			$result = json_decode($res->getBody()->getContents(), true, 512, JSON_BIGINT_AS_STRING);
			echo Tool::show($result);
			if($value['applyment_state'] != $result['applyment_state']) {
				$sub_mch_id = !empty($result['sub_mchid']) ? $result['sub_mchid'] : '';
				$sign_url = !empty($result['sign_url']) ? $result['sign_url'] : '';
				$gates_detail = JSON([
					'sub_mch_id' => $sub_mch_id,
					'sign_url' => $sign_url,
				]);
				$fields = [
					'applyment_state' => $result['applyment_state'],
					'gates_detail' => $gates_detail,
					'time_update' => _time(),
				];
				$reject_reason = [];
				if(!empty($result['audit_detail'])) {
					foreach($result['audit_detail'] as $key => $val) {
						if(!empty($val['reject_reason'])) {
							$reject_reason[] = $val['reject_reason'];
						}
					}
				}
				Db::name('gates_apply')->insert([
					'gates_id' => $value['id'],
					'gates_msg' => $this->ApplymentState[$result['applyment_state']],
					'dateline' => $fields['time_update'],
					'applyment_id' => $applyment_id,
					'reject_reason' => JSON($reject_reason),
				]);
				Db::name('gates')->where('business_code', '=', $business_code)->update($fields);
			}
			if(in_array($result['applyment_state'], ['APPLYMENT_STATE_EDITTING', 'APPLYMENT_STATE_REJECTED', 'APPLYMENT_STATE_FINISHED', 'APPLYMENT_STATE_CANCELED'])) {
				$job->delete();
			}
		}
	}

}

