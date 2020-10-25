<?php

namespace app\pay\controller;

use \think\Db;
use \app\common\Pay;
use \app\common\PayAction;

class User extends Auth
{

	public $merchant = [];

	public $mch_user = [];

	public $open_user_id = null;

	public function __construct()
	{

		parent::__construct();

	}

	/**
	 * 获取微信会员卡投放链接
	 * @param string $card_id 必须
	 * @param string $out_string 可选
	 * @return json $apply_card_url
	 */
	public function weixin_card_activateurl($card_id = '', $out_string = '')
	{
		//PayAction::log(request()->method() . ' ' . request()->url());
		//PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		if(empty($card_id)) {
			return make_json(0, '缺少参数 [card_id]');
		}
		$out_info = json_decode($out_string, true);
		if(empty($out_info['unionid'])) {
			return make_json(0, '缺少外部参数 [unionid]');
		}
		if(empty($out_info['mini_openid'])) {
			return make_json(0, '缺少外部参数 [mini_openid]');
		}
		$merchant_id = Db::name('merchant_weixin')->where('card_id', '=', $card_id)->value('merchant_id');
		if(empty($merchant_id)) {
			return make_json(0, '无效的参数 [card_id]');
		}
		$mch_user = model('MchUser')->get_user(['mini_openid' => $out_info['mini_openid'], 'merchant_id' => $merchant_id]);
		if(empty($mch_user)) {
			return make_json(0, '无效的外部参数 [mini_openid]');
		}
		$param = [
			'unionid' => $out_info['unionid'],
			'mini_openid' => $out_info['mini_openid'],
		];
		$short_url = model('Url')->make(url('/pay/user/weixin_card_activatelink', ['card_id' => $card_id, 'out_string' => JSON($param)], null, true));
		return make_json(1, 'ok', [
			'apply_card_url' => $short_url['contents']['url']
		]);
	}

	/**
	 * 获取微信会员卡领卡链接
	 * @param string $card_id 必须
	 * @param string $out_string 可选
	 * @return json $apply_card_url
	 */
	public function weixin_card_activatelink($card_id = '', $out_string = '')
	{
		//PayAction::log(request()->method() . ' ' . request()->url());
		//PayAction::log(input('param.'));
		if(empty($card_id)) {
			return '缺少参数 [card_id]';
		}
		$out_info = json_decode($out_string, true);
		if(empty($out_info['unionid'])) {
			return make_json(0, '缺少外部参数 [unionid]');
		}
		if(empty($out_info['mini_openid'])) {
			return make_json(0, '缺少外部参数 [mini_openid]');
		}
		$merchant_id = Db::name('merchant_weixin')->where('card_id', '=', $card_id)->value('merchant_id');
		if(empty($merchant_id)) {
			return '无效的参数 [card_id]';
		}
		$mch_user = model('MchUser')->get_user(['mini_openid' => $out_info['mini_openid'], 'merchant_id' => $merchant_id]);
		if(empty($mch_user)) {
			return '无效的外部参数 [mini_openid]';
		}
		if(!empty($unionid) && !empty($out_info['unionid']) && ($unionid != $out_info['unionid'])) {
			return '用户unionid验证失败！';
		}
		if(empty($out_info['sub_openid'])) {
			$MchConfig = Db::name('merchant_weixin')->where('card_id', '=', $card_id)->field('merchant_id, appid, appsecret')->find();
			$MchWeChat = \app\common\WeChatMerchant::init($MchConfig);
			$Oauth = $MchWeChat->Oauth();
			$code = input('param.code');
			if(empty($code)) {
				$the_url = url('weixin_card_activatelink', ['card_id' => $card_id, 'out_string' => JSON($out_info)], null, true);
				return \befen\jump($Oauth->getOauthRedirect($the_url, null, 'snsapi_base'));
			}
			try {
				$res = $Oauth->getOauthAccessToken();
				$out_info['sub_openid'] = $res['openid'];
			} catch (\Exception $e) {
				echo $e->getMessage();
				exit();
			}
		}
		$card_info = [
			'card_no' => $mch_user['card_no'],
			'unionid' => $out_info['unionid'],
			'sub_openid' => $out_info['sub_openid'],
			'mini_openid' => $out_info['mini_openid'],
		];
		return \befen\jump(url('/wechat/card/get', ['card_id' => $card_id, 'card_info' => authcode(JSON($card_info), 'ENCODE', $out_info['sub_openid'])]));
	}

	/**
	 * 微信开通会员
	 * @param String $open_user_id 用户唯一标识
	 */
	public function weixin_card_open($open_user_id = null)
	{
		//PayAction::log(request()->method() . ' ' . request()->url());
		//PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$code = input('post.code');
		$iv = input('post.iv');
		$encryptedData = input('post.encryptedData');
		if(empty($code)) {
			return make_json(0, '缺少参数 [code]');
		}
		if(empty($iv)) {
			return make_json(0, '缺少参数 [iv]');
		}
		if(empty($encryptedData)) {
			return make_json(0, '缺少参数 [encryptedData]');
		}
		try {
			$mini_config = model('Config')->config(null, 'weixin_iot_');
			$WeMini = \app\common\WeMiniConsole::init([
				'appid' => $mini_config['weixin_iot_appid'],
				'appsecret' => $mini_config['weixin_iot_appsecret'],
			]);
			$res = $WeMini->load('Crypt')->userInfo($code, $iv, $encryptedData);
			$unionid = !empty($res['unionId']) ? $res['unionId'] : '';
			$mini_openid = $res['openId'];
			$mch_user = model('MchUser')->get_user(['mini_openid' => $mini_openid, 'merchant_id' => $this->merchant['merchant_id']]);
			$card_no = model('MchUser')->make_card();
			if($mch_user) {
				$user_data = [];
				if(empty($mch_user['card_no'])) {
					$mch_user['card_no'] = $card_no;
					$user_data['card_no'] = $card_no;
				}
				if(empty($mch_user['unionid'])) {
					$mch_user['unionid'] = $unionid;
					$user_data['unionid'] = $unionid;
				}
				if(!empty($user_data)) {
					model('MchUser')->allowField(true)->save($user_data, ['id' => $mch_user['id']]);
				}
				return make_json(1, 'ok', $mch_user);
			} else {
				model('MchUser')->allowField(true)->save([
					'merchant_id' => $this->merchant['merchant_id'],
					'card_no' => $card_no,
					'unionid' => $unionid,
					'mini_openid' => $mini_openid,
					'credit' => '0.00',
					'balance' => '0.00',
					'open_datetime' => _time()
				]);
				$mch_user = model('MchUser')->get_user(['card_no' => $card_no, 'merchant_id' => $this->merchant['merchant_id']]);
				return make_json(1, 'ok', $mch_user);
			}
		} catch (\Exception $e) {
			return make_json(0, $e->getMessage());
		}
	}

	/**
	 * 获取微信小程序[openid]
	 * @param String $code
	 */
	public function weixin_get_openid($code = '')
	{
		//PayAction::log(request()->method() . ' ' . request()->url());
		//PayAction::log(input('post.'));
		try {
			$mini_config = model('Config')->config(null, 'weixin_iot_');
			$WeMini = \app\common\WeMiniConsole::init([
				'appid' => $mini_config['weixin_iot_appid'],
				'appsecret' => $mini_config['weixin_iot_appsecret'],
			]);
			$res = $WeMini->load('Crypt')->session($code);
			return make_json(1, 'ok', $res);
		} catch (\Exception $e) {
			return make_json(0, $e->getMessage());
		}
	}

	/**
	 * 微信获取会员信息
	 * @param String $open_user_id 用户唯一标识
	 */
	public function weixin_card_userinfo($open_user_id = null)
	{
		//PayAction::log(request()->method() . ' ' . request()->url());
		//PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$card_no = input('post.card_no/s');
		$mini_openid = input('post.mini_openid');
		if(!empty($card_no) || !empty($mini_openid)) {
			if(!empty($card_no)) {
				$mch_user = model('MchUser')->get_user(['card_no' => $card_no, 'merchant_id' => $this->merchant['merchant_id']]);
			} else {
				$mch_user = model('MchUser')->get_user(['mini_openid' => $mini_openid, 'merchant_id' => $this->merchant['merchant_id']]);
			}
			if(empty($mch_user)) {
				return make_json(0, '未获取到会员信息');
			} else {
				return make_json(1, 'ok', $mch_user);
			}
		} else {
			$code = input('post.code');
			$iv = input('post.iv');
			$encryptedData = input('post.encryptedData');
			if(empty($code)) {
				return make_json(0, '缺少参数 [code]');
			}
			if(empty($iv)) {
				return make_json(0, '缺少参数 [iv]');
			}
			if(empty($encryptedData)) {
				return make_json(0, '缺少参数 [encryptedData]');
			}
			try {
				$mini_config = model('Config')->config(null, 'weixin_iot_');
				$WeMini = \app\common\WeMiniConsole::init([
					'appid' => $mini_config['weixin_iot_appid'],
					'appsecret' => $mini_config['weixin_iot_appsecret'],
				]);
				$res = $WeMini->load('Crypt')->userInfo($code, $iv, $encryptedData);
				$unionid = !empty($res['unionId']) ? $res['unionId'] : '';
				$mini_openid = $res['openId'];
				$mch_user = model('MchUser')->get_user(['mini_openid' => $mini_openid, 'merchant_id' => $this->merchant['merchant_id']]);
				if(empty($mch_user)) {
					return make_json(0, '未获取到会员信息');
				} else {
					return make_json(1, 'ok', $mch_user);
				}
			} catch (\Exception $e) {
				return make_json(0, $e->getMessage());
			}
		}
	}

	/**
	 * 获取支付宝会员卡投放链接
	 * @param string $template_id 必须
	 * @param string $out_string 可选
	 * @return json $apply_card_url
	 */
	public function alipay_card_activateurl($template_id = '', $out_string = '')
	{
		//PayAction::log(request()->method() . ' ' . request()->url());
		//PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		if(empty($template_id)) {
			return make_json(0, '缺少参数 [template_id]');
		}
		$out_info = json_decode($out_string, true);
		if(empty($out_info['user_id'])) {
			return make_json(0, '缺少外部参数 [user_id]');
		}
		$merchant_id = Db::name('merchant_alipay')->where('card_id', '=', $template_id)->value('merchant_id');
		if(empty($merchant_id)) {
			return make_json(0, '无效的参数 [template_id]');
		}
		$mch_user = model('MchUser')->get_user(['user_id' => $out_info['user_id'], 'merchant_id' => $merchant_id]);
		if(empty($mch_user)) {
			return make_json(0, '无效的外部参数 [user_id]');
		}
		$short_url = model('Url')->make(url('/pay/user/alipay_card_activatelink', ['template_id' => $template_id, 'out_string' => $out_string], null, true));
		return make_json(1, 'ok', [
			'apply_card_url' => $short_url['contents']['url']
		]);
	}

	/**
	 * 获取支付宝会员卡领卡链接
	 * @param string $template_id 必须
	 * @param string $out_string 可选
	 * @return json $apply_card_url
	 */
	public function alipay_card_activatelink($template_id = '', $out_string = '')
	{
		//PayAction::log(request()->method() . ' ' . request()->url());
		//PayAction::log(input('param.'));
		if(empty($template_id)) {
			return '缺少参数 [template_id]';
		}
		$out_info = json_decode($out_string, true);
		if(empty($out_info['user_id'])) {
			return '缺少外部参数 [user_id]';
		}
		$merchant_id = Db::name('merchant_alipay')->where('card_id', '=', $template_id)->value('merchant_id');
		if(empty($merchant_id)) {
			return '无效的参数 [template_id]';
		}
		$mch_user = model('MchUser')->get_user(['user_id' => $out_info['user_id'], 'merchant_id' => $merchant_id]);
		if(empty($mch_user)) {
			return '无效的外部参数 [user_id]';
		}
		$UserAlipay = new \app\common\UserAlipay();
		$res = $UserAlipay->card_activateurl($template_id, authcode(JSON($out_info), 'ENCODE', $out_info['user_id']));
		if($res['status'] == 1) {
			return \befen\jump($res['contents']['apply_card_url']);
		} else {
			echo $res['message'];
			exit();
		}
	}

	/**
	 * 支付宝开通会员
	 * @param String $open_user_id 用户唯一标识
	 */
	public function alipay_card_open($open_user_id = null)
	{
		//PayAction::log(request()->method() . ' ' . request()->url());
		//PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$user_id = input('post.user_id/s') ? input('post.user_id/s') : input('post.buyer_id/s');
		if(empty($user_id)) {
			return make_json(0, '缺少参数 [用户标识]');
		}
		$mch_user = model('MchUser')->get_user(['user_id' => $user_id, 'merchant_id' => $this->merchant['merchant_id']]);
		$card_no = model('MchUser')->make_card();
		if($mch_user) {
			$user_data = [];
			if(empty($mch_user['card_no'])) {
				$mch_user['card_no'] = $card_no;
				$user_data['card_no'] = $card_no;
			}
			if(!empty($user_data)) {
				model('MchUser')->allowField(true)->save($user_data, ['id' => $mch_user['id']]);
			}
			return make_json(1, 'ok', $mch_user);
		} else {
			model('MchUser')->allowField(true)->save([
				'merchant_id' => $this->merchant['merchant_id'],
				'card_no' => $card_no,
				'user_id' => $user_id,
				'credit' => '0.00',
				'balance' => '0.00',
				'open_datetime' => _time(),
			]);
			$mch_user = model('MchUser')->get_user(['card_no' => $card_no, 'merchant_id' => $this->merchant['merchant_id']]);
			return make_json(1, 'ok', $mch_user);
		}
	}

	/**
	 * 支付宝获取会员信息
	 * @param String $open_user_id 用户唯一标识
	 */
	public function alipay_card_userinfo($open_user_id = null)
	{
		//PayAction::log(request()->method() . ' ' . request()->url());
		//PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$card_no = input('post.card_no/s');
		$user_id = input('post.user_id/s') ? input('post.user_id/s') : input('post.buyer_id/s');
		if(empty($card_no) && empty($user_id)) {
			return make_json(0, '缺少参数 [用户标识]');
		}
		if(!empty($card_no)) {
			$mch_user = model('MchUser')->get_user(['card_no' => $card_no, 'merchant_id' => $this->merchant['merchant_id']]);
		} else {
			$mch_user = model('MchUser')->get_user(['user_id' => $user_id, 'merchant_id' => $this->merchant['merchant_id']]);
		}
		if(empty($mch_user)) {
			return make_json(0, '未获取到会员信息');
		} else {
			return make_json(1, 'ok', $mch_user);
		}
	}

}

