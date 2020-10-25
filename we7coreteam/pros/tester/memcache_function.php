<?php
use Testify\Testify;

require '../framework/bootstrap.inc.php';
require IA_ROOT . '/framework/library/testify/Testify.php';

$tester = new Testify('测试memcache函数持久化');
$tester->test('测试是否开启了memcache', function() {
	global $_W, $tester;
	$tester->assertEquals($_W['config']['setting']['cache'], 'memcache');
});
$tester->test('测试写入', function(){
	global $tester;
	cache_write('test1', 'test1');
	$memcachevalue = cache_read('test1');
	$mysqlvalue = pdo_fetchcolumn("SELECT value FROM ".tablename('core_cache')." WHERE `key` = 'test1'");
	$mysqlvalue = unserialize($mysqlvalue);
	
	$tester->assertEquals($memcachevalue, $mysqlvalue);
});

$tester->test('测试读取', function(){
	global $tester;
	cache_write('test1', 'test1');
	cache_write('test2', 'test2');
	$memcachevalue = cache_read('test1');
	$tester->assertEquals($memcachevalue, 'test1');
	
	$mysqlvalue = pdo_fetchcolumn("SELECT value FROM ".tablename('core_cache')." WHERE `key` = 'test1'");
	$mysqlvalue = unserialize($mysqlvalue);
	$tester->assertEquals($memcachevalue, 'test1');
});

$tester->test('测试memcache数据丢失后从mysql中读取', function(){
	global $tester;
	cache_write('test1', 'test1');
	cache_write('test2', 'test2');
	
	$memcache = cache_memcache();
	$memcache->delete(cache_prefix('test1'));
	$memcachevalue = $memcache->get(cache_prefix('test1'));
	$tester->assertEquals($memcachevalue, '');
	
	$memcachevalue = cache_read('test1');
	$tester->assertEquals($memcachevalue, 'test1');
	
	$memcachevalue = $memcache->get(cache_prefix('test1'));
	$tester->assertEquals($memcachevalue, 'test1');
});
	
$tester->test('测试删除memcahe数据', function(){
	global $tester;
	cache_write('test1', 'test1');
	cache_write('test2', 'test2');
	
	cache_delete('test1');
	$memcachevalue = cache_read('test1');
	$tester->assertEquals($memcachevalue, '');
	
	$mysqlvalue = pdo_fetchcolumn("SELECT value FROM ".tablename('core_cache')." WHERE `key` = 'test1'");
	$mysqlvalue = unserialize($mysqlvalue);
	$tester->assertEquals($mysqlvalue, '');
	
	$memcache = cache_memcache();
	$memcache->delete(cache_prefix('test2'));
	cache_delete('test2');
	$memcachevalue = cache_read('test2');
	$tester->assertEquals($memcachevalue, '');
});

$tester->test('测试清空所有数据', function(){
	global $tester;
	cache_write('test1', 'test1');
	cache_write('test2', 'test2');
	$memcachevalue = cache_read('test1');
	$tester->assertEquals($memcachevalue, 'test1');
	cache_clean();
	$memcachevalue = cache_read('test1');
	$tester->assertEquals($memcachevalue, '');
	
	$mysqlcount = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('core_cache'));
	$tester->assertEquals($mysqlcount, '0');
});
	

$tester->run();