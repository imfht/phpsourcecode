<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class MchBill extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	public static function _insert($merchant = [], $data = [])
	{
		$merchant['merchant_id'] = !empty($merchant['merchant_id']) ? $merchant['merchant_id'] : 0;
		$merchant['store_id'] = !empty($merchant['store_id']) ? $merchant['store_id'] : 0;
		$merchant['person_id'] = !empty($merchant['person_id']) ? $merchant['person_id'] : 0;
		$merchant['device_id'] = !empty($merchant['device_id']) ? $merchant['device_id'] : 0;
		if(isset($merchant['store'])) {
			if($merchant['store_id'] == 0) {
				$merchant['store_id'] = $merchant['store']['store_id'];
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
		if(isset($merchant['store_device'])) {
			if($merchant['store_id'] == 0) {
				$merchant['store_id'] = $merchant['store_device']['store_id'];
			}
			if($merchant['device_id'] == 0) {
				$merchant['device_id'] = $merchant['store_device']['device_id'];
			}
		}
		foreach($merchant as $key => $val) {
			if(in_array($key, ['merchant_id', 'store_id', 'person_id', 'device_id'])) {
				$data[$key] = $val;
			}
		}
		return (new self)->allowField(true)->save($data);
	}

	public static function _delete($id)
	{
		return self::destroy(['id' => $id]);
	}

	public static function _update($id, $data = [])
	{
		return (new self)->allowField(true)->save($data, ['id' => $id]);
	}

}

