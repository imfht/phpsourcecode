<?php

namespace Addons\Third\Controller;

use Addons\Third\Controller\BaseController;

include ('simple_html_dom.php');
header ( "Content-type: text/html; charset=utf-8" );
class APIController extends BaseController {
	public function lists() {
		$this->model = get_model_by_id ( 34 );
		parent::common_lists ( $this->model );
	}
	public function restext($content) {
		$xmlstring = "
	<xml>
	<MsgType><![CDATA[text]]></MsgType>
	<Content><![CDATA[{$content}]]></Content>
	</xml>
	";
		echo $xmlstring;
	}
	
	// 这里可以添加站内的一些常用插件或在这做一些接口中转，如快递查询等
	public function fanyi() {
		$keyword = $_REQUEST ['keyword'];
		
		$tranurl = "http://openapi.baidu.com/public/2.0/bmt/translate?client_id=Vw1d6QeeKfn5Zye9Zmyec75W&q={$keyword}&from=auto&to=auto"; // 百度翻译地址
		$transtr = file_get_contents ( $tranurl ); // 读入文件
		$transon = json_decode ( $transtr ); // json解析
		                                // print_r($transon);
		$contentStr = $transon->trans_result [0]->dst; // 读取翻译内容
		                                              // echo $contentStr;
		return $this->restext ( $contentStr );
	}
	public function getjoke() {
		$strurl = "http://apiwei.sinaapp.com/joke/?appkey=trialuser";
		$fa = file_get_contents ( $strurl );
		$strjson = json_decode ( $fa );
		
		return $this->restext ( $fa );
	}
	public function kuaidi() {
		$keyword = $_REQUEST ['keyword'];
		$strurl = "http://apiwei.sinaapp.com/expressauto/?appkey=trialuser&number={$keyword}";
		$fa = file_get_contents ( $strurl );
		$strjson = json_decode ( $fa );
		// $contentStr = $strjson->response;
		return $this->restext ( $strjson );
	}
	public function getWeather() {
		$keyword = $_REQUEST ['keyword'];
		$url = "http://api.map.baidu.com/telematics/v2/weather?location={$keyword}&ak=1a3cde429f38434f1811a75e1a90310c";
		$fa = file_get_contents ( $url );
		$f = simplexml_load_string ( $fa );
		$city = $f->currentCity;
		$da1 = $f->results->result [0]->date;
		$da2 = $f->results->result [1]->date;
		$da3 = $f->results->result [2]->date;
		$w1 = $f->results->result [0]->weather;
		$w2 = $f->results->result [1]->weather;
		$w3 = $f->results->result [2]->weather;
		$p1 = $f->results->result [0]->wind;
		$p2 = $f->results->result [1]->wind;
		$p3 = $f->results->result [2]->wind;
		$q1 = $f->results->result [0]->temperature;
		$q2 = $f->results->result [1]->temperature;
		$q3 = $f->results->result [2]->temperature;
		$d1 = $cityname . $da1 . $w1 . $p1 . $q1;
		$d2 = $cityname . $da2 . $w2 . $p2 . $q2;
		$d3 = $cityname . $da3 . $w3 . $p3 . $q3;
		$msg = <<<str
         $d1
         $d2
         $d3
str;
		return $this->restext ( $msg );
	}
	public function suanming() {
		$keyword = $_REQUEST ['keyword'];
		$strurl = "http://apiwei.sinaapp.com/fortune/?appkey=trailuser&name={$keyword}";
		$fa = file_get_contents ( $strurl );
		$strjson = json_decode ( $fa );
		
		return $this->restext ( $strjson );
	}
	// function kuaidi(){
	// $keyword = $_REQUEST['keyword'];
	// $strurl="http://apiwei.sinaapp.com/expressauto/?appkey=trialuser&number={$keyword}";
	// $fa=file_get_contents($strurl);
	// $strjson=json_decode($fa);
	// $contentStr = $strjson->response;
	// return $strjson;
	// }
	public function getCoachInfo() {
		$keyword = $_REQUEST ['keyword'];
		$divide = "到";
		$dividePos = strpos ( $keyword, $divide );
		$divideLen = strlen ( $divide );
		$from = substr ( $keyword, 0, $dividePos );
		$to = substr ( $keyword, $dividePos + $divideLen, strlen ( $keyword ) - $dividePos - $divideLen );
		
		try {
			$url = "http://www.keyunzhan.com/zhandaozhan_search.php?find_type=2&shousuo=2&startstate=" . urlencode ( $from ) . "&endstate=" . urlencode ( $to );
			$html_coach = file_get_html ( $url );
			if (! isset ( $html_coach )) {
				$html_coach->clear ();
				return "检索出错！\n如果经常这样，请发送9到QQ空间给我们留言。";
			}
			
			$result = "";
			foreach ( $html_coach->find ( 'div[class="car_left_detail01_rows"]' ) as $singleCoach ) {
				$start = $singleCoach->find ( 'div[class="car_left_detail01_rows_col01"]', 0 )->plaintext;
				$station = $singleCoach->find ( 'div[class="car_left_detail01_rows_col01"]', 1 )->plaintext;
				$end = $singleCoach->find ( 'div[class="car_left_detail01_rows_col01"]', 2 )->plaintext;
				$time = $singleCoach->find ( 'div[class="car_left_detail01_rows_col02"]', 0 )->plaintext;
				$time2 = trim ( str_replace ( "+显示全部", "", $time ) );
				$time3 = preg_replace ( "/[\n\s]+/is", " ", $time2 );
				$distance = $singleCoach->find ( 'div[class="car_left_detail01_rows_col02"]', 1 )->plaintext;
				$price = $singleCoach->find ( 'div[class="car_left_detail01_rows_col03"]', 0 )->plaintext;
				
				$result .= "出发：" . $start . $station . "\n" . "目的：" . $end . "\n" . "时间：" . $time3 . "\n" . "里程：" . $distance . "\n" . "票价：" . $price . "\n\n";
			}
			
			$html_coach->clear ();
			return $this->restext ( trim ( $result ) );
		} catch ( Exception $e ) {
		}
	}
}

