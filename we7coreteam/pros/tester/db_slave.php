<?php
use Testify\Testify;

require '../framework/bootstrap.inc.php';
require IA_ROOT . '/framework/library/testify/Testify.php';

$tester = new Testify('测试数据库主从配置');
$tester->test('测试是否开启了主从', function() {
	global $_W, $tester;
	$tester->assertEquals($_W['config']['db']['slave_status'], true);
});

load()->classs('db');
if (empty($_W['config']['db']['master'])) {
	$config = array(
		'host' => $_W['config']['db']['host'],
		'username' => $_W['config']['db']['username'],
		'password' => $_W['config']['db']['password'],
		'port' => $_W['config']['db']['port'],
		'database' => $_W['config']['db']['database'],
		'charset' => $_W['config']['db']['charset'],
		'pconnect' => $_W['config']['db']['pconnect'],
		'tablepre' => $_W['config']['db']['tablepre'],
	);
} else {
	$config = $_W['config']['db']['master'];
}
$master = new DB($config);
$slave = new DB($_W['config']['db']['slave']['1']);

$tester->test('测试主库删除，写入', function() {
	global $_W, $tester, $master, $slave;
	
	$master->delete('core_settings', array('key' => 'test_slave'));
	$result = $slave->get('core_settings', array('key' => 'test_slave'));
	$tester->assertEquals($result['value'], '');

	$master->insert('core_settings', array('key' => 'test_slave', 'value' => 'success'), true);
	$result = $slave->get('core_settings', array('key' => 'test_slave'));
	$tester->assertEquals($result['value'], 'success');
});

$tester->test('测试主库删除，写入', function() {
	global $_W, $tester, $master, $slave;
	pdo_insert('core_attachment', array('uniacid' => '888', 'uid' => '888', 'filename' => '', 'attachment' => '', 'type' => 1, 'createtime' => time()));
	$count = pdo_fetch("SELECT id FROM ".tablename('core_attachment') . " ORDER BY id DESC");
	$tester->assertEquals(pdo_insertid(), $count['id']);
	pdo_delete('core_attachment', array('uniacid' => '888'));
});

$tester->run();