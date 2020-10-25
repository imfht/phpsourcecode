<?php
/*
 *
 * home.GoodsOrderList  产品订单商品列表 
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和软件定制
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com>
 * @version ：1.0
 * @link ：http://www.07fly.top
 */	
class WxGoodsOrderList extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	private $shop='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
		
	}
	
	//根据订单号查询订单商品弄表
	public function goods_order_list($order_id){
		if(empty($order_id)) $order_id=0;
		$sql	="select o.*,g.defaultpic from fly_goods_order_list as o left join fly_goods as g on o.goods_id=g.goods_id where o.order_id='$order_id'";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;		
		
	}
	
	
}//
?>