<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class Goods extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	/**
	 * 获取商品/Sku图片
	 * @param [type] $goods_id
	 * @param array $attr [大份,微辣]
	 */
	public static function getPic($goods_id, $attr = []) {
		$pic = self::where('goods_id', $goods_id)->value('cover_pic');
		if(!empty($attr)) {
			$sku = self::getSku($goods_id, $attr);
			if(!empty($sku['pic'])) {
				$pic = $sku['pic'];
			}
		}
		return $pic;
	}

	/**
	 * 获取指定Sku信息
	 * @param [type] $goods_id
	 * @param [type] $your_attr [大份,微辣]
	 */
	public static function getSku($goods_id, $your_attr) {
		$match_sku = [];
		$your_attr = implode(',', $your_attr);
		$sku_list = self::where('goods_id', $goods_id)->value('attr');
		$sku_list = json_decode($sku_list, true);
		foreach($sku_list as $sku) {
			$attr_list = $sku['attr_list'];
			$my_attr = array_column($attr_list, 'name');
			$my_attr = implode(',', $my_attr);
			if($my_attr == $your_attr) {
				$match_sku = $sku;
				break;
			}
		}
		return $match_sku;
	}
}

