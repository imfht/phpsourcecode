<?php

namespace app\pay\controller;

use \think\Db;
use \app\common\Pay;
use \app\common\PayAction;

class Cash extends Auth
{

	public $merchant = [];

	public $mch_user = [];

	public $card_no = [];

	public $open_user_id = null;

	public function __construct()
	{

		parent::__construct();

		$this->mch_user = [];
		$this->card_no = input('post.card_no/s');
		$this->open_user_id = model('MchUser')->get_uid(['buyer_id', 'mini_openid']);
		if(!empty($this->merchant['merchant_id'])) {
			if(!empty($this->card_no)) {
				$this->mch_user = model('MchUser')->get_user(['card_no' => $this->card_no, 'merchant_id' => $this->merchant['merchant_id']]);
			} else {
				$this->mch_user = model('MchUser')->get_user(['user_id|mini_openid' => $this->open_user_id, 'merchant_id' => $this->merchant['merchant_id']]);
			}
		}

	}

	/**
	 * 商户交易号
	 */
	public function index($prefix = 'P')
	{
		PayAction::log(request()->method() . ' ' . request()->url());
		PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		if(!preg_match('/^[A-Z]{1,2}$/', $prefix)) {
			return make_json(0, '前缀格式错误');
		}
		$length = strlen($prefix);
		$out_trade_no = preg_replace('/\d{' . $length . '}$/', '', get_order_number($prefix));
		$contents = ['out_trade_no' => $out_trade_no];
		PayAction::log(JSON($contents));
		return make_json(1, 'ok', $contents);
	}

