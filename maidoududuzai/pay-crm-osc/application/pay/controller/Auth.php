<?php

namespace app\pay\controller;

use \think\Db;
use \app\common\Pay;
use \app\common\PayAction;

class Auth
{

	public $merchant = [];
	public $person = [];
	public $device = [];

	public $SN = null;
	public $debug = null;

	public $errNo = 0;
	public $errMsg = null;

	public function __construct()
	{
		$this->SN = input('post.SN/s');
		$this->person_id = input('post.person_id/s');
		$this->debug();
		if(empty($this->SN) || empty($this->person_id)) {
			$this->errMsg = '缺少公共参数';
		} else {
			if(request()->action() != 'login') {
				$this->person = $this->get_person(['person_id' => $this->person_id]);
				if(empty($this->person)) {
					$this->errMsg = '员工不存在';
				} else {
					if($this->person['status'] == 0) {
						$this->errMsg = '员工不可用';
					} else {
						$this->merchant = Pay::merchant($merchant_id, ['SN' => $this->SN]);
						if(empty($this->merchant)) {
							$this->errMsg = '未获取到商户信息';
						} else {
							if($merchant_id != $this->person['merchant_id']) {
								$this->errMsg = '商户信息验证失败';
							}
						}
						$this->merchant['person_id'] = $this->person['person_id'];
						$this->merchant['store_id'] = $this->merchant['store_device']['store_id'];
						$this->merchant['device_id'] = $this->merchant['store_device']['device_id'];
					}
				}
			}
		}
	}

	/**
	 * 调试模式
	 */
	public function debug()
	{
		$this->device = Db::name('store_device')->where('status', '=', '1')->where('SN', '=', $this->SN)->field('device_id, SN, merchant_id')->find();
		if(empty($this->device)) {
			$this->debug = true;
			$this->device = Db::name('store_device')
				->alias('sd')
				->join('store_person sp', 'sd.merchant_id = sp.merchant_id', 'LEFT')
				->where('sd.status', '=', '1')
				->where('sp.status', '=', '1')
				->order('sd.device_id', 'ASC')
				->order('sp.person_id', 'ASC')
				->field('sp.person_id, sd.device_id, sd.SN, sd.merchant_id')
				->find();
			if(!empty($this->device)) {
				$this->SN = $this->device['SN'];
			}
		}
	}

	/**
	 * 卡券颜色
	 * @param String $card_color
	 */
	public function color($card_color = '')
	{
		$color_array = [
			'Color010' => '#63B359',
			'Color020' => '#2C9F67',
			'Color030' => '#509FC9',
			'Color040' => '#5885CF',
			'Color050' => '#9062C0',
			'Color060' => '#D09A45',
			'Color070' => '#E4B138',
			'Color080' => '#EE903C',
			'Color081' => '#F08500',
			'Color082' => '#A9D92D',
			'Color090' => '#DD6549',
			'Color100' => '#CC463D',
			'Color101' => '#CF3E36',
			'Color102' => '#5E6671',
		];
		return isset($color_array[$card_color]) ? $color_array[$card_color] : '#15A01F';
	}

