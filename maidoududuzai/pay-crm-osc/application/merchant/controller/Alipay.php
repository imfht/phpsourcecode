<?php

namespace app\merchant\controller;

use \think\Db;
use \app\common\UserAlipay;

class Alipay
{

	public $merchant;
	public $UserAlipay;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
		$this->UserAlipay = new UserAlipay($this->merchant);
	}

	public function index()
	{
		$value = model('MerchantAlipay')->get(['merchant_id' => $this->merchant['merchant_id']]);
		if(request()->isPost()) {
			//model('MerchantAlipay')->allowField(true)->save(input('post.'), ['merchant_id' => $this->merchant['merchant_id']]);
			return make_json(1, '操作成功');
		}
		include \befen\view();
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
			$card_show_name = input('param.card_show_name/s', '');
			$option['card_show_name'] = $card_show_name;
			if(empty($card_show_name)) {
				return make_json(0, '标题必填');
			}
			$phone = input('param.phone/s', '');
			$option['TELEPHOME'] = $phone;
			if(empty($phone)) {
				return make_json(0, '商家电话必填');
			}
			$template_benefit_info = input('post.template_benefit_info/a', []);
			if(!empty($template_benefit_info['show'])) {
				unset($template_benefit_info['show']);
				$option['template_benefit_info'] = $template_benefit_info;
			} else {
				$template_benefit_info = [];
			}
			if(empty($this->UserAlipay->app_auth_token)) {
				return make_json(0, '未获取到app_auth_token');
			}
			if(empty($this->UserAlipay->card_id)) {
				// 创建
				$result = $this->UserAlipay->create_card_template($option);
			} else {
				// 更新
				$result = $this->UserAlipay->modify_card_template($option);
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
					Db::name('merchant_alipay')->where('merchant_id', '=', $this->merchant['merchant_id'])->update($data_update);
				}
			}
			return $result;
		}
		//view
		$value = Db::name('merchant_alipay')
			->where('merchant_id', '=', $this->merchant['merchant_id'])
			->field('status, is_card, is_charge, card_id, card_data, card_bg_url, card_logo_url')
			->find();
		$value = !empty($value) ? $value : [];
		$card_data = !empty($value['card_data']) ? $value['card_data'] : '';
		$card_data = json_decode($card_data, true);
		$card_data = !empty($card_data) ? $card_data : [];
		//背景logo
		$value['background_id'] = (empty($card_data['template_style_info']['background_id']) || empty($value['card_bg_url'])) ? '' : $card_data['template_style_info']['background_id'];
		$value['logo_id'] = (empty($card_data['template_style_info']['logo_id']) || empty($value['card_logo_url'])) ? '' : $card_data['template_style_info']['logo_id'];
		$value['card_bg_url'] = (empty($value['background_id']) || empty($value['card_bg_url'])) ? '' : $value['card_bg_url'];
		$value['card_logo_url'] = (empty($value['logo_id']) || empty($value['card_logo_url'])) ? '' : $value['card_logo_url'];
		//标题
		$value['card_show_name'] = empty($card_data['template_style_info']['card_show_name']) ? '' : $card_data['template_style_info']['card_show_name'];
		//商家电话
		$card_data['column_info_list'] = empty($card_data['column_info_list']) ? [] : $card_data['column_info_list'];
		$phone = array_filter($card_data['column_info_list'], function($v) {
			return $v['code'] == 'TELEPHOME' ? true : false;
		});
		$value['phone'] = empty($phone) ? '' : array_values($phone)[0]['value'];
		//会员权益
		if(!empty($card_data['template_benefit_info'])) {
			$value['template_benefit_info'] = $card_data['template_benefit_info'][0];
			$value['template_benefit_info']['benefit_desc'] = implode(PHP_EOL, $value['template_benefit_info']['benefit_desc']);
			$value['template_benefit_info']['show'] = 1;
		} else {
			$value['template_benefit_info'] = [
				'title'=> '',
				'benefit_desc'=> '',
				'start_date'=> '',
				'end_date'=> ''
			];
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
			Db::name('merchant_alipay')->where('merchant_id', '=', $this->merchant['merchant_id'])->update(['is_card' => input('post.is_card')]);
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
			Db::name('merchant_alipay')->where('merchant_id', '=', $this->merchant['merchant_id'])->update(['is_charge' => input('post.is_charge')]);
			return make_json(1, '操作成功');
		}
	}

	/**
	 * 上传图片，会员卡LOGO背景图
	 * @param file $image
	 * @return array
	 */
	public function media_upload()
	{
		return $this->UserAlipay->media_upload();
	}

	/**
	 * 获取会员卡领卡投放链接
	 * @param string $template_id
	 * @return array $apply_card_url
	 */
	public function card_activateurl($template_id = '', $out_string = '')
	{
		if(empty($template_id)) {
			$template_id = Db::name('merchant_alipay')->where('merchant_id', '=', $this->merchant['merchant_id'])->value('card_id');
		}
		if(empty($template_id)) {
			return make_json(0, '会员卡未配置');
		}
		$short_url = model('Url')->make(url('/pay/user/alipay_card_activate', ['template_id' => $template_id, 'out_string' => $out_string], null, true));
		if($short_url['status'] == 0) {
			return make_json(0, $short_url['message']);
		}
		return make_json(1, 'ok', [
			'apply_card_url' => $short_url['contents']['url']
		]);
	}

}

