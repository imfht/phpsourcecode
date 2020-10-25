<?php

namespace app\pay\controller;

use \think\Db;
use \app\common\Pay;
use \app\common\PayAction;
use \app\common\PayAlipay;
use \app\common\PayWeixin;

class Index
{

	public $merchant = [];
	public $trade = [];

	public $mch_user = [];
	public $open_user_id = '';

	public $errNo = 0;
	public $errMsg = null;

	public function __construct()
	{
		if(!in_array(request()->action(), ['index']) && request()->controller() == basename(str_replace('\\', '/', __CLASS__))) {
			exit('Access Denied');
		}
	}

	/**
	 * 聚合收款码
	 * @param String $qid
	 */
	public function index()
	{
		$value = model('Qrcode')->get_one(['id' => input('param.qid')]);
		if(!$value) {
			return '无效的二维码';
		} else {
			//list($qrc_key, $qrc_val) = explode('=', $qrcode_url);
			if(!$value['store_id'] && !$value['person_id']) {
				$qrcode_url = 'merchant_id=' . $value['merchant_id'];
			} else {
				if(!$value['person_id']) {
					$qrcode_url = 'store_id=' . $value['store_id'];
				} else {
					$qrcode_url = 'person_id=' . $value['person_id'];
				}
			}
			$url = '/';
			if(preg_match('/AlipayClient/', input('server.HTTP_USER_AGENT'))) {
				$url = '/pay/alipay/pay_qrcode';
			} else {
				$url = '/pay/weixin/pay_qrcode';
			}
			$the_url = url($url, ['qid' => $value['id'], 'qrcode_url' => authcode($qrcode_url, 'ENCODE', input('param.qid'))], null, true);
			return \befen\redirect($the_url);
		}
	}

	/**
	 * test
	 * @param
	 */
	public function test()
	{

	}

	/**
	 * 支付接口
	 * @param String $BizContent
	 */
	public static function pay($merchant = [], $BizContent = [])
	{
		//
	}

