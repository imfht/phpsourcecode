**PApiTest**

基于[simpletest](http://sourceforge.net/projects/simpletest/)单元测试库，更加方便http API接口单元测试。目前只对JSON数据格式作处理。

![Alt text](./test.jpg)

使用例子
----------
<p>
<pre>
require_once dirname(__FILE__) . '/../component/PApi.php';
class Demo extends UnitTestCase {
	function testIplookup(){
		$url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=218.4.255.255';
		$browser = new SimpleBrowser();
		$browser->get($url);
		PApi::showJson('手机号码归属地查询api接口'.$url, $browser->getContent());
		$this->assertEqual(200, $browser->getResponseCode());
	}
	
	function testWeather(){
		$url = 'http://www.weather.com.cn/data/cityinfo/101280101.html';
		$browser = new SimpleBrowser();
		$browser->get($url);
		PApi::showJson('广州天气接口'.$url, $browser->getContent());
		$this->assertEqual(200, $browser->getResponseCode());
	}
}
</pre>
</p>
----------

