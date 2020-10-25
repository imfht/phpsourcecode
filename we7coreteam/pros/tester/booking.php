<?php
use Testify\Testify;

require '../framework/bootstrap.inc.php';
require IA_ROOT . '/framework/library/testify/Testify.php';

load()->func('communication');

$tester = new Testify('测试订阅关注和取消');
$tester->test('测试关注', function(){
	global $tester;
	$message = <<<EOF
	<xml><ToUserName><![CDATA[gh_bfca3f781d27]]></ToUserName>
	<FromUserName><![CDATA[oxNH0s4pcDkHAEazHq7FopRWF7123renchao]]></FromUserName>
	<CreateTime>1457174967</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[subscribe]]></Event>
	<EventKey><![CDATA[]]></EventKey>
	</xml>
EOF;
	$response = api($message);
	$tester->assertNotEquals($response, 'Check Sign Fail.');
	
	load()->model('mc');
	$fans = mc_fansinfo('oxNH0s4pcDkHAEazHq7FopRWF7123renchao');
	$tester->assertEquals('oxNH0s4pcDkHAEazHq7FopRWF7123renchao', $fans['openid']);
	
	$member = mc_fetch($fans['uid']);
	$tester->assertEquals($fans['uid'], $member['uid']);
	
	$tester->uid = $member['uid'];
	
	$count = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('mc_mapping_fans')." WHERE openid = '{$fans['openid']}'");
	$tester->assertEquals(1, $count);
});

$tester->test('测试已关注后取消', function(){
	global $tester;
	$message = <<<EOF
	<xml><ToUserName><![CDATA[gh_bfca3f781d27]]></ToUserName>
	<FromUserName><![CDATA[oxNH0s4pcDkHAEazHq7FopRWF7123renchao]]></FromUserName>
	<CreateTime>1457174967</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[unsubscribe]]></Event>
	<EventKey><![CDATA[]]></EventKey>
	</xml>
EOF;
	
	$response = api($message);
	$tester->assertNotEquals($response, 'Check Sign Fail.');

	load()->model('mc');
	$fans = mc_fansinfo('oxNH0s4pcDkHAEazHq7FopRWF7123renchao');
	$tester->assertEquals('', $fans['openid']);

	$member = mc_fetch($tester->uid);
	$tester->assertEquals(0, intval($member['uid']));

	$count = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('mc_mapping_fans')." WHERE openid = 'oxNH0s4pcDkHAEazHq7FopRWF7123renchao'");
	$tester->assertEquals(0, $count);
});

$tester->test('测试有积分时取消关注', function(){
	global $tester;
	$message = <<<EOF
	<xml><ToUserName><![CDATA[gh_bfca3f781d27]]></ToUserName>
	<FromUserName><![CDATA[oxNH0s4pcDkHAEazHq7FopRWF7123renchao]]></FromUserName>
	<CreateTime>1457174967</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[subscribe]]></Event>
	<EventKey><![CDATA[]]></EventKey>
	</xml>
EOF;
	
	$response = api($message);
	$tester->assertNotEquals($response, 'Check Sign Fail.');
	
	load()->model('mc');
	$fans = mc_fansinfo('oxNH0s4pcDkHAEazHq7FopRWF7123renchao');
	$member = mc_fetch($fans['uid']);
	$tester->assertEquals($fans['uid'], $member['uid']);
	
	mc_credit_update($fans['uid'], 'credit1', '10');
	$member = mc_fetch($fans['uid']);
	$tester->assertEquals(10, $member['credit1']);
	
	$message = <<<EOF
	<xml><ToUserName><![CDATA[gh_bfca3f781d27]]></ToUserName>
	<FromUserName><![CDATA[oxNH0s4pcDkHAEazHq7FopRWF7123renchao]]></FromUserName>
	<CreateTime>1457174967</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[unsubscribe]]></Event>
	<EventKey><![CDATA[]]></EventKey>
	</xml>
EOF;
	$response = api($message);
	$fans = mc_fansinfo('oxNH0s4pcDkHAEazHq7FopRWF7123renchao');
	$tester->assertEquals('oxNH0s4pcDkHAEazHq7FopRWF7123renchao', $fans['openid']);
	
	$member = mc_fetch($fans['uid']);
	$tester->assertEquals($fans['uid'], $member['uid']);
	$tester->assertEquals(10, $member['credit1']);
	
	
	mc_credit_update($fans['uid'], 'credit1', -10);
	$member = mc_fetch($fans['uid']);
	$tester->assertEquals(0, $member['credti1']);
	
	$message = <<<EOF
	<xml><ToUserName><![CDATA[gh_bfca3f781d27]]></ToUserName>
	<FromUserName><![CDATA[oxNH0s4pcDkHAEazHq7FopRWF7123renchao]]></FromUserName>
	<CreateTime>1457174967</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[unsubscribe]]></Event>
	<EventKey><![CDATA[]]></EventKey>
	</xml>
EOF;
	$response = api($message);
});

$tester->run();

function api($message) {
	$item = array();
	$item['token'] = 'Ck925gx0qx0K4k4G2xGAK42Y49Y6XB9Z';
	$item['apiurl'] = 'http://pro.we7.cc/api.php?id=269&';
	$sign = array(
		'timestamp' => TIMESTAMP,
		'nonce' => random(10, 1),
	);
	$signkey = array($item['token'], $sign['timestamp'], $sign['nonce']);
	sort($signkey, SORT_STRING);
	$sign['signature'] = sha1(implode($signkey));
	$item['apiurl'] .= http_build_query($sign, '', '&');
	$response = ihttp_request($item['apiurl'], $message, array('CURLOPT_HTTPHEADER' => array('Content-Type: text/xml; charset=utf-8')));
	return $response['content'];
}