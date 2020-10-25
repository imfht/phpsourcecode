<?php
require_once dirname(__FILE__) . '/../component/PApi.php';
class Demo extends UnitTestCase {
	function testGet(){
		$url = 'http://localhost/papitest/test/api/get.php';
		$browser = new SimpleBrowser();
		$parame = array('id'=>1);
		$browser->get($url, $parame);
		PApi::showJson('GET api接口测试'.$url, $browser->getContent());
		$this->assertEqual(200, $browser->getResponseCode());
	}
	
	function testPost(){
		$url = 'http://localhost/papitest/test/api/post.php';
		$browser = new SimpleBrowser();
		$parame = array('id'=>1);
		$browser->post($url, $parame);
		PApi::showJson('POST api接口测试'.$url, $browser->getContent());
		$this->assertEqual(200, $browser->getResponseCode());
	}
// 	function testUpload(){
// 	    $url = 'http://localhost/papitest/test/api/upload.php';
// 	    $browser = new SimpleBrowser();
// 	    $parame = array(
// 	        'id'=>1,
// // 	        'img' => '@' . dirname(__FILE__) . "/api/test.jpg"
// 	    );
// 	    $parame = new SimpleMultipartEncoding($parame, true);
// 	    echo dirname(__FILE__) . '/api/test.jpg'."<br/>";
// 	    $browser->post($url, $parame);  // , 'application/octet-stream'
// 	    echo $browser->getContent();
// 	    PApi::showJson('UPLOAD api接口测试'.$url, $browser->getContent());
// 	    $this->assertEqual(200, $browser->getResponseCode());
// 	}
}