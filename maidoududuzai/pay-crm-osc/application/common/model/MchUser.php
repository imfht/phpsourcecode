<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class MchUser extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	public static function make_card()
	{
		do {
			$card_no = ToString(mt_rand(1000, 9999)) . ToString(mt_rand(1000, 9999)) . ToString(mt_rand(1000, 9999));
		} while(0 != Db::name('mch_user')->where('card_no', '=', $card_no)->count());
		return $card_no;
	}

	public static function get_uid($field = [])
	{
		$open_user_id = null;
		$fields = ['user_id', 'buyer_id', 'unionid', 'openid', 'sub_openid', 'mini_openid'];
		if(!is_array($field)) {
			if(in_array($field, $fields)) {
				$open_user_id = input('post.' . $field . '/s');
			}
		} else {
			if(!empty($field)) {
				foreach($field as $value) {
					if(in_array($value, $fields)) {
						$open_user_id = input('post.' . $value . '/s');
						if(!empty($open_user_id)) {
							break;
						}
					}
				}
			} else {
				foreach($fields as $value) {
					if(in_array($value, $fields)) {
						$open_user_id = input('post.' . $value . '/s');
						if(!empty($open_user_id)) {
							break;
						}
					}
				}
			}
		}
		return $open_user_id;
	}

	public static function get_user($fields = [])
	{
		$where = [];
		foreach($fields as $key => $value) {
			$where[$key] = ['=', $value];
		}
		return Db::name('mch_user')->where($where)->field('id, card_no, user_id, unionid, openid, sub_openid, mini_openid, balance, credit, phone, username, nickname, sex, biz_card_no, UserCardCode')->find();
	}

	public static function charge($merchant = [], $mch_user = [], $total_amount, $out_trade_no = '')
	{
		$mch_charge = Db::name('mch_charge')->where('merchant_id', '=', $merchant['merchant_id'])->where('status', '=', '1')->order('pay_amount', 'DESC')->select();
		$charge_amount = $total_amount;
		foreach($mch_charge as $key => $value) {
			if($total_amount >= $value['pay_amount']) {
				$total_amount = $total_amount + $value['send_amount'];
				$charge_amount = $charge_amount + $value['send_amount'];
				break;
			}
		}
		$mch_user['balance'] = $mch_user['balance'] + $total_amount;
		$mch_user['charge_amount'] = $charge_amount;
		Db::name('mch_user')->where('id', '=', $mch_user['id'])->update([
			'balance' => $mch_user['balance'],
		]);
		if(!Db::name('mch_bill')->where('out_trade_no', '=', $out_trade_no)->count()) {
			MchBill::_insert($merchant, [
				'uid' => $mch_user['id'],
				'out_trade_no' => $out_trade_no,
				'status' => 1,
				'balance' => $mch_user['balance'],
				'balance_do' => '+',
				'balance_amount' => $total_amount,
				'credit' => $mch_user['credit'],
				'credit_do' => '',
				'credit_amount' => 0,
				'action' => '充值',
				'description' => '会员充值',
				'time_create' => _time(),
			]);
		}
		\think\Queue::push('\app\pay\job\MchUser@card', [
			'merchant_id' => $merchant['merchant_id'],
			'mch_uid' => $mch_user['id'],
		]);
		return $mch_user;
	}

	public static function payment($merchant = [], $mch_user = [], $total_amount, $out_trade_no = '')
	{
		$mch_user['balance'] = $mch_user['balance'] - $total_amount;
		$mch_user['credit'] = $mch_user['credit'] + floor($total_amount);
		Db::name('mch_user')->where('id', '=', $mch_user['id'])->update([
			'balance' => $mch_user['balance'],
			'credit' => $mch_user['credit'],
		]);
		MchBill::_insert($merchant, [
			'uid' => $mch_user['id'],
			'out_trade_no' => $out_trade_no,
			'status' => 1,
			'balance' => $mch_user['balance'],
			'balance_do' => '-',
			'balance_amount' => $total_amount,
			'credit' => $mch_user['credit'],
			'credit_do' => '+',
			'credit_amount' => floor($total_amount),
			'action' => '消费',
			'description' => '会员消费',
			'time_create' => _time(),
		]);
		\think\Queue::push('\app\pay\job\MchUser@card', [
			'merchant_id' => $merchant['merchant_id'],
			'mch_uid' => $mch_user['id'],
		]);
		return $mch_user;
	}

	public static function mch_user_update($merchant = [], $mch_user = [], $user_info = [], $out_trade_no = '')
	{
		if($user_info['balance_do'] == '-') {
			$mch_user['balance'] = $mch_user['balance'] - $user_info['balance_amount'];
		} else {
			$mch_user['balance'] = $mch_user['balance'] + $user_info['balance_amount'];
		}
		if($user_info['credit_do'] == '-') {
			$mch_user['credit'] = $mch_user['credit'] - $user_info['credit_amount'];
		} else {
			$mch_user['credit'] = $mch_user['credit'] + $user_info['credit_amount'];
		}
		Db::name('mch_user')->where('id', '=', $mch_user['id'])->update([
			'balance' => $mch_user['balance'],
			'credit' => $mch_user['credit'],
		]);
		MchBill::_insert($merchant, [
			'uid' => $mch_user['id'],
			'out_trade_no' => $out_trade_no,
			'status' => 1,
			'balance' => $mch_user['balance'],
			'balance_do' => $user_info['balance_do'],
			'balance_amount' => $user_info['balance_amount'],
			'credit' => $mch_user['credit'],
			'credit_do' => $user_info['credit_do'],
			'credit_amount' => $user_info['credit_amount'],
			'action' => '调账',
			'description' => '会员调账',
			'time_create' => _time(),
		]);
		\think\Queue::push('\app\pay\job\MchUser@card', [
			'merchant_id' => $merchant['merchant_id'],
			'mch_uid' => $mch_user['id'],
		]);
		return $mch_user;
	}

}

