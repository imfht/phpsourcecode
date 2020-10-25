<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class Store extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	public static $list_status = [1 => '正常', 0 => '关闭'];

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

	public static function get_one_store($merchant_id)
	{
		$store_id = Db::name('store')->where('merchant_id', '=', $merchant_id)->order('store_id', 'ASC')->value('store_id');
		if(!$store_id) {
			$merchant = Db::name('merchant')->where('merchant_id', '=', $merchant_id)->field('agent_id, merchant_id, per_name, per_phone')->find();
			$store = new self;
			$store->allowField(true)->save([
				'store_name' => '默认门店',
				'store_status' => 1,
				'agent_id' => $merchant['agent_id'],
				'merchant_id' => $merchant['merchant_id'],
				'per_name' => $merchant['per_name'],
				'per_phone' => $merchant['per_phone'],
				'time_create' => _time(),
			]);
			$store_id = $store->getLastInsID();
			/* HjSync */
			//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@store', ['store_id' => $store_id]);
			/* HjSync */
		}
		return $store_id;
	}

}