	/**
	 * 支付接口
	 * @param String $trade_type 必填 支付方式 face_code刷脸，bar_code扫码，card余额
	 * @param String $biz_type 必填 业务类型 normal支付，charge充值
	 * @param String $out_trade_no 必填 商户交易号
	 * @param Float $total_amount 必填 金额，单位元
	 * @param String $auth_code 可选 支付授权码
	 * @param String $openid 可选 微信用户openid，会员充值、余额支付必填
	 * @param String $buyer_id 可选 支付宝用户user_id，会员充值、余额支付必填
	 */
	public function pay()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$trade_type = input('post.trade_type/s');
		/* 兼容老版本开始 */
		if(empty($trade_type)) {
			$code_type = input('post.code_type/s');
			if($code_type == 'F') {
				$trade_type = 'face_code';
			} else {
				$trade_type = 'bar_code';
			}
		}
		/* 兼容老版本结束 */
		$biz_type = input('post.biz_type/s');
		/* 兼容老版本开始 */
		if(empty($biz_type)) {
			$biz_type = input('post.pay_type/s');
		}
		/* 兼容老版本结束 */
		$out_trade_no = input('post.out_trade_no/s');
		$total_amount = input('post.total_amount/f', 0);
		if(empty($trade_type)) {
			return make_json(0, '缺少参数 [trade_type]');
		}
		if(empty($biz_type)) {
			return make_json(0, '缺少参数 [biz_type]');
		}
		if(empty($out_trade_no)) {
			return make_json(0, '缺少参数 [out_trade_no]');
		}
		if($total_amount == 0) {
			return make_json(0, '缺少参数 [total_amount]');
		}
		if(!in_array($trade_type, ['face_code', 'bar_code', 'card'])) {
			return make_json(0, '非法参数 [trade_type]');
		}
		if(!in_array($biz_type, ['normal', 'charge'])) {
			return make_json(0, '非法参数 [biz_type]');
		}
		if(0 != Db::name('trade')->where('out_trade_no', '=', $out_trade_no)->count()) {
			return make_json(0, '商户交易号已存在！');
		}
		if($trade_type == 'card' || $biz_type == 'charge') {
			if(empty($this->mch_user)) {
				return make_json(0, '无法识别会员');
			}
			if($trade_type == 'card') {
				$res = make_return('0', 'ok', []);
				if($this->mch_user) {
					if($this->mch_user['balance'] < $total_amount) {
						$res['message'] = '会员卡余额不足';
					} else {
						$res['status'] = '1';
						$res['message'] = 'card';
						$this->mch_user = model('MchUser')->payment($this->merchant, $this->mch_user, $total_amount, $out_trade_no);
					}
					$res['contents']['card_info'] = ToString($this->mch_user);
					return JSON($res);
				}
			}
		}
		$auth_code = input('post.auth_code/s');
		/* 兼容老版本开始 */
		if(empty($auth_code)) {
			$auth_code = input('post.face_code/s');
		}
		/* 兼容老版本结束 */
		if(empty($auth_code)) {
			return make_json(0, '缺少参数 [auth_code]');
		}
		$pay_client = Pay::client($auth_code);
		if(!$pay_client) {
			return make_json(0, '无法识别付款码');
		}
		$this->errMsg = Index::check_merchant($pay_client, $this->merchant);
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		model('Trade')->_insert($this->merchant, $pay_client, $trade_type, $total_amount, $out_trade_no);
		$sub_gate = $this->merchant['gate_' . $pay_client];
		if(!empty($sub_gate)) {
			if(empty($this->merchant[$sub_gate])) {
				return make_json(0, '支付通道未配置');
			}
			model('Trade')->_update($out_trade_no, ['sub_gate' => $sub_gate]);
		}
		$gate = $sub_gate ? $sub_gate : $pay_client;
		$PostData = [
			'out_trade_no' => $out_trade_no,
			'auth_code' => $auth_code,
			'total_amount' => $total_amount,
			'subject' => $this->merchant['merchant_name'],
			'body' => $this->merchant['merchant_name'],
			'spbill_create_ip' => get_ipaddr(),
		];
		$res = PayAction::gate($gate)->merchant($this->merchant)->pay($PostData);
		if(empty($sub_gate)) {
			$res = PayAction::result_filter($res);
		}
		if($res->status == 0) {
			if((isset($res->contents->code) && $res->contents->code == 10003) || (isset($res->contents->err_code) && $res->contents->err_code == 'USERPAYING')) {
				$res->status = 1;
				$res->message = 'query';
			}
		} else {
			$res->message = 'query';
		}
		if(isset($res->contents->bizCode)) {
			if($res->contents->bizCode === '0000' && isset($res->contents->transactionId)) {
				$res->status = 1;
				$res->message = 'query';
				model('Trade')->_update($out_trade_no, [
					'trade_no' => $res->contents->transactionId
				]);
			}
			if($res->contents->bizCode === '2002' || (isset($res->contents->tranSts) && $res->contents->tranSts === 'PAYING')) {
				$res->status = 1;
				$res->message = 'query';
			}
		}
		$contents = ['out_trade_no' => $out_trade_no];
		$res->contents = $res->contents ? $res->contents : [];
		foreach($res->contents as $key => $value) {
			if(!isset($contents[$key])) {
				$contents[$key] = $value;
			}
		}
		$res->contents = $contents;
		return make_json($res->status, $res->message, $res->contents);
	}

	/**
	 * 查询接口
	 * @param String $out_trade_no
	 */
	public function query()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		return JSON(Index::query($this->merchant, input('post.out_trade_no/s')));
	}

	/**
	 * 关闭接口
	 * @param String $out_trade_no
	 */
	public function close()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		//return JSON(Index::close($this->merchant, input('post.out_trade_no/s')));
	}

	/**
	 * 撤销接口
	 * @param String $out_trade_no
	 */
	public function cancel()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		//return JSON(Index::cancel($this->merchant, input('post.out_trade_no/s')));
	}

	/**
	 * 退款接口
	 * @param String $out_trade_no
	 */
	public function refund()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$order_id = Db::name('order')->where('out_trade_no', '=', input('post.out_trade_no/s'))->value('order_id');
		return JSON(Index::refund($this->merchant, input('post.out_trade_no/s'), [
			'out_refund_no' => input('post.out_refund_no/s'),
			'refund_amount' => input('post.refund_amount/f', 0),
			'refund_reason' => input('post.refund_reason/s'),
			'order_id' => $order_id,
			'order_detail_id' => input('post.order_detail_id/d', 0),
		]));
	}

	/**
	 * 广告接口
	 * @param String $SN
	 * @return Json Array {type:"image", time:"5", item:["item.jpg"], video:"video.mp4"}
	 */
	public function slider()
	{
		$value = Db::name('store_device')->where('status', '=', '1')->where('SN', '=', input('post.SN/s'))->find();
		if(!$value) {
			$item = [url('/', null, null, true) . 'uploads/test/cash_00.jpg?t=' . gsdate('Y-m-d-H')];
			return make_json(1, 'ok', [
				'type' => 'image',
				'time' => 5,
				'item' => $item,
			]);
		} else {
			$ads = json_decode($value['ads']);
			if(empty($ads)) {
				$ads = new \stdClass;
			}
			if(empty($ads->type) || !in_array($ads->type, ['image', 'video'])) {
				$ads->type = 'image';
			}
			switch($ads->type) {
				case 'image':
					if(empty($ads->item)) {
						$ads->type = 'image';
						$ads->item = [url('/', null, null, true) . 'uploads/test/cash_' . $value['trade_gate'] . '.jpg?t=' . gsdate('Y-m-d-H')];
					}
				break;
				case 'video':
					if(empty($ads->video)) {
						$ads->type = 'image';
						$ads->item = [url('/', null, null, true) . 'uploads/test/cash_' . $value['trade_gate'] . '.jpg?t=' . gsdate('Y-m-d-H')];
					}
				break;
			}
			return make_json(1, 'ok', $ads);
		}
	}

	/**
	 * 交易列表
	 * @param Int $page
	 * @param String $trade_gate
	 * @param String $time_create `2011-01-02 ~ 2012-02-03`
	 */
	public function order()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$where = [];
		if($this->person['manager']) {
			$where['t.store_id'] = ['=', $this->person['store_id']];
		} else {
			$where['t.person_id'] = ['=', $this->person['person_id']];
		}
		$where['t.trade_status'] = ['IN', ['CLOSED', 'SUCCESS', 'TRADE_CLOSED', 'TRADE_SUCCESS']];
		if(input('post.trade_gate')) {
			$where['trade_gate'] = ['IN', input('post.trade_gate')];
		}
		if(input('post.trade_type')) {
			$where['trade_type'] = ['IN', input('post.trade_type')];
		}
		if(input('post.time_create')) {
			$time_create_range = explode('~', input('post.time_create'));
			$time_create_range = array_map(function($v){
				return gstime(trim($v));
			}, $time_create_range);
			$time_create_range[1] += 86400;
			$where['t.time_create'] = ['BETWEEN TIME', $time_create_range];
		}
		$object = Db::name('trade')
			->alias('t')
			->join('store s', 's.store_id = t.store_id', 'LEFT')
			->where($where)
			->order('trade_id', 'DESC')
			->field('t.*')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$last_page = $array['last_page'];
		$current_page = $array['current_page'];
		foreach($list as $k => $v) {
			foreach($v as $key => $val) {
				if(!in_array($key, ['store_id', 'person_id', 'trade_gate', 'trade_type', 'out_trade_no', 'total_amount', 'trade_status', 'time_create'])) {
					unset($list[$k][$key]);
				}
				if($key == 'trade_status') {
					$list[$k][$key] = preg_replace('/^TRADE_/', '', $val);
				}
			}
		}
		return make_json(1, 'ok', [
			'total' => $array['total'],
			'list' => $list,
			'per_page' => $array['per_page'],
			'last_page' => $array['last_page'],
			'current_page' => $array['current_page'],
		]);
	}

}

