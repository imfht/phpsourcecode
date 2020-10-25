<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class Refund extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	public static $list_check_status = [-1 => '审核中', 0 => '失败', 1 => '成功'];

	public static function getCheckStatus($status = null)
	{
		if(!isset(self::$list_check_status[$status])) {
			if(is_null($status)) {
				return self::$list_check_status;
			} else {
				return $status;
			}
		} else {
			return self::$list_check_status[$status];
		}
	}

	public static $list_refund_status = [-1 => '退款中', 0 => '失败', 1 => '成功'];

	public static function getRefundStatus($status = null)
	{
		if(!isset(self::$list_refund_status[$status])) {
			if(is_null($status)) {
				return self::$list_refund_status;
			} else {
				return $status;
			}
		} else {
			return self::$list_refund_status[$status];
		}
	}

	public static function _insert($trade = [], $refund_amount = 0, $refund_reason = '', $out_refund_no = null)
	{
		if(empty($out_refund_no)) {
			$out_refund_no = preg_replace('/\d{2}$/', '', get_order_number('TK'));
		}
		$refund = (new self)->allowField(true)->save([
			'merchant_id' => $trade['merchant_id'],
			'store_id' => $trade['store_id'],
			'person_id' => $trade['person_id'],
			'check_status' => -1,
			'refund_status' => -1,
			'out_trade_no' => $trade['out_trade_no'],
			'out_refund_no' => $out_refund_no,
			'refund_amount' => $refund_amount,
			'refund_fee' => 100 * $refund_amount,
			'refund_reason' => $refund_reason,
			'time_create' => _time(),
		]);
		//Job Queue
		//\think\Queue::push('\app\pay\job\PayTrade@refund', ['out_refund_no' => $out_refund_no]);
		if(!$refund) {
			return null;
		} else {
			return (string)$out_refund_no;
		}
	}

	public static function _delete($out_refund_no = '')
	{
		return self::destroy(['out_refund_no' => $out_refund_no]);
	}

	public static function _update($out_refund_no = '', $out_refund_detail = [])
	{
		return (new self)->allowField(true)->save($out_refund_detail, ['out_refund_no' => $out_refund_no]);
	}

}