	/**
	 * 查询接口
	 * @param String $out_trade_no
	 */
	public static function query($merchant = [], $out_trade_no = '')
	{
		$self = self::get_trade($merchant, $out_trade_no);
		$trade = $self->trade;
		if($self->errMsg) {
			return make_return(0, $self->errMsg);
		}
		$gate = !empty($trade['sub_gate']) ? $trade['sub_gate'] : $trade['trade_gate'];
		$res = PayAction::gate($gate)->merchant($merchant)->set_trade($trade)->query($out_trade_no);
		if(http()->get_error($res->message)) {
			return make_return(0, 'RETRY');
		}
		if(empty($trade['sub_gate'])) {
			if($res->status == 0) {
				$trade_status = 'PAYERROR';
			} else {
				if(isset($res->contents->trade_status)) {
					$trade_status = $res->contents->trade_status;
					if($trade_status == 'PAYERROR') {
						$res->status = 0;
					}
					model('\app\common\model\Trade')->_update($out_trade_no, [
						'trade_status' => $trade_status,
						'time_update' => _time(),
						'trade_no' => isset($res->contents->trade_no) ? $res->contents->trade_no : '',
						//'user_id' => isset($res->contents->user_id) ? $res->contents->user_id : '',
						//'unionid' => isset($res->contents->unionid) ? $res->contents->unionid : '',
						//'openid' => isset($res->contents->openid) ? $res->contents->openid : '',
						//'sub_openid' => isset($res->contents->sub_openid) ? $res->contents->sub_openid : '',
					]);
				} else {
					$trade_status = 'USERPAYING';
				}
			}
			if(in_array($trade_status, ['SUCCESS', 'TRADE_SUCCESS'])) {
				if(empty($trade['qrcode_id'])) {
					\think\Queue::push('\app\pay\job\Template@weixin', ['out_trade_no' => $out_trade_no]);
				}
			}
			$res = PayAction::result_filter($res);
		} else {
			switch($trade['sub_gate']) {
				case 'suixing';
					if($res->status == 0) {
						$trade_status = 'PAYERROR';
					} else {
						if(isset($res->contents->tranSts)) {
							if(in_array($res->contents->tranSts, ['CLOSED', 'SUCCESS'])) {
								$trade_status = $res->contents->tranSts;
							} else {
								$trade_status = 'USERPAYING';
							}
							model('\app\common\model\Trade')->_update($out_trade_no, [
								'trade_status' => $trade_status,
								'time_update' => _time(),
							]);
						} else {
							$trade_status = 'USERPAYING';
						}
					}
					if(in_array($trade_status, ['SUCCESS', 'TRADE_SUCCESS'])) {
						if(empty($trade['qrcode_id'])) {
							\think\Queue::push('\app\pay\job\Template@weixin', ['out_trade_no' => $out_trade_no]);
						}
					}
				break;
			}
		}
		$biz_type = input('post.biz_type/s');
		$card_no = input('post.card_no/s');
		$open_user_id = model('\app\common\model\MchUser')->get_uid(['buyer_id', 'mini_openid']);
		if(empty($open_user_id) && !empty($res->contents->user_id)) {
			$user_id = $res->contents->user_id;
			$open_user_id = $res->contents->user_id;
		}
		if(empty($open_user_id) && !empty($res->contents->unionid)) {
			$unionid = $res->contents->unionid;
			$open_user_id = $res->contents->unionid;
		}
		if(empty($open_user_id) && !empty($res->contents->openid)) {
			$openid = $res->contents->openid;
			$open_user_id = $res->contents->openid;
		}
		if(empty($open_user_id) && !empty($res->contents->sub_openid)) {
			$sub_openid = $res->contents->sub_openid;
			$open_user_id = $res->contents->sub_openid;
		}
		$mch_user = [];
		if($biz_type == 'charge') {
			if(!empty($card_no)) {
				$mch_user = model('\app\common\model\MchUser')->get_user(['card_no' => $card_no, 'merchant_id' => $merchant['merchant_id']]);
			} else {
				$mch_user = model('\app\common\model\MchUser')->get_user(['user_id|mini_openid' => $open_user_id, 'merchant_id' => $merchant['merchant_id']]);
			}
			if(!empty($mch_user) && $trade_status == 'SUCCESS') {
				$mch_user = model('\app\common\model\MchUser')->charge($merchant, $mch_user, $trade['total_amount'], $trade['out_trade_no']);
			}
		} else {
			$order = Db::name('order')->where('out_trade_no', '=', $out_trade_no)->find();
			if($trade_status == 'SUCCESS' && $order && $order['status'] != 3) {
				Db::name('order')->where('out_trade_no', '=', $out_trade_no)->update([
					'status' => 3,
					'time_update' => _time(),
				]);
				Db::name('order_detail')->where('order_id', '=', $order['order_id'])->update([
					'time_update' => _time(),
				]);
			}
		}
		$res->message = $trade_status;
		$contents = $res->contents;
		$res->contents = new \stdClass;
		$res->contents->out_trade_no = $out_trade_no;
		$res->contents->trade_status = $trade_status;
		if(!empty($openid)) {
			$res->contents->openid = $openid;
		}
		if(!empty($user_id)) {
			$res->contents->user_id = $user_id;
		}
		foreach($trade as $key => $val) {
			if(in_array($key, ['trade_gate', 'trade_type', 'total_amount'])) {
				$res->contents->$key = $val;
			}
		}
		if(!empty($mch_user)) {
			if(!empty($mch_user['charge_amount'])) {
				$res->contents->charge_amount = number_string($mch_user['charge_amount']);
				unset($mch_user['charge_amount']);
			}
			$res->contents->card_info = $mch_user;
		}
		//$res->contents->trade = $trade;
		PayAction::log(JSON($res));
		return ToArray($res);
	}

	/**
	 * 关闭接口
	 * @param String $out_trade_no
	 */
	public static function close($merchant = [], $out_trade_no = '')
	{
		$self = self::get_trade($merchant, $out_trade_no);
		$trade = $self->trade;
		if($self->errMsg) {
			return make_return(0, $self->errMsg);
		}
		$res = PayAction::gate($trade['trade_gate'])->merchant($merchant)->set_trade($trade)->close($out_trade_no);
		$res = PayAction::result_filter($res);
		if($res->status == 1) {
			model('\app\common\model\Trade')->_update($out_trade_no, ['trade_status' => 'CLOSED']);
			//Pay::trade_profit($out_trade_no);
		}
		//$res->contents->trade = $trade;
		return ToArray($res);
	}

