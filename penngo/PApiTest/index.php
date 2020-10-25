<?php



require_once 'component/PApi.php';
class Demo extends UnitTestCase {

	//
	function testIplookup(){
	$browser = new SimpleBrowser();
	$browser->get("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=218.4.255.255");


	PApi::showJson("手机号码归属地查询api接口", $browser->getContent());

	$this->assertEqual(200, $browser->getResponseCode());
	// 		$args = $response->body->args;
	// 		$this->assertEqual("Mark", $args->name);
	// 		$this->assertEqual("thefosk", $args->nick);
	}


	function testWeather(){
	$browser = new SimpleBrowser();
	$browser->get("http://www.weather.com.cn/data/cityinfo/101280101.html");
		//echo $browser->getContent();
	PApi::showJson("广州天气接口", $browser->getContent());
	$this->assertEqual(200, $browser->getResponseCode());
	// 		$args = $response->body->args;
	// 		$this->assertEqual("Mark", $args->name);
	// 		$this->assertEqual("thefosk", $args->nick);
	}
}