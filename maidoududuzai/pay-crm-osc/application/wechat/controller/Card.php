<?php

namespace app\wechat\controller;

use \think\Db;
use \app\common\WeChat;
use \app\common\WeChatMerchant;

class Card
{

	public function __construct()
	{

	}

	public function get()
	{
		$card_id = input('param.card_id');
		$MchConfig = Db::name('merchant_weixin')->where('card_id', '=', $card_id)->field('merchant_id, appid, appsecret')->find();
		if(empty($MchConfig)) {
			return '无效的参数 [card_id]';
		}
		$MchWeChat = WeChatMerchant::init($MchConfig);
		$sub_openid = input('param.sub_openid');
		if(empty($sub_openid)) {
			$Oauth = $MchWeChat->Oauth();
			$code = input('param.code');
			if(empty($code)) {
				$the_url = url('get', input('param.'), null, true);
				return \befen\jump($Oauth->getOauthRedirect($the_url, null, 'snsapi_base'));
			}
			try {
				$res = $Oauth->getOauthAccessToken();
				$sub_openid = $res['openid'];
			} catch (\Exception $e) {
				echo $e->getMessage();
				exit();
			}
		}
		$card_info = authcode(input('param.card_info'), 'DECODE', $sub_openid);
		$card_info = json_decode($card_info, true);
		if(empty($card_info)) {
			return '无效的请求参数';
		}
		$card_no = $card_info['card_no'];
		$mch_user = model('MchUser')->get_user(['card_no' => $card_no]);
		$Card = $MchWeChat->load('Card');
		if(empty($mch_user['sub_openid'])) {
			model('MchUser')->allowField(true)->save([
				'sub_openid' => $card_info['sub_openid'],
			], ['card_no' => $card_no]);
			try {
				$res = $Card->modifyStock($card_id, 1);
			} catch (\Exception $e) {
				return '修改库存失败,' . $e->getMessage();
			}
		}
		/*
		//核查卡号
		try {
			$res = $Card->checkCode($card_id, [$card_no]);
			if(count($res['exist_code'])) {
				$is_exist = true;
			} else {
				$is_exist = false;
			}
		} catch (\Exception $e) {
			return '核查卡号失败,' . $e->getMessage();
		}
		if(!$is_exist) {
			//导入卡号
			try {
				$res = $Card->deposit($card_id, [$card_no]);
			} catch (\Exception $e) {
				return '导入卡号失败,' . $e->getMessage();
			}
			//修改库存
			try {
				$res = $Card->modifyStock($card_id, 1);
			} catch (\Exception $e) {
				return '修改库存失败,' . $e->getMessage();
			}
		}
		*/
		try {
			$wx_config = $MchWeChat->load('Script')->getJsSign(\befen\get_url(true));
		} catch (\Exception $e) {
			return '获取wx_config失败,' . $e->getMessage();
		}
		try {
			$api_ticket = $MchWeChat->load('Script')->getTicket('wx_card');
		} catch (\Exception $e) {
			return '获取api_ticket失败,' . $e->getMessage();
		}
		$code = $card_no;
		$timestamp = $wx_config['timestamp'];
		$nonce_str = $wx_config['nonceStr'];
		$api_data = [
			$api_ticket,
			$card_id,
			$code,
			$sub_openid,
			$timestamp,
			$nonce_str,
		];
		sort($api_data, SORT_STRING);
		$signature = sha1(implode($api_data));
		include \befen\view('home/weixin_get_card.htm');
	}

	public function open()
	{
		$card_id = input('param.card_id');
		$encrypt_code = input('param.encrypt_code');
		$sub_openid = input('param.openid');
		$activate_ticket = input('param.activate_ticket');
		$MchConfig = Db::name('merchant_weixin')->where('card_id', '=', $card_id)->field('merchant_id, appid, appsecret')->find();
		if(empty($MchConfig)) {
			return '无效的参数 [card_id]';
		}
		$mch_user = model('MchUser')->get_user(['sub_openid' => $sub_openid, 'merchant_id' => $MchConfig['merchant_id']]);
		if(empty($mch_user)) {
			return '无效的参数 [sub_openid]';
		}
		//$MchWeChat = WeChatMerchant::init($MchConfig);
		$UserWeixin = new \app\common\UserWeixin($MchConfig);
		$Card = $UserWeixin->MchWeChat->load('Card');
		//获取会员信息
		$res = $UserWeixin->query_card_activateform($activate_ticket);
		if($res['status'] == 0) {
			return $res['message'];
		}
		$result = $res['contents'];
		$form_info = $result['info']['common_field_list'];
		$user_data = [];
		foreach($form_info as $key => $val) {
			switch($val['name']) {
				case 'USER_FORM_INFO_FLAG_NAME':
					$user_data['username'] = $val['value'];
				break;
				case 'USER_FORM_INFO_FLAG_SEX':
					$user_data['sex'] = '0';
					switch($val['value']) {
						case '男':
							$user_data['sex'] = '1';
						break;
						case '女':
							$user_data['sex'] = '2';
						break;
					}
				break;
				case 'USER_FORM_INFO_FLAG_MOBILE':
					$user_data['phone'] = $val['value'];
				break;
				case 'USER_FORM_INFO_FLAG_BIRTHDAY':
					$user_data['birthday'] = $val['value'];
				break;
			}
		}
		//检查手机号码
		if(empty($user_data['phone'])) {
			return '手机号码必填';
		}
		if(0 != Db::name('mch_user')->where('merchant_id', '=', $merchant_id)->where('phone', '=', $user_data['phone'])->where('sub_openid', '<>', '')->count()) {
			return '手机号码已经存在';
		}
		//合并会员数据
		if(empty($mch_user['phone'])) {

		}
		//会员卡激活
		$res = $UserWeixin->card_open($card_id, $mch_user['card_no'], $mch_user['credit'], $mch_user['balance']);
		if($res['status'] == 0) {
			return $res['message'];
		}
		$user_data['UserCardCode'] = $mch_user['card_no'];
		model('MchUser')->allowField(true)->save($user_data, ['id' => $mch_user['id']]);
		try {
			$wx_config = $UserWeixin->MchWeChat->load('Script')->getJsSign(\befen\get_url(true));
		} catch (\Exception $e) {
			return '获取wx_config失败,' . $e->getMessage();
		}
		include \befen\view('home/weixin_open_card.htm');
	}

	public function user_info()
	{

		//wechat!

		//user_info

		//include \befen\view('home/weixin_user_info.htm');

	}

}