	/**
	 * 撤销接口
	 * @param String $out_trade_no
	 */
	public static function cancel($merchant = [], $out_trade_no = '')
	{
		$self = self::get_trade($merchant, $out_trade_no);
		$trade = $self->trade;
		if($self->errMsg) {
			return make_return(0, $self->errMsg);
		}
		$res = PayAction::gate($trade['trade_gate'])->merchant($merchant)->set_trade($trade)->cancel($out_trade_no);
		$res = PayAction::result_filter($res);
		if($res->status == 1) {
			model('\app\common\model\Trade')->_delete($out_trade_no);
			//Pay::trade_profit($out_trade_no);
		}
		//$res->contents->trade = $trade;
		return ToArray($res);
	}

	/**
	 * 退款接口
	 * @param String $out_trade_no 必填 商户交易号
	 * @param String $out_refund_no 可选 退款交易号
	 * @param Float $refund_amount 可选 退款金额
	 * @param String $refund_reason 可选 退款原因
	 * @param Int $order_id 可选 订单id
	 * @param Int $order_detail_id 可选 订单详情id
	 */
	public static function refund($merchant = [], $out_trade_no = '', $refund_detail = [])
	{
		$time_create = Db::name('trade')->where('out_trade_no', '=', $out_trade_no)->value('time_create');
		if(!empty($time_create) && (_time() - $time_create) > (7 * 24 * 60 * 60)) {
			return make_return(0, '交易已结束');
		}
		$order = Db::name('order')->where('out_trade_no', '=', $out_trade_no)->find();
		if(empty($order)) {
			return self::trade_refund($merchant, $out_trade_no, $refund_detail);
		} else {
			if($order['merchant_id'] != $merchant['merchant_id']) {
				return make_return(0, '非法操作');
			}
			return self::order_refund($merchant, $out_trade_no, $refund_detail);
		}
	}

