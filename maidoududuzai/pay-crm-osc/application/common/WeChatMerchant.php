<?php

namespace app\common;

use \think\Db;

class WeChatMerchant extends \app\common\WeChat
{

	public function __construct($Method = null, $config = [])
	{
		parent::__construct($Method, $config);
	}

	public static function init($config = [])
	{
		// mch cfg
		$self = new self();
		if(empty($config['cache_path'])) {
			$config['cache_path'] = TEMP_PATH . 'WeChat/Merchant/' . $config['appid'];
		}
		$self->set($config);
		return $self;
	}

	public function getUserInfo($openid)
	{
		try {
			$UserInfo = $this->load('User')->getUserInfo($openid);
			$fields = [
				'openid' => $UserInfo['openid'],
				'unionid' => $UserInfo['unionid'],
				'nickname' => $UserInfo['nickname'],
				'sex' => $UserInfo['sex'],
				'headimgurl' => $UserInfo['headimgurl'],
				'subscribe' => 1,
				'subscribe_time' => $UserInfo['subscribe_time'],
				'groupid' => $UserInfo['groupid'],
				'merchant_id' => $this->merchant_id
			];
			$where = [];
			$where['openid'] = ['=', $UserInfo['openid']];
			$where['merchant_id'] = ['=', $this->merchant_id];
			model('MchUser')->allowField(true)->save($fields, ['openid' => $UserInfo['openid']]);
			return $UserInfo;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	public function getUserCardInfo($CardId, $UserCardCode)
	{
		try {
			$UserCardInfo = $this->load('Card')->getCardMemberCard($CardId, $UserCardCode);
			if($UserCardInfo && ($CardId == $this->card_id)) {
				$fields = [];
				$fields['UserCardCode'] = $UserCardInfo['membership_number'];
				foreach($UserCardInfo['user_info']['common_field_list'] as $key => $val) {
					switch($val['name']) {
						case 'USER_FORM_INFO_FLAG_NAME':
							$fields['username'] = $val['value'];
						break;
						case 'USER_FORM_INFO_FLAG_MOBILE':
							$fields['phone'] = $val['value'];
						break;
						case 'USER_FORM_INFO_FLAG_BIRTHDAY':
							$fields['birthday'] = $val['value'];
						break;
					}
				}
				model('MchUser')->allowField(true)->save($fields, ['openid' => $UserCardInfo['openid']]);
			}
			return $UserCardInfo;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

}

