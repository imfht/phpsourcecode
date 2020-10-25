<?php

namespace app\merchant\controller;

use \think\Db;
use \app\common\UserWeixin;

class Weixin
{

	public $merchant;
	public $UserWeixin;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
		$this->UserWeixin = new UserWeixin($this->merchant);
	}

	public function index()
	{
		$value = model('MerchantWeixin')->get(['merchant_id' => $this->merchant['merchant_id']]);
		if(request()->isPost()) {
			$MchWeChat = \app\common\WeChatMerchant::init([
				'appid' => input('post.appid'),
				'appsecret' => input('post.appsecret'),
			]);
			try {
				$MchWeChat->load('User')->getUserList();
			} catch (\Exception $e) {
				return make_json(0, $e->getMessage());
			}
			model('MerchantWeixin')->allowField(true)->save(input('post.'), ['merchant_id' => $this->merchant['merchant_id']]);
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	/**
	 * 会员卡开关
	 * @param string $is_card
	 */
	public function card_switch()
	{
		if(request()->isPost()) {
			Db::name('merchant_weixin')->where('merchant_id', '=', $this->merchant['merchant_id'])->update(['is_card' => input('post.is_card')]);
			return make_json(1, '操作成功');
		}
	}

	/**
	 * 会员卡储值开关
	 * @param string $is_charge
	 */
	public function charge_switch()
	{
		if(request()->isPost()) {
			Db::name('merchant_weixin')->where('merchant_id', '=', $this->merchant['merchant_id'])->update(['is_charge' => input('post.is_charge')]);
			return make_json(1, '操作成功');
		}
	}

	public function card()
	{
		if(request()->isPost()) {
			$option = [];
			$option['background_id'] = input('param.background_id/s', '');
			$option['logo_id'] =input('param.logo_id/s', '');
			$card_bg_url = input('param.card_bg_url/s', '');
			if($card_bg_url) {
				$option['card_bg_url'] = $card_bg_url;
			} else {
				return make_json(0, '会员卡背景必填');
			}
			$card_logo_url = input('param.card_logo_url/s', '');
			if($card_logo_url) {
				$option['card_logo_url'] = $card_logo_url;
			} else {
				return make_json(0, '会员卡LOGO必填');
			}
			$brand_name = input('param.brand_name/s', '');
			$option['brand_name'] = $brand_name;
			if(empty($brand_name)) {
				return make_json(0, '商户名称必填');
			}
			$title = input('param.title/s', '');
			$option['title'] = $title;
			if(empty($title)) {
				return make_json(0, '标题必填');
			}
			$color = input('param.color/s', '');
			$option['color'] = $color;
			if(!in_array($color, ['Color010', 'Color020', 'Color030', 'Color040', 'Color050', 'Color060', 'Color070', 'Color080', 'Color090', 'Color100', 'Color101', 'Color102'])) {
				return make_json(0, '颜色必填');
			}
			$service_phone = input('param.service_phone/s', '');
			$option['service_phone'] = $service_phone;
			if(empty($service_phone)) {
				return make_json(0, '商家电话必填');
			}
			$prerogative = input('param.prerogative/s', '');
			$option['prerogative'] = $prerogative;
			if(empty($prerogative)) {
				return make_json(0, '会员权益说明必填');
			}
			if(empty($this->UserWeixin->card_id)) {
				// 创建卡
				$result = $this->UserWeixin->create_card_template($option);
			} else {
				// 更新卡
				$result = $this->UserWeixin->modify_card_template($option);
			}
			if($result['status'] == 1) {
				$data_update = [];
				if(!empty($card_bg_url)) {
					$data_update['card_bg_url'] = $card_bg_url;
				}
				if(!empty($card_logo_url)) {
					$data_update['card_logo_url'] = $card_logo_url;
				}
				if(!empty($data_update)) {
					Db::name('merchant_weixin')->where('merchant_id', '=', $this->merchant['merchant_id'])->update($data_update);
				}
			}
			return $result;
		}
		//view
		$value = Db::name('merchant_weixin')
			->where('merchant_id', '=', $this->merchant['merchant_id'])
			->field('status, is_card, is_charge, card_id, card_data, card_bg_url, card_logo_url')
			->find();
		$value = !empty($value) ? $value : [];
		$card_data = !empty($value['card_data']) ? $value['card_data'] : '';
		$card_data = json_decode($card_data, true);
		$card_data = !empty($card_data) ? $card_data : [];
		//背景logo
		$value['background_id'] = (empty($card_data['background_pic_url']) || empty($value['card_bg_url'])) ? '' : $card_data['background_pic_url'];
		$value['logo_id'] = (empty($card_data['base_info']['logo_url']) || empty($value['card_logo_url'])) ? '' : $card_data['base_info']['logo_url'];
		$value['card_bg_url'] = (empty($value['background_id']) || empty($value['card_bg_url'])) ? '' : $value['card_bg_url'];
		$value['card_logo_url'] = (empty($value['logo_id']) || empty($value['card_logo_url'])) ? '' : $value['card_logo_url'];
		//商户名称
		$value['brand_name'] = empty($card_data['base_info']['brand_name']) ? '' : $card_data['base_info']['brand_name'];
		//标题
		$value['title'] = empty($card_data['base_info']['title']) ? '' : $card_data['base_info']['title'];
		//颜色
		$value['color'] = empty($card_data['base_info']['color']) ? '' : $card_data['base_info']['color'];
		//客服电话
		$value['service_phone'] = empty($card_data['base_info']['service_phone']) ? '' : $card_data['base_info']['service_phone'];
		//会员权益说明
		$value['prerogative'] = empty($card_data['prerogative']) ? '' : $card_data['prerogative'];
		include \befen\view();
	}

	public function del_card($card_id = '')
	{
		if(empty($card_id)) {
			$card_id = Db::name('merchant_weixin')->where('merchant_id', '=', $this->merchant['merchant_id'])->value('card_id');
		}
		$Card = $this->UserWeixin->MchWeChat->load('Card');
		try {
			$res = $Card->deleteCard($card_id);
			Db::name('merchant_weixin')->where('merchant_id', '=', $this->merchant['merchant_id'])->update(['card_id' => '', 'card_data' => '']);
			return make_json(1, 'ok', $res);
		} catch (\Exception $e) {
			return make_json(0, $e->getMessage());
		}
	}

	/**
	 * 上传图片，会员卡LOGO背景图
	 * @param file $image
	 * @return array
	 */
	public function media_upload()
	{
		return $this->UserWeixin->media_upload();
	}

	/**
	 * 获取会员卡领卡投放链接
	 * @param string $card_id
	 * @return array $apply_card_url
	 */
	public function card_activateurl($card_id = '', $out_string = '')
	{
		if(empty($card_id)) {
			$card_id = Db::name('merchant_weixin')->where('merchant_id', '=', $this->merchant['merchant_id'])->value('card_id');
		}
		if(empty($card_id)) {
			return make_json(0, '会员卡未配置');
		}
		$short_url = model('Url')->make(url('/pay/user/weixin_card_activate', ['card_id' => $card_id, 'out_string' => $out_string], null, true));
		if($short_url['status'] == 0) {
			return make_json(0, $short_url['message']);
		}
		return make_json(1, 'ok', [
			'apply_card_url' => $short_url['contents']['url']
		]);
	}

}