	public static function trade_refund($merchant = [], $out_trade_no = '', $refund_detail = [])
	{
		$self = self::get_trade($merchant, $out_trade_no);
		$trade = $self->trade;
		if($self->errMsg) {
			return make_return(0, $self->errMsg);
		}
		$refund_amount = !empty($refund_detail['refund_amount']) ? $refund_detail['refund_amount'] : $trade['total_amount'];
		$refund_reason = !empty($refund_detail['refund_reason']) ? $refund_detail['refund_reason'] : '';
		$out_refund_no = !empty($refund_detail['out_refund_no']) ? $refund_detail['out_refund_no'] : preg_replace('/\d{2}$/', '', get_order_number('TK'));
		$order_id = !empty($refund_detail['order_id']) ? $refund_detail['order_id'] : 0;
		$order_detail_id = !empty($refund_detail['order_detail_id']) ? $refund_detail['order_detail_id'] : 0;
		if(0 != Db::name('mch_bill')->where('out_trade_no', '=', $out_trade_no)->count()) {
			return make_return(0, '充值订单无法退款');
		}
		if(!in_array($trade['trade_status'], ['SUCCESS', 'TRADE_SUCCESS'])) {
			return make_return(0, '交易状态错误');
		}
		$total_refund = Db::name('refund')->where('out_trade_no', '=', $out_trade_no)->sum('refund_amount');
		if($trade['total_amount'] < ($total_refund + $refund_amount)) {
			return make_return(0, '退款金额错误');
		}
		$gate = !empty($trade['sub_gate']) ? $trade['sub_gate'] : $trade['trade_gate'];
		$res = PayAction::gate($gate)->merchant($merchant)->set_trade($trade)->refund($out_trade_no, [
			'out_refund_no' => $out_refund_no,
			'refund_amount' => $refund_amount,
			'refund_reason' => $refund_reason,
		]);
		if($res->status == 0) {
			return make_return(0, $res->message, $res->contents);
		}
		Db::name('refund')->insert([
			'merchant_id' => $trade['merchant_id'],
			'store_id' => $trade['store_id'],
			'person_id' => $trade['person_id'],
			'order_id' => $order_id ,
			'order_detail_id' => $order_detail_id,
			'check_status' => 1,
			'refund_status' => -1,
			'out_trade_no' => $out_trade_no,
			'out_refund_no' => $out_refund_no,
			'refund_amount' => $refund_amount,
			'refund_fee' => 100 * $refund_amount,
			'refund_reason' => $refund_reason,
			'time_create' => _time(),
		]);
		\think\Queue::push('\app\pay\job\PayTrade@refund', ['out_trade_no' => $out_trade_no, 'out_refund_no' => $out_refund_no]);
		if(empty($trade['sub_gate'])) {
			$res = PayAction::result_filter($res);
			if((isset($res->contents->code) && $res->contents->code == 10000) || (isset($res->contents->result_code) && $res->contents->result_code == 'SUCCESS')) {
				if($trade['total_amount'] == ($total_refund + $refund_amount)) {
					model('\app\common\model\Trade')->_update($out_trade_no, ['trade_status' => 'CLOSED']);
				}
				\think\Queue::push('\app\pay\job\Template@weixin', ['out_refund_no' => $out_refund_no]);
				return make_return(1, 'ok', $res->contents);
			} else {
				return make_return(0, $res->message, $res->contents);
			}
		} else {
			switch($trade['sub_gate']) {
				case 'suixing';
					if(isset($res->contents->bizCode) && $res->contents->bizCode === '0000') {
						if($trade['total_amount'] == ($total_refund + $refund_amount)) {
							model('\app\common\model\Trade')->_update($out_trade_no, ['trade_status' => 'CLOSED']);
						}
						\think\Queue::push('\app\pay\job\Template@weixin', ['out_refund_no' => $out_refund_no]);
						return make_return(1, 'ok', $res->contents);
					} else {
						return make_return(0, $res->message, $res->contents);
					}
				break;
			}
		}
	}

