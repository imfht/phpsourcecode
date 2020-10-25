<?php

namespace app\common;

use \think\Db;
use \app\common\AopSdk;

Class UserAlipay extends AopSdk {

	public $app_auth_token = null;

	public $card_id = null;
	public $card_data = [];

	public $merchant = [];
	public $merchant_id = null;

	public function __construct($merchant = [])
	{
		parent::__construct();
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
			->join('merchant_alipay ma', 'm.merchant_id = ma.merchant_id')
			->where('m.merchant_id', '=', $merchant_id)
			->field('m.merchant_id, m.merchant_no, ma.card_id, ma.card_data, ma.app_auth_token')
			->find();
		if(!empty($merchant)) {
			$this->merchant = [
				'merchant_id' => $merchant['merchant_id'],
				'merchant_no' => $merchant['merchant_no'],
			];
			$this->merchant_id = $merchant['merchant_id'];
			$this->app_auth_token = $merchant['app_auth_token'];
			$this->card_id = $merchant['card_id'];
			$this->card_data = json_decode($merchant['card_data'], true);
		}
		return $merchant;
	}

	/**
	 * 获取会员卡领卡投放链接
	 * @param string $template_id 必须
	 * @param json $out_string 可选
	 * @return array [apply_card_url]
	 */
	public function card_activateurl($template_id = '', $out_string = '')
	{
		$merchant = Db::name('merchant')
			->alias('m')
			->join('merchant_alipay ma', 'm.merchant_id = ma.merchant_id')
			->where('ma.card_id', '=', $template_id)
			->field('m.merchant_id, m.merchant_no, ma.card_id, ma.card_data, ma.app_auth_token')
			->find();
		if(empty($merchant)) {
			return make_return(0, '未获取到商户id');
		}
		$this->merchant = [
			'merchant_id' => $merchant['merchant_id'],
			'merchant_no' => $merchant['merchant_no'],
		];
		$this->merchant_id = $merchant['merchant_id'];
		$this->app_auth_token = $merchant['app_auth_token'];
		$callback = url('/pay/alipay/card_activate_callback', null, null, true);
		$BizContent = [
			'template_id' => $template_id,
			'out_string' => $out_string,
			'callback' => $callback,
		];
		$this->load('alipay.marketing.card.activateurl.apply');
		$res = $this->execute($BizContent, null, $this->app_auth_token);
		$apply_card_url = urldecode(urldecode($res['contents']['apply_card_url']));
		if($res['status'] == 1) {
			return make_return(1, 'ok', ['apply_card_url' => $apply_card_url]);
		} else {
			return make_return(0, '获取投放链接失败,' . $res['message']);
		}
	}

	/**
	 * 会员卡开卡
	 * @param string $template_id
	 * @param string $user_uni_id
	 * @param string $access_token
	 * @param string $card_ext_info 外部卡信息
	 * @param string $member_ext_info 商户会员信息
	 * @return array
	 */
	public function card_open($template_id, $user_uni_id, $access_token, $card_ext_info = [], $member_ext_info = [])
	{
		$BizContent = [
			'out_serial_no' => get_order_number(),
			'card_template_id' => $template_id,
			'card_user_info' => [
				'user_uni_id' => $user_uni_id,
				'user_uni_id_type' => 'UID',
			],
			'card_ext_info' => $card_ext_info,
			'member_ext_info' => $member_ext_info,
		];
		$this->load('alipay.marketing.card.open');
		$res = $this->execute($BizContent, $access_token, $this->app_auth_token);
		if($res['status'] == 1) {
			return make_return(1, 'ok', $res['contents']);
		} else {
			return make_return(0, '会员卡开卡失败,' . $res['message']);
		}
	}

	/**
	 * 会员卡查询
	 * @param string $target_card_no
	 * @param string $target_card_no_type
	 * @return array
	 */
	public function card_query($target_card_no, $target_card_no_type = 'BIZ_CARD')
	{
		$BizContent = [
			'target_card_no' => $target_card_no,
			'target_card_no_type' => $target_card_no_type,
		];
		$this->load('alipay.marketing.card.query');
		$res = $this->execute($BizContent, null, $this->app_auth_token);
		if($res['status'] == 1) {
			return make_return(1, 'ok', $res['contents']);
		} else {
			return make_return(0, '会员卡查询失败,' . $res['message']);
		}
	}

	/**
	 * 会员卡更新
	 * @param string $target_card_no
	 * @param string $target_card_no_type
	 * @param string $level 会员等级
	 * @param string $point 会员积分
	 * @param string $balance 会员余额
	 * @return array
	 */
	public function card_update($target_card_no, $target_card_no_type = 'BIZ_CARD', $level = '', $point = '', $balance = '')
	{
		$mch_user = Db::name('mch_user')->where(['biz_card_no' => $target_card_no, 'merchant_id' => $this->merchant_id])->field('id, card_no, credit, balance, open_datetime, valid_datetime')->find();
		if(empty($mch_user)) {
			return make_return(0, '未获取到会员信息');
		}
		$card_info = [
			'external_card_no' => $mch_user['card_no'],
			'level' => $level ? $level : 'VIP',
			'point' => $point ? $point : $mch_user['credit'],
			'balance' => $balance ? $balance : $mch_user['balance'],
			'open_date' => gsdate('Y-m-d H:i:s', $mch_user['open_datetime']),
			'valid_date' => empty($mch_user['valid_datetime']) ? '2088-01-01 00:00:00' : gsdate('Y-m-d H:i:s', $mch_user['valid_datetime'])
		];
		$BizContent = [
			'occur_time' => gsdate('Y-m-d H:i:s'),
			'card_info' => $card_info,
			'target_card_no' => $target_card_no,
			'target_card_no_type' => $target_card_no_type,
		];
		$this->load('alipay.marketing.card.update');
		$res = $this->execute($BizContent, null, $this->app_auth_token);
		if($res['status'] == 1) {
			return make_return(1, 'ok', $res['contents']);
		} else {
			return make_return(0, '更新会员卡失败,' . $res['message']);
		}
	}

	/**
	 * 查询会员卡表单信息
	 * @param string $template_id 可选
	 * @param string $request_id 可选
	 * @param string $access_token 必须
	 * @return array
	 */
	public function query_card_activateform($template_id, $request_id, $access_token)
	{
		$BizContent = [
			'template_id' => $template_id,
			'request_id' => $request_id,
			'biz_type' => 'MEMBER_CARD',
		];
		$this->load('alipay.marketing.card.activateform.query');
		$res = $this->execute($BizContent, $access_token, $this->app_auth_token);
		if($res['status'] == 1) {
			return make_return(1, 'ok', $res['contents']);
		} else {
			return make_return(0, '查询会员卡表单信息失败,' . $res['message']);
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
			$imageInfo = explode('.', $info['contents']['FileName']);
			$imageName = $imageInfo[0];
			$imageType = $imageInfo[1];
			$imageContent = '@' . file_path($info['contents']['SaveName']);
		}
		$this->load('alipay.offline.material.image.upload');
		$res = $this->execute(null, null, $this->app_auth_token, [
			'ImageName' => $imageName,
			'ImageType' => $imageType,
			'ImageContent' => $imageContent
		]);
		if($res['status'] == 1) {
			return make_return(1, 'ok', [
				'image_id' => $res['contents']['image_id'],
				'image_url' => $res['contents']['image_url'],
				'local_url' => url('/') . preg_replace('/^\//', '', $info['contents']['SaveName'])
			]);
		} else {
			return make_return(0, '上传失败:' . $res['message']);
		}
	}

	/**
	 * 会员卡开卡表单模板配置
	 * @param string $template_id 可选
	 * @param array $fields OPEN_FORM_FIELD_MOBILE|OPEN_FORM_FIELD_NAME|OPEN_FORM_FIELD_GENDER|OPEN_FORM_FIELD_BIRTHDAY|OPEN_FORM_FIELD_BIRTHDAY_WITH_YEAR|OPEN_FORM_FIELD_EMAIL|OPEN_FORM_FIELD_CITY|OPEN_FORM_FIELD_ADDRESS
	 * @return array
	 */
	public function set_card_formtemplate($template_id = '', $fields = [])
	{
		if(empty($template_id)) {
			$template_id = $this->card_data['template_id'];
		}
		if(empty($template_id)) {
			return make_return(0, '未获取到template_id');
		}
		$fields = !empty($fields) ? $fields : [
			'required' => json_encode(['common_fields' => [
				'OPEN_FORM_FIELD_MOBILE',
				'OPEN_FORM_FIELD_NAME',
				'OPEN_FORM_FIELD_GENDER',
				'OPEN_FORM_FIELD_BIRTHDAY_WITH_YEAR',
			]]),
			'optional' => json_encode(['common_fields' => []])
		];
		$BizContent = [
			'fields' => $fields,
			'template_id' => $template_id,
		];
		$this->load('alipay.marketing.card.formtemplate.set');
		$res = $this->execute($BizContent, null, $this->app_auth_token);
		if($res['status'] == 1) {
			return make_return(1, 'ok', $res['contents']);
		} else {
			return make_return(0, '开卡表单模板配置失败,' . $res['message']);
		}
	}

	/**
	 * 查询会员卡模板
	 * @param string $template_id
	 * @return array
	 */
	public function query_card_template($template_id = '')
	{
		if(empty($template_id)) {
			$template_id = $this->card_data['template_id'];
		}
		if(empty($template_id)) {
			return make_return(0, '未获取到template_id');
		}
		$BizContent = [
			'template_id' => $template_id
		];
		$this->load('alipay.marketing.card.template.query');
		$res = $this->execute($BizContent, null, $this->app_auth_token);
		if($res['status'] == 1) {
			return make_return(1, 'ok', $res['contents']);
		} else {
			return make_return(0, '查询会员卡模板失败,' . $res['message']);
		}
	}

	/**
	 * 创建会员卡模板
	 * @param array $option 模板配置 [template_id|card_show_name|logo_id|background_id]
	 * @return array 模板id [template_id]
	 */
	public function create_card_template($option = [])
	{
		$card_id = Db::name('merchant_alipay')->where('merchant_id', '=', $this->merchant_id)->value('card_id');
		if($card_id) {
			return make_return(0, '会员卡模板已经存在');
		}
		$BizContent = [
			'biz_no_suffix_len' => '12',
			'request_id' => get_order_number(),
			'card_type' => 'OUT_MEMBER_CARD',
			'biz_no_prefix' => '',
			'write_off_type' => 'qrcode',
			'template_style_info' => [
				'card_show_name' => $option['card_show_name'],
				'color' => 'rgb(55,112,179)',
				'bg_color' => 'rgb(55,112,179)',
				'logo_id' => $option['logo_id'],
				'background_id' => $option['background_id'],
			],
			'template_benefit_info' => isset($option['template_benefit_info']) ? [$option['template_benefit_info']] : [],
			'column_info_list' => [
				[
					'code' => 'PAYCODE',
					'more_info' => [
						'title' => '扩展信息',
						'url' => 'alipays://platformapi/startapp?appId=20000056',
						'params' => '{}',
					],
					'title' => '付款码',
					'operate_type' => 'openWeb',
					'value' => '打开付款码',
				],
				[
					'code' => 'TELEPHOME',
					'title' => '商家电话',
					'value' => $option['TELEPHOME'],
				],
				[
					'code' => 'BALANCE',
					'title' => '余额',
					'value' => '',
				],
				[
					'code' => 'POINT',
					'title' => '积分',
					'value' => '',
				]
			],
			'field_rule_list' => [
				[
					'field_name' => 'Balance',
					'rule_name' => 'ASSIGN_FROM_REQUEST',
					'rule_value' => 'Balance',
				],
				[
					'field_name' => 'Point',
					'rule_name' => 'ASSIGN_FROM_REQUEST',
					'rule_value' => 'Point',
				]
			]
		];
		$this->load('alipay.marketing.card.template.create');
		$this->request->setNotifyUrl(url('/pay/alipay/card_change_callback', null, null, true));
		$res = $this->execute($BizContent, null, $this->app_auth_token);
		if($res['status'] == 0) {
			return make_return(0, '创建会员卡模板失败,' . $res['message']);
		} else {
			$template_id = $res['contents']['template_id'];
			if(!empty($option['template_id']) && $template_id != $option['template_id']) {
				return make_return(0, '模板接口异常,请联系服务商');
			}
			$BizContent['template_id'] = $template_id;
			$BizContent['biz_no_suffix_len'] = '12';
			ksort($BizContent);
			Db::name('merchant_alipay')->where('app_auth_token', '=', $this->app_auth_token)->update([
				'card_id' => $template_id,
				'card_data' => JSON($BizContent),
			]);
		}
		return $this->set_card_formtemplate($template_id, []);
	}

	/**
	 * 修改会员卡模板
	 * @param array $option 模板配置 [template_id|card_show_name|logo_id|background_id]
	 * @return array 模板id [template_id]
	 */
	public function modify_card_template($option = [])
	{
		if(empty($option['template_id'])) {
			$option['template_id'] = $this->card_data['template_id'];
		}
		if(empty($option['template_id'])) {
			return make_return(0, '未获取到template_id');
		}
		$BizContent = [
			'template_id' => $option['template_id'],
			'request_id' => get_order_number(),
			'biz_no_prefix' => '',
			'write_off_type' => 'qrcode',
			'template_style_info' => [
				'card_show_name' => $option['card_show_name'],
				'color' => 'rgb(55,112,179)',
				'bg_color' => 'rgb(55,112,179)',
				'logo_id' => $option['logo_id'],
				'background_id' => $option['background_id'],
			],
			'template_benefit_info' => isset($option['template_benefit_info']) ? [$option['template_benefit_info']] : [],
			'column_info_list' => [
				[
					'code' => 'PAYCODE',
					'more_info' => [
						'title' => '扩展信息',
						'url' => 'alipays://platformapi/startapp?appId=20000056',
						'params' => '{}',
					],
					'title' => '付款码',
					'operate_type' => 'openWeb',
					'value' => '打开付款码',
				],
				[
					'code' => 'TELEPHOME',
					'title' => '商家电话',
					'value' => $option['TELEPHOME'],
				],
				[
					'code' => 'BALANCE',
					'title' => '余额',
					'value' => '',
				],
				[
					'code' => 'POINT',
					'title' => '积分',
					'value' => '',
				]
			],
			'field_rule_list' => [
				[
					'field_name' => 'Balance',
					'rule_name' => 'ASSIGN_FROM_REQUEST',
					'rule_value' => 'Balance',
				],
				[
					'field_name' => 'Point',
					'rule_name' => 'ASSIGN_FROM_REQUEST',
					'rule_value' => 'Point',
				]
			]
		];
		$this->load('alipay.marketing.card.template.modify');
		$this->request->setNotifyUrl(url('/pay/alipay/card_change_callback', null, null, true));
		$res = $this->execute($BizContent, null, $this->app_auth_token);
		if($res['status'] == 0) {
			return make_return(0, '修改会员卡模板失败,' . $res['message']);
		} else {
			$template_id = $res['contents']['template_id'];
			if(!empty($option['template_id']) && $template_id != $option['template_id']) {
				return make_return(0, '模板接口异常,请联系服务商');
			}
			$BizContent['template_id'] = $template_id;
			$BizContent['biz_no_suffix_len'] = '12';
			ksort($BizContent);
			Db::name('merchant_alipay')->where('app_auth_token', '=', $this->app_auth_token)->update([
				'card_id' => $template_id,
				'card_data' => JSON($BizContent),
			]);
		}
		return $this->set_card_formtemplate($template_id, []);
	}

}

