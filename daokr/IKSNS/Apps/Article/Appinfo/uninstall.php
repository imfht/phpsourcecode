<?php
/**
 * 卸载文章应用
 * @author 小麦  <160780470@qq.com>
 * @version IKPHP1.5
 */
if(!defined('SITE_PATH')) exit();
// 数据库表前缀
$db_prefix = C('DB_PREFIX');
// 卸载数据SQL数组
$sql = array(
	// 数据
	"DROP TABLE IF EXISTS `{$db_prefix}article`;",
	"DROP TABLE IF EXISTS `{$db_prefix}article_item`;",
	"DROP TABLE IF EXISTS `{$db_prefix}article_cate`;",
	"DROP TABLE IF EXISTS `{$db_prefix}article_channel`;",	
	"DROP TABLE IF EXISTS `{$db_prefix}article_comment`;",
	"DROP TABLE IF EXISTS `{$db_prefix}article_robots`;",
);
// 删除文件
// 执行SQL
foreach($sql as $v) {
	D('')->execute($v);
}