	public static function order_refund($merchant = [], $out_trade_no = '', $refund_detail = [])
	{
		$refund_amount = !empty($refund_detail['refund_amount']) ? $refund_detail['refund_amount'] : 0;
		$refund_reason = !empty($refund_detail['refund_reason']) ? $refund_detail['refund_reason'] : '';
		$out_refund_no = !empty($refund_detail['out_refund_no']) ? $refund_detail['out_refund_no'] : preg_replace('/\d{2}$/', '', get_order_number('TK'));
		$order_id = !empty($refund_detail['order_id']) ? $refund_detail['order_id'] : 0;
		$order_detail_id = !empty($refund_detail['order_detail_id']) ? $refund_detail['order_detail_id'] : 0;
		if(empty($out_trade_no)) {
			return make_return(0, '缺少参数 [out_trade_no]');
		}
		if(empty($order_id)) {
			return make_return(0, '缺少参数 [order_id]');
		}
		$order = Db::name('order')->where('out_trade_no', '=', $out_trade_no)->where('order_id', '=', $order_id)->find();
		if(empty($order)) {
			return make_return(0, '订单不存在');
		} else {
			if($order['merchant_id'] != $merchant['merchant_id']) {
				return make_return(0, '非法操作');
			}
		}
		if($order['status'] == 0) {
			return make_return(0, '订单未支付');
		}
		if($order_detail_id) {
			$order_detail = Db::name('order_detail')->where('order_id', '=', $order_id)->where('id', '=', $order_detail_id)->find();
			if(!$order_detail) {
				return make_return(0, '订单详情不存在');	
			} else {
				if($order_detail['is_refund']) {
					return make_return(0, '此订单已退款');
				}
			}
		}
		if($order_detail_id == 0) {
			if($refund_amount == 0) {
				$refund_amount = $order['pay_price'];
			} else {
				if($refund_amount > $order['pay_price']) {
					return make_return(0, '退款金额错误');
				}
			}
		} else {
			if($refund_amount == 0) {
				$refund_amount = $order_detail['num'] * $order_detail['price'];
			} else {
				if($refund_amount > $order_detail['num'] * $order_detail['price']) {
					return make_return(0, '退款金额错误');
				}
			}
		}
		//计算总退款金额
		$refund = $order['refund'] + $refund_amount;
		if($refund > $order['pay_price']) {
			return make_return(0, '退款金额超出实付金额');
		}
		switch($order['trade_type']) {
			//现金
			case 'cash':
				//写入退款
				Db::name('refund')->insert([
					'merchant_id' => $order['merchant_id'],
					'store_id' => $order['store_id'],
					'person_id' => $order['person_id'],
					'order_id' => $order_id,
					'order_detail_id' => $order_detail_id,
					'check_status' => 1,
					'refund_status' => 1,
					'out_trade_no' => $out_trade_no,
					'out_refund_no' => $out_refund_no,
					'refund_amount' => $refund_amount,
					'refund_fee' => 100 * $refund_amount,
					'refund_reason' => $refund_reason,
					'time_create' => _time(),
					'time_update' => _time(),
				]);
				//写入退款
				Db::name('order')->where('order_id', '=', $order_id)->update([
					'refund' => $refund,
					'time_update' => _time(),
				]);
				if($order_detail_id == 0) {
					//全额退款
					Db::name('order_detail')->where('order_id', '=', $order_id)->update([
						'is_refund' => 1,
						'time_update' => _time(),
					]);
				} else {
					//部分退款
					Db::name('order_detail')->where('order_id', '=', $order_id)->where('id', '=', $order_detail_id)->update([
						'is_refund' => 1,
						'time_update' => _time(),
					]);
				}
				return make_return(1, 'ok');
			break;
			//会员卡
			case 'card':

			break;
			//网关支付
			default:
				$self = self::get_trade($merchant, $out_trade_no);
				$trade = $self->trade;
				if($self->errMsg) {
					return make_return(0, $self->errMsg);
				}
				$res = self::trade_refund($merchant, $out_trade_no, $refund_detail);
				if($res['status']) {
					//写入退款
					Db::name('order')->where('order_id', '=', $order_id)->update([
						'refund' => $refund,
						'time_update' => _time(),
					]);
					if($order_detail_id == 0) {
						//全额退款
						Db::name('order_detail')->where('order_id', '=', $order_id)->update([
							'is_refund' => 1,
							'time_update' => _time(),
						]);
					} else {
						//部分退款
						Db::name('order_detail')->where('order_id', '=', $order_id)->where('id', '=', $order_detail_id)->update([
							'is_refund' => 1,
							'time_update' => _time(),
						]);
					}
				}
				return $res;
			break;
		}
	}

	/**
	 * 退款查询接口
	 * @param String $out_trade_no
	 * @param String $out_refund_no
	 */
	public static function query_refund($merchant = [], $out_trade_no = '', $out_refund_no = '')
	{
		$self = self::get_trade($merchant, $out_trade_no);
		$trade = $self->trade;
		if($self->errMsg) {
			return make_return(0, $self->errMsg);
		}
		$res = PayAction::gate($trade['trade_gate'])->merchant($merchant)->query_refund($out_trade_no, $out_refund_no);
		if($res->status == 1) {
			switch($trade['trade_gate']) {
				case 'alipay':
					if(empty($res->contents->refund_status) || $res->contents->refund_status == 'REFUND_SUCCESS') {
						model('\app\common\model\Refund')->_update($out_refund_no, [
							'refund_status' => 1,
							'time_update' => _time(),
						]);
					}
				break;
				case 'weixin':
					if(isset($res->contents->return_code) && $res->contents->return_code == 'SUCCESS') {
						for($i=0; $i<10; $i++) {
							$refund_status = 'refund_status_' . $i;
							if(isset($res->contents->$refund_status)) {
								switch($res->contents->$refund_status) {
									//异常
									case 'CHANGE':
										model('\app\common\model\Refund')->_update($out_refund_no, [
											'refund_status' => 0,
											'time_update' => _time(),
										]);
									break;
									//成功
									case 'SUCCESS':
										model('\app\common\model\Refund')->_update($out_refund_no, [
											'refund_status' => 1,
											'time_update' => _time(),
										]);
									break;
									//处理
									case 'PROCESSING':
										model('\app\common\model\Refund')->_update($out_refund_no, [
											'refund_status' => -1,
											'time_update' => _time(),
										]);
									break;
									//关闭
									case 'REFUNDCLOSE':
										model('\app\common\model\Refund')->_delete($out_refund_no);
									break;
								}
								break;
							}
						}
					}
				break;
				default:
					//other_gates
				break;
			}
		}
		//$res->contents->trade = $trade;
		//$res->contents->refund = Db::name('refund')->where('out_trade_no', '=', $out_trade_no)->select();
		return ToArray($res);
	}

