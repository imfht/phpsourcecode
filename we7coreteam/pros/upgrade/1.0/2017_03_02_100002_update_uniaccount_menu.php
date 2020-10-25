<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 16:37.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateUniaccountMenu {
	public function up() {
		if (!pdo_fieldexists('news_reply', 'media_id')) {
			pdo_query('ALTER TABLE '.tablename('news_reply')." ADD `media_id` int(10) NOT NULL DEFAULT '0';");
		}

		//自定义菜单为重名title添加后缀（如：title为‘默认菜单’的有两个重名，其id分别为1、2，执行该语句后title分别为：‘默认菜单_1’、‘默认菜单_2’）
		//如果title值为空时,判断type值为1的话，title变为'默认菜单_自己当前id',type值为3时,title值变为'个性化菜单_当前自己id'
		//如果title值不为空,则将出现重复的值改为'当前自己的title_自己的id';
//		pdo_query('UPDATE '.tablename('uni_account_menus')." SET title = if(title = '',if(type=1, concat('默然菜单_',id),concat('个性化菜单_',id)),concat(title,'_',id)) WHERE  `title` IN (SELECT a.title FROM (SELECT `title` FROM ".tablename('uni_account_menus').' GROUP BY `title` having count(*) >1 ) a)');
	}
}
