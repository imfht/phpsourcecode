<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class Order extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	public static $list_status = [0 => '未支付', 1 => '待发货', 2 => '已发货', 3 => '已完成'];

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
		'cash' => '现金',
		'card' => '余额',
		'online' => '在线支付',
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

	public static function _insert()
	{

	}

	public static function _delete($order_id = '')
	{
		return self::destroy(['order_id' => $order_id]);
	}

	public static function _update($order_id = '', $order_detail = [])
	{
		return (new self)->allowField(true)->save($order_detail, ['order_id' => $order_id]);
	}

}

