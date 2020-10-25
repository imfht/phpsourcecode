<?php
namespace We7\V155;
defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1504331449
 * @version 1.5.5
 */

class CreateModuleStore {

	/**
	 *  执行更新
	 */
	public function up() {
		 if (IMS_FAMILY != 'x') {
			 return true;
		 }
		$store_menu_exist = pdo_get('core_menu', array('group_name' => 'frame', 'permission_name' => 'store', 'is_system' => 1));
		if (empty($store_menu_exist)) {
			pdo_insert('core_menu', array('group_name' => 'frame', 'type' => 'url', 'is_display' => 1, 'is_system' => 1, 'permission_name' => 'store'));
		}
		$store_module_exist = pdo_get('modules', array('name' => 'store'));
		if (empty($store_module_exist)) {
			$data = array(
				'name' => 'store',
				'type' => 'business',
				'title' => '站内商城',
				'title_initial' => 'Z',
				'version' => '1.0',
				'ability' => '站内商城',
				'description' => '站内商城',
				'author' => 'we7',
				'issystem' => '1',
				'wxapp_support' => '1',
				'app_support' => '2',
			);
			pdo_insert('modules', $data);
		}
		setting_save(array('status' => STATUS_ON), 'store');

		pdo_query("
		CREATE TABLE IF NOT EXISTS `ims_site_store_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL COMMENT '商品类型：1、模块；2、公众号；3、小程序',
  `title` varchar(100) NOT NULL COMMENT '商品名称',
  `module` varchar(50) NOT NULL COMMENT '模块名',
  `account_num` int(10) NOT NULL COMMENT '公众号数量',
  `wxapp_num` int(10) NOT NULL COMMENT '小程序数量',
  `price` decimal(10,2) NOT NULL COMMENT '价格',
  `unit` varchar(15) NOT NULL COMMENT '价格单位',
  `slide` varchar(1000) NOT NULL COMMENT '幻灯片',
  `category_id` int(10) NOT NULL COMMENT '商品分类ID',
  `title_initial` varchar(1) NOT NULL COMMENT '商品名称拼音首字母，用于查询',
  `status` tinyint(1) NOT NULL COMMENT '上下架状态：1、上架；0、下架',
  `createtime` int(10) NOT NULL,
  `synopsis` varchar(255) NOT NULL COMMENT '商品简介',
  `description` text NOT NULL COMMENT '商品详情',
  PRIMARY KEY (`id`),
  KEY `module` (`module`),
  KEY `category_id` (`category_id`),
  KEY `price` (`price`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='站内商城商品表';
CREATE TABLE IF NOT EXISTS `ims_site_store_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderid` varchar(28) NOT NULL COMMENT '订单号',
  `goodsid` int(10) NOT NULL COMMENT '商品ID',
  `duration` int(10) NOT NULL COMMENT '购买时长',
  `buyer` varchar(50) NOT NULL,
  `buyerid` int(10) NOT NULL,
  `amount` decimal(10,2) NOT NULL COMMENT '订单总额',
  `type` tinyint(1) NOT NULL COMMENT '订单状态：1、已下单；2、已删除；3、交易成功',
  `changeprice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否更改价格：1、是；0、否',
  `createtime` int(10) NOT NULL,
  `uniacid` int(10) NOT NULL COMMENT '购买商品添加到的公众号',
  PRIMARY KEY (`id`),
  KEY `goodid` (`goodsid`),
  KEY `buyerid` (`buyerid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='站内商城订单表';
		");
	}

	/**
	 *  回滚更新
	 */
	public function down() {


	}
}
