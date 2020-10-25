<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class Trade extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	public static function gates()
	{
		return \app\common\Pay::GATES;
	}

	public static function getGate($gate = null)
	{
		$text = \app\common\Pay::GATES;
		if(!isset($text[$gate])) {
			if(is_null($gate)) {
				return $text;
			} else {
				return $gate;
			}
		} else {
			return $text[$gate];
		}
	}

	public static $list_status = [
		'SUCCESS' => '支付成功',
		'CLOSED' => '交易关闭',
		'REFUND' => '已退款',
		'NOTPAY' => '未支付',
		'PAYERROR' => '支付失败',
		'USERPAYING' => '用户支付中',
	];

	public static function getStatus($status = null)
	{
		if(!isset(self::$list_status[$status])) {
			if(is_null($status)) {
				return self::$list_status;
			} else {
				return $status;
			}
		} else {
			return self::$list_status[$status];
		}
	}

	public static $list_type = [
		'qr_code' => '收款码',
		'bar_code' => '扫码',
		'face_code' => '刷脸',
	];

	public static function get_type($type = null)
	{
		if(!isset(self::$list_type[$type])) {
			if(is_null($type)) {
				return self::$list_type;
			} else {
				return $type;
			}
		} else {
			return self::$list_type[$type];
		}
	}

	//backup
	public static function _query($out_trade_no)
	{
		return Db::name('trade')->where('out_trade_no', '=', $out_trade_no)->find();
	}

	public static function _insert($merchant = [], $trade_gate = '', $trade_type = '', $total_amount = 0, $out_trade_no = null)
	{
		if(empty($out_trade_no)) {
			$out_trade_no = preg_replace('/\d{1}$/', '', get_order_number('P'));
		}
		$merchant['agent_id'] = !empty($merchant['agent_id']) ? $merchant['agent_id'] : 0;
		$merchant['merchant_id'] = !empty($merchant['merchant_id']) ? $merchant['merchant_id'] : 0;
		$merchant['store_id'] = !empty($merchant['store_id']) ? $merchant['store_id'] : 0;
		$merchant['device_id'] = !empty($merchant['device_id']) ? $merchant['device_id'] : 0;
		$merchant['person_id'] = !empty($merchant['person_id']) ? $merchant['person_id'] : 0;
		$merchant['qrcode_id'] = !empty($merchant['qrcode_id']) ? $merchant['qrcode_id'] : 0;
		if(isset($merchant['store'])) {
			if($merchant['store_id'] == 0) {
				$merchant['store_id'] = $merchant['store']['store_id'];
			}
		}
		if(isset($merchant['store_device'])) {
			if($merchant['store_id'] == 0) {
				$merchant['store_id'] = $merchant['store_device']['store_id'];
			}
			if($merchant['device_id'] == 0) {
				$merchant['device_id'] = $merchant['store_device']['device_id'];
			}
		}
		if(isset($merchant['store_person'])) {
			if($merchant['store_id'] == 0) {
				$merchant['store_id'] = $merchant['store_person']['store_id'];
			}
			if($merchant['person_id'] == 0) {
				$merchant['person_id'] = $merchant['store_person']['person_id'];
			}
		}
		$trade = (new self)->allowField(true)->save([
			'agent_id' => $merchant['agent_id'],
			'merchant_id' => $merchant['merchant_id'],
			'store_id' => $merchant['store_id'],
			'device_id' => $merchant['device_id'],
			'person_id' => $merchant['person_id'],
			'qrcode_id' => $merchant['qrcode_id'],
			'trade_gate' => $trade_gate,
			'trade_type' => $trade_type,
			'out_trade_no' => $out_trade_no,
			'total_amount' => $total_amount,
			'total_fee' => $total_amount * 100,
			'agent_rates' => $merchant['agent_rates'],
			'trade_rates' => $merchant['trade_rates'],
			'time_create' => _time(),
		]);
		\think\Queue::push('\app\pay\job\PayTrade@query', ['out_trade_no' => $out_trade_no]);
		if(!$trade) {
			return null;
		} else {
			return (string)$out_trade_no;
		}
	}

	public static function _delete($out_trade_no = '')
	{
		return self::destroy(['out_trade_no' => $out_trade_no]);
	}

	public static function _update($out_trade_no = '', $out_trade_detail = [])
	{
		return (new self)->allowField(true)->save($out_trade_detail, ['out_trade_no' => $out_trade_no]);
	}

}

