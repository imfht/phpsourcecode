<?php

namespace app\common;

use \think\Db;
use \app\common\WeChatMerchant;

Class UserWeixin {

	public $appid = null;
	public $appsecret = null;

	public $card_id = null;
	public $card_data = [];

	public $merchant = [];
	public $merchant_id = null;

	public function __construct($merchant = [])
	{
		//parent::__construct();
		if(!is_array($merchant)) {
			if(!empty($merchant)) {
				$this->set_mch_id($merchant);
			}
		} else {
			if(!empty($merchant['merchant_id'])) {
				$this->set_mch_id($merchant['merchant_id']);
			}
		}
	}

	/**
	 * 设置商户
	 * @param string $merchant_id
	 * @return array
	 */
	public function set_mch_id($merchant_id)
	{
		$merchant = Db::name('merchant')
			->alias('m')
			->join('merchant_weixin mw', 'm.merchant_id = mw.merchant_id')
			->where('m.merchant_id', '=', $merchant_id)
			->field('m.merchant_id, m.merchant_no, mw.card_id, mw.card_data, mw.appid, mw.appsecret')
			->find();
		if(!empty($merchant)) {
			$this->merchant = [
				'merchant_id' => $merchant['merchant_id'],
				'merchant_no' => $merchant['merchant_no'],
			];
			$this->merchant_id = $merchant['merchant_id'];
			$this->appid = $merchant['appid'];
			$this->appsecret = $merchant['appsecret'];
			$this->card_id = $merchant['card_id'];
			$this->card_data = json_decode($merchant['card_data'], true);
			$this->MchWeChat = WeChatMerchant::init([
				'appid' => $merchant['appid'],
				'appsecret' => $merchant['appsecret'],
			]);
		}
		return $merchant;
	}

	/**
	 * 获取会员卡领卡投放链接
	 * @param string $card_id 必须
	 * @param json $out_string 可选
	 * @return array [apply_card_url]
	 */
	public function card_activateurl($card_id = '', $out_string = '')
	{
		$merchant = Db::name('merchant')
			->alias('m')
			->join('merchant_weixin mw', 'm.merchant_id = mw.merchant_id')
			->where('mw.card_id', '=', $card_id)
			->field('m.merchant_id, m.merchant_no, mw.card_id, mw.card_data, mw.appid, mw.appsecret')
			->find();
		if(empty($merchant)) {
			return make_return(0, '未获取到商户id');
		}
		$this->merchant = [
			'merchant_id' => $merchant['merchant_id'],
			'merchant_no' => $merchant['merchant_no'],
		];
		$this->merchant_id = $merchant['merchant_id'];
		$this->appid = $merchant['appid'];
		$this->appsecret = $merchant['appsecret'];
		$this->MchWeChat = WeChatMerchant::init([
			'appid' => $merchant['appid'],
			'appsecret' => $merchant['appsecret'],
		]);
		$Card = $this->MchWeChat->load('Card');
		$out_info = json_decode($out_string, true);
		//二维码投放链接
		try {
			$res = $Card->createQrc([
				'action_name' => 'QR_CARD',
				'action_info' => [
					'card' => [
						'card_id' => $card_id,
						'outer_str' => JSON($out_info),
						//'code' => $out_info['card_no'],
						//'openid' => $out_info['sub_openid'],
					]
				]
			]);
			return make_return(1, 'ok', ['apply_card_url' => $res['url']]);
		} catch (\Exception $e) {
			return make_return(0, '获取投放链接失败,' . $e->getMessage());
		}
	}

	/**
	 * 会员卡激活
	 * @param string $card_id
	 * @param string $card_no
	 * @param string $init_bonus
	 * @param string $init_balance
	 * @param string $background_pic_url
	 * @return array
	 */
	public function card_open($card_id, $card_no, $init_bonus = '', $init_balance = '', $background_pic_url = null)
	{
		$Card = $this->MchWeChat->load('Card');
		try {
			$data = [
				'membership_number' => $card_no,
				'code' => $card_no,
				'card_id' => $card_id,
				'init_bonus' => $init_bonus,
				//'init_balance' => $init_balance,
			];
			if(!empty($background_pic_url)) {
				$data['init_bonus_record'] = $background_pic_url;
			}
			$res = $Card->activateMemberCard($data);
			return make_return(1, 'ok', $res);
		} catch (\Exception $e) {
			return make_return(0, '会员卡激活失败,' . $e->getMessage());
		}
	}

	/**
	 * 会员卡查询
	 * @param string $card_id
	 * @param string $card_no
	 * @return array
	 */
	public function card_query($card_id, $card_no)
	{
		$Card = $this->MchWeChat->load('Card');
		try {
			$res = $Card->getCardMemberCard($card_id, $card_no);
			return make_return(1, 'ok', $res);
		} catch (\Exception $e) {
			return make_return(0, '会员卡查询失败,' . $e->getMessage());
		}
	}

	/**
	 * 会员卡更新
	 * @param string $card_id
	 * @param string $card_no
	 * @param string $bonus
	 * @param string $balance
	 * @param string $background_pic_url
	 * @return array
	 */
	public function card_update($card_id, $card_no, $bonus = '', $balance = '', $background_pic_url = null)
	{
		$mch_user = Db::name('mch_user')->where(['UserCardCode' => $card_no, 'merchant_id' => $this->merchant_id])->field('id, card_no, credit, balance')->find();
		if(empty($mch_user)) {
			return make_return(0, '未获取到会员信息');
		}
		$Card = $this->MchWeChat->load('Card');
		try {
			$data = [
				'card_id' => $card_id,
				'code' => $card_no,
				'bonus' => $bonus ? $bonus : $mch_user['credit'],
				//'balance' => $balance ? $balance : $mch_user['balance'],
			];
			if(!empty($background_pic_url)) {
				$data['init_bonus_record'] = $background_pic_url;
			}
			$res = $Card->updateMemberCardUser($data);
			return make_return(1, 'ok', $res);
		} catch (\Exception $e) {
			return make_return(0, '更新会员卡失败,' . $e->getMessage());
		}
	}

	/**
	 * 查询会员卡表单信息
	 * @param string $activate_ticket 必须
	 * @return array
	 */
	public function query_card_activateform($activate_ticket)
	{
		$Card = $this->MchWeChat->load('Card');
		try {
			$res = $Card->getActivateMemberCardTempinfo($activate_ticket);
			return make_return(1, 'ok', $res);
		} catch (\Exception $e) {
			return make_return(0, '查询会员卡表单信息失败,' . $e->getMessage());
		}
	}

	/**
	 * 上传图片，会员卡LOGO背景图
	 * @param file $image
	 * @param string $storage 存储方式 local(本地)|remote(远程)
	 * @return array
	 */
	public function media_upload($storage = 'local')
	{
		$file = request()->file('image');
		if(empty($file)) {
			return make_return(0, '图片必传');
		}
		$info = \app\common\Upload::merchant($this->merchant, 'image');
		if($info['status'] == 0) {
			return make_return(0, '上传失败:' . $info['message']);
		} else {
			if(!is_file(file_path($info['contents']['SaveName']))) {
				return make_return(0, '上传失败:文件不存在');
			}
		}
		$Media = $this->MchWeChat->load('Media');
		try {
			$res = $Media->uploadImg('@' . file_path($info['contents']['SaveName']));
			return make_return(1, 'ok', [
				'image_url' => $res['url'],
				'local_url' => url('/') . preg_replace('/^\//', '', $info['contents']['SaveName'])
			]);
		} catch (\Exception $e) {
			return make_return(0, '上传失败:' . $e->getMessage());
		}
	}

	/**
	 * 会员卡开卡表单配置
	 * @param string $card_id 可选
	 * @param array $option USER_FORM_INFO_FLAG_MOBILE|USER_FORM_INFO_FLAG_NAME|USER_FORM_INFO_FLAG_SEX|USER_FORM_INFO_FLAG_BIRTHDAY
	 * @return array
	 */
	public function set_card_formtemplate($card_id = '', $fields = [])
	{
		if(empty($card_id)) {
			$card_id = $this->card_id;
		}
		if(empty($template_id)) {
			return make_return(0, '未获取到card_id');
		}
		$fields = [
			'card_id' => $card_id,
			'required_form' => [
				'common_field_id_list' => [
					'USER_FORM_INFO_FLAG_MOBILE',
					'USER_FORM_INFO_FLAG_NAME',
					'USER_FORM_INFO_FLAG_SEX',
					'USER_FORM_INFO_FLAG_BIRTHDAY',
				]
			]
		];
		$Card = $this->MchWeChat->load('Card');
		try {
			$res = $Card->setActivateMemberCardUser($fields);
			return make_return(1, 'ok', $res);
		} catch (\Exception $e) {
			return make_return(0, '开卡表单配置失败,' . $e->getMessage());
		}
	}

	/**
	 * 查询会员卡
	 * @param string $card_id
	 * @return array
	 */
	public function query_card_template($card_id = '')
	{
		if(empty($card_id)) {
			$card_id = $this->card_id;
		}
		if(empty($card_id)) {
			return make_return(0, '未获取到card_id');
		}
		$Card = $this->MchWeChat->load('Card');
		try {
			$res = $Card->getCard($card_id);
			return make_return(1, 'ok', $res['contents']);
		} catch (\Exception $e) {
			return make_return(0, '查询会员卡失败,' . $e->getMessage());
		}
	}

	/**
	 * 创建会员卡
	 * @param array $option
	 * @return array
	 */
	public function create_card_template($option = [])
	{
		$card_id = Db::name('merchant_weixin')->where('merchant_id', '=', $this->merchant_id)->value('card_id');
		if($card_id) {
			return make_return(0, '会员卡已经存在');
		}
		$data = [
			'card' => [
				'card_type' => 'MEMBER_CARD',
				'member_card' => [
					'background_pic_url' => $option['background_id'],
					'custom_field1' => [
						'name' => '会员卡',
						'url' => url('/wechat/card/user_info', ['merchant_id' => $this->merchant_id], null, true),
					],
					'base_info' => [
						'logo_url' => $option['logo_id'],
						'brand_name' => $option['brand_name'],
						'code_type' => 'CODE_TYPE_QRCODE',
						'title' => $option['title'],
						'color' => $option['color'],
						//'center_title' => '微信支付',
						//'center_url' => 'weixin://qrcode',
						'notice' => '',
						'service_phone' => $option['service_phone'],
						'description' => '',
						'date_info' => [
							'type' => 'DATE_TYPE_PERMANENT'
						],
						'sku' => [
							'quantity' => 0
						],
						'pay_info' => [
							'swipe_card' => [
								'is_swipe_card' => true
							]
						],
						'bind_openid' => true,
						'get_limit' => 1,
						'can_give_friend' => false,
						'need_push_on_view' => false,
						//'custom_url_name' => '会员中心',
						//'custom_url' => url('/wechat/card/user_info', ['merchant_id' => $this->merchant_id], null, true),
						//'promotion_url_name' => '营销入口',
						//'promotion_url' => url('/wechat/card/marketing', ['merchant_id' => $this->merchant_id], null, true),
						'use_custom_code' => true,
						//'get_custom_code_mode' => 'GET_CUSTOM_CODE_MODE_DEPOSIT',
					],
					'prerogative' => $option['prerogative'],
					'wx_activate' => true,
					'wx_activate_after_submit' => true,
					'wx_activate_after_submit_url' => url('/wechat/card/open', ['merchant_id' => $this->merchant_id], null, true),
					'supply_balance' => false,
					'supply_bonus' => true,
					'bonus_rule' => [
						'cost_money_unit' => 0,
						'increase_bonus' => 0,
						'max_increase_bonus' => 0,
						'init_increase_bonus' => 0,
						'cost_bonus_unit' => 0,
						'reduce_money' => 0,
						'least_money_to_use_bonus' => 0,
						'max_reduce_bonus' => 0,
					]
				],
			],
		];
		if(empty($option['background_id'])) {
			unset($data['card']['member_card']['background_pic_url']);
		}
		$Card = $this->MchWeChat->load('Card');
		try {
			$res = $Card->create($data);
			$this->set_card_formtemplate($res['card_id']);
			unset($data['card']['member_card']['base_info']['sku']);
			Db::name('merchant_weixin')->where('merchant_id', '=', $this->merchant['merchant_id'])->update([
				'card_id' => $res['card_id'],
				'card_data' => JSON($data['card']['member_card'])
			]);
			return make_return(1, 'ok', $res);
		} catch (\Exception $e) {
			return make_return(0, '创建会员卡失败,' . $e->getMessage());
		}
	}

	/**
	 * 修改会员卡
	 * @param array $option
	 * @return array
	 */
	public function modify_card_template($option = [])
	{
		if(empty($option['card_id'])) {
			$option['card_id'] = $this->card_id;
		}
		if(empty($option['card_id'])) {
			return make_return(0, '未获取到card_id');
		}
		$data = [
			'card' => [
				'card_type' => 'MEMBER_CARD',
				'member_card' => [
					'background_pic_url' => $option['background_id'],
					'custom_field1' => [
						'name' => '会员卡',
						'url' => url('/wechat/card/user_info', ['merchant_id' => $this->merchant_id], null, true),
					],
					'base_info' => [
						'logo_url' => $option['logo_id'],
						'brand_name' => $option['brand_name'],
						'code_type' => 'CODE_TYPE_QRCODE',
						'title' => $option['title'],
						'color' => $option['color'],
						//'center_title' => '微信支付',
						//'center_url' => 'weixin://qrcode',
						'notice' => '',
						'service_phone' => $option['service_phone'],
						'description' => '',
						'date_info' => [
							'type' => 'DATE_TYPE_PERMANENT'
						],
						/*
						'sku' => [
							'quantity' => 0
						],
						*/
						'pay_info' => [
							'swipe_card' => [
								'is_swipe_card' => true
							]
						],
						'bind_openid' => true,
						'get_limit' => 1,
						'can_give_friend' => false,
						'need_push_on_view' => false,
						//'custom_url_name' => '会员中心',
						//'custom_url' => url('/wechat/card/user_info', ['merchant_id' => $this->merchant_id], null, true),
						//'promotion_url_name' => '营销入口',
						//'promotion_url' => url('/wechat/card/marketing', ['merchant_id' => $this->merchant_id], null, true),
						'use_custom_code' => true,
						//'get_custom_code_mode' => 'GET_CUSTOM_CODE_MODE_DEPOSIT',
					],
					'prerogative' => $option['prerogative'],
					'wx_activate' => true,
					'wx_activate_after_submit' => true,
					'wx_activate_after_submit_url' => url('/wechat/card/open', ['merchant_id' => $this->merchant_id], null, true),
					'supply_balance' => false,
					'supply_bonus' => true,
					'bonus_rule' => [
						'cost_money_unit' => 0,
						'increase_bonus' => 0,
						'max_increase_bonus' => 0,
						'init_increase_bonus' => 0,
						'cost_bonus_unit' => 0,
						'reduce_money' => 0,
						'least_money_to_use_bonus' => 0,
						'max_reduce_bonus' => 0,
					]
				],
			],
		];
		if(empty($option['background_id'])) {
			unset($data['card']['member_card']['background_pic_url']);
		}
		$Card = $this->MchWeChat->load('Card');
		try {
			$member_card = $data['card']['member_card'];
			//不允许修改的参数
			unset($member_card['base_info']['brand_name']);
			unset($member_card['base_info']['use_custom_code']);
			//不允许修改的参数
			$res = $Card->updateCard($option['card_id'], $member_card);
			$this->set_card_formtemplate($option['card_id']);
			unset($data['card']['member_card']['base_info']['sku']);
			Db::name('merchant_weixin')->where('merchant_id', '=', $this->merchant['merchant_id'])->update([
				'card_id' => $option['card_id'],
				'card_data' => JSON($data['card']['member_card'])
			]);
			return make_return(1, 'ok', $res);
		} catch (\Exception $e) {
			return make_return(0, '修改会员卡失败,' . $e->getMessage());
		}
	}

}