	/**
	 * 店员登录
	 * @param String $SN
	 * @param String $per_phone
	 */
	public function login()
	{
		PayAction::log(request()->method() . ' ' . request()->url());
		PayAction::log(input('post.'));
		$SN = input('post.SN/s');
		$per_phone = input('post.per_phone/s');
		if(empty($SN)) {
			return make_json(0, '缺少参数 [SN]');
		}
		if(empty($per_phone)) {
			return make_json(0, '缺少参数 [per_phone]');
		}
		if($this->debug == null) {
			$person = $this->get_person(['per_phone' => $per_phone]);
			if(empty($person)) {
				return make_json(0, '登录失败 [员工不存在]');
			} else {
				if($person['status'] == 0) {
					return make_json(0, '登录失败 [员工不可用]');
				}
			}
			if($person['merchant_id'] != $this->device['merchant_id']) {
				return make_json(0, '登录失败 [验证失败]');
			}
		} else {
			if($per_phone == '00000000000') {
				if(!empty($this->device)) {
					$person = $this->get_person(['person_id' => $this->device['person_id']]);
				}
				if(empty($person)) {
				return make_json(0, '登录失败 [员工不存在]');
				}
			} else {
				return make_json(0, '登录失败 [DEBUG]');
			}
		}
		$person['person_token'] = authcode(authcode($person['password'], 'DECODE'), 'ENDODE', $this->device['device_id']);
		unset($person['password']);
		$weixin_card = Db::name('merchant_weixin')->where('merchant_id', '=', $person['merchant_id'])->field('is_card, is_charge, card_id, card_data, card_bg_url, card_logo_url')->find();
		$alipay_card = Db::name('merchant_alipay')->where('merchant_id', '=', $person['merchant_id'])->field('is_card, is_charge, card_id, card_data, card_bg_url, card_logo_url')->find();
		$weixin_card_data = json_decode($weixin_card['card_data'], true);
		$alipay_card_data = json_decode($alipay_card['card_data'], true);
		$person['card'] = [
			'weixin' => [
				'is_card' => $weixin_card['is_card'],
				'is_charge' => $weixin_card['is_charge'],
				'card_id' => $weixin_card['card_id'],
				'card_show_name' => !empty($weixin_card_data['base_info']['title']) ? $weixin_card_data['base_info']['title'] : '',
				'card_color' => !empty($weixin_card_data['base_info']['color']) ? $this->color($weixin_card_data['base_info']['color']) : '',
				'card_bg_url' => url('/', null, null, true) . preg_replace('/^\//', '', $weixin_card['card_bg_url']),
				'card_logo_url' => url('/', null, null, true) . preg_replace('/^\//', '', $weixin_card['card_logo_url']),
			],
			'alipay' => [
				'is_card' => $alipay_card['is_card'],
				'is_charge' => $alipay_card['is_charge'],
				'card_id' => $alipay_card['card_id'],
				'card_show_name' => !empty($alipay_card_data['template_style_info']['card_show_name']) ? $alipay_card_data['template_style_info']['card_show_name'] : '',
				'card_color' => null,
				'card_bg_url' => url('/', null, null, true) . preg_replace('/^\//', '', $alipay_card['card_bg_url']),
				'card_logo_url' => url('/', null, null, true) . preg_replace('/^\//', '', $alipay_card['card_logo_url']),
			],
		];
		$person['card_charge'] = Db::name('mch_charge')->where('merchant_id', '=', $person['merchant_id'])->where('status', '=', '1')->field('id, name, pay_amount, send_amount')->order('pay_amount', 'ASC')->select();
		PayAction::log($person);
		return make_json(1, 'ok', $person);
	}

	/**
	 * 密码验证
	 * @param String $password
	 */
	public function check()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$password = input('post.password');
		if($this->debug) {
			if($password == '147258') {
				return make_json(1, 'ok');
			} else {
				return make_json(0, '密码错误');
			}
		} else {
			if($password == authcode($this->person['password'], 'DECODE')) {
				return make_json(1, 'ok');
			} else {
				return make_json(0, '密码错误');
			}
		}
	}

	/**
	 * 密码验证
	 * @param String $password
	 */
	public function verify()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		if(authcode($this->person['password'], 'DECODE') == authcode(input('post.password'), 'DECODE', $this->device['device_id'])) {
			return make_json(1, 'ok');
		} else {
			return make_json(0, '密码验证失败');
		}
	}

	/**
	 * 获取店员信息
	 * @param Array $field
	 * @param String $SN (null)
	 */
	public function get_person($field = [], $SN = null)
	{
		$where = [];
		foreach($field as $key => $val) {
			$where['sp.'.$key] = ['=', $val];
		}
		if(!empty($SN)) {
			$where['sd.SN'] = ['=', $SN];
		}
		$person = Db::name('store_person')
			->alias('sp')
			->join('merchant m', 'm.merchant_id = sp.merchant_id', 'LEFT')
			->join('store s', 's.store_id = sp.store_id', 'LEFT')
			->join('store_device sd', 'sd.merchant_id = sp.merchant_id', 'LEFT')
			->where($where)
			->field('sp.person_id, sp.per_name, sp.per_phone, sp.status, sp.manager, sp.openid, sp.password, m.merchant_id, m.merchant_no, m.merchant_name, m.merchant_shortname, s.store_id, s.store_name')
			->find();
		if(empty($person)) {
			return [];
		} else {
			if(empty($person['merchant_shortname'])) {
				$person['merchant_shortname'] = $person['merchant_name'];
			}
			return $person;
		}
	}

}

