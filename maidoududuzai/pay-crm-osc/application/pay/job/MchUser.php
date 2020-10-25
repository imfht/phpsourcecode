<?php

namespace app\pay\job;

use \think\Db;
use \think\queue\Job;

class MchUser
{

	public function fire(Job $job, $data = [])
	{

	}

	//更新信息
	public function card(Job $job, $data)
	{
		echo Tool::show($data);
		if(empty($data['mch_uid']) || empty($data['merchant_id'])) {
			$job->delete();
		} else {
			$mch_user = Db::name('mch_user')->where('id', '=', $data['mch_uid'])->where('merchant_id', '=', $data['merchant_id'])->find();
			if(!$mch_user) {
				$job->delete();
			} else {
				if(!$mch_user['biz_card_no'] && !$mch_user['UserCardCode']) {
					$job->delete();
				} else {
					//alipay
					if($mch_user['biz_card_no']) {
						$UserAlipay = new \app\common\UserAlipay($data['merchant_id']);
						$res = $UserAlipay->card_update($mch_user['biz_card_no'], 'BIZ_CARD', '', $mch_user['credit'], $mch_user['balance']);
						echo Tool::show($res);
						if($res['status'] == 1) {
							$job->delete();
						}
					}
					//weixin
					if($mch_user['UserCardCode']) {
						$UserWeixin = new \app\common\UserWeixin($data['merchant_id']);
						$res = $UserWeixin->card_update($UserWeixin->card_id, $mch_user['UserCardCode'], $mch_user['credit'], $mch_user['balance']);
						echo Tool::show($res);
						if($res['status'] == 1) {
							$job->delete();
						}
					}
				}
			}
		}
	}

}