	/**
	 * 获取交易
	 * @param String $out_trade_no
	 */
	public static function get_trade($merchant = [], $out_trade_no = null)
	{
		$self = new self();
		if(empty($out_trade_no)) {
			$out_trade_no = input('post.out_trade_no/s');
		}
		if(empty($out_trade_no)) {
			!$self->errMsg && $self->errMsg = '缺少参数 [out_trade_no]';
			return $self;
		}
		$self->trade = PayAction::query_trade($out_trade_no);
		if(!$self->trade) {
			!$self->errMsg && $self->errMsg = '交易不存在';
			return $self;
		}
		if(!$merchant) {
			!$self->errMsg && $self->errMsg = '未获取到商户信息';
			return $self;
		}
		if(!isset(Pay::GATES[$self->trade['trade_gate']])) {
			!$self->errMsg && $self->errMsg = '未获取到支付通道';
			return $self;
		}
		$self->errMsg = self::check_merchant($self->trade['trade_gate'], $merchant);
		if($self->errMsg) {
			return $self;
		}
		if($self->trade['merchant_id'] != $merchant['merchant_id']) {
			!$self->errMsg && $self->errMsg = '交易不存在 [merchant_id]';
			return $self;
		}
		/* Person
		if(!empty($merchant['store_person'])) {
			if($self->trade['person_id'] != $merchant['store_person']['person_id']) {
				!$self->errMsg && $self->errMsg = '交易不存在 [person_id]';
				return $self;
			}
		}
		*/
		/* Device
		if(!empty($merchant['store_device'])) {
			if($self->trade['device_id'] != $merchant['store_device']['device_id']) {
				!$self->errMsg && $self->errMsg = '交易不存在 [device_id]';
				return $self;
			}
		}
		*/
		return $self;
	}

	/**
	 * 商户状态
	 * @param String $trade_gate
	 * @param Array $PayMerchant
	 */
	public static function check_merchant($trade_gate, $PayMerchant = [])
	{
		if(!isset(Pay::GATES[$trade_gate])) {
			return '未获取到支付通道';
		}
		if(!$PayMerchant || !array_filter($PayMerchant)) {
			return '未获取到商户信息';
		}
		if(!$PayMerchant['status']) {
			return '当前商户不可用';
		}
		if(isset($PayMerchant['store']) && !$PayMerchant['store']['store_status']) {
			return '当前门店不可用';
		}
		if(isset($PayMerchant['store_person']) && !$PayMerchant['store_person']['status']) {
			return '当前员工不可用';
		}
		if(isset($PayMerchant['store_device']) && !$PayMerchant['store_device']['status']) {
			return '当前设备不可用';
		}
		if(0 == number($PayMerchant['trade_rates'])) {
			return '商户费率未配置';
		}
		if($trade_gate == 'weixin') {
			if(!$PayMerchant['status_weixin']) {
				return '微信通道不可用';
			}
			if(empty($PayMerchant['sub_mch_id']) && empty($PayMerchant['gate_weixin'])) {
				return '微信通道未配置';
			}
		}
		if($trade_gate == 'alipay') {
			if(!$PayMerchant['status_alipay']) {
				return '支付宝通道不可用';
			}
			if(empty($PayMerchant['app_auth_token']) && empty($PayMerchant['gate_alipay'])) {
				return '支付宝通道未配置';
			}
		}
		return null;
	}

}

