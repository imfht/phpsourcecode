<?php
namespace Home\Controller;
use Think\Controller;
/*
$resJsonArray['jsonData'][0]['aqi']
	//空气质量，仅限国内部分城市，国际城市无此字段
   			"aqi": "30", //空气质量指数
            "co": "0", //一氧化碳1小时平均值(ug/m³)
            "no2": "10", //二氧化氮1小时平均值(ug/m³)
            "o3": "94", //臭氧1小时平均值(ug/m³)
            "pm10": "10", //PM10 1小时平均值(ug/m³)
            "pm25": "7", //PM2.5 1小时平均值(ug/m³)
            "qlty": "优", //空气质量类别
            "so2": "3" //二氧化硫1小时平均值(ug/m³)
*/
/*
$resJsonArray['jsonData'][0]['basic']
	//基本信息
		"city": "北京",  //城市名称
        "cnty": "中国",  //国家
        "id": "CN101010100",  //城市ID，参见http://www.heweather.com/documents/cn-city-list
        "lat": "39.904000",  //城市维度
        "lon": "116.391000",  //城市经度
        "update": {  //更新时间
            "loc": "2015-07-02 14:44", //当地时间
            "utc": "2015-07-02 06:46"  //UTC时间
        }
*/
/*
$resJsonArray['jsonData'][0]['now']
	//实况天气
        "cond": { //天气状况
            "code": "100", //天气状况代码
            "txt": "晴" //天气状况描述
        },
        "fl": "30", //体感温度
        "hum": "20%", //相对湿度（%）
        "pcpn": "0.0", //降水量（mm）
        "pres": "1001", //气压
        "tmp": "32", //温度
        "vis": "10", //能见度（km）
        "wind": { //风力风向
            "deg": "10", //风向（360度）
            "dir": "北风", //风向
            "sc": "3级", //风力
            "spd": "15" //风速（kmph）
*/
/*
$resJsonArray['jsonData'][0]['daily_forecast']
         //7天天气预报
          "date": "2015-07-02", //预报日期
            "astro": { //天文数值  
                "sr": "04:50", //日出时间
                "ss": "19:47" //日落时间
            },
            "cond": { //天气状况
                "code_d": "100", //白天天气状况代码，参考http://www.heweather.com/documents/condition-code
                "code_n": "100", //夜间天气状况代码
                "txt_d": "晴", //白天天气状况描述
                "txt_n": "晴" //夜间天气状况描述
            },
            "hum": "14", //相对湿度（%）
            "pcpn": "0.0", //降水量（mm）
            "pop": "0", //降水概率
            "pres": "1003", //气压
            "tmp": { //温度
                "max": "34℃", //最高温度
                "min": "18℃" //最低温度
            },
            "vis": "10", //能见度（km）
            "wind": { //风力风向
                "deg": "339", //风向（360度）
                "dir": "东南风", //风向
                "sc": "3-4", //风力
                "spd": "15" //风速（kmph）

*/
/*
 $resJsonArray['jsonData'][0]['hourly_forecast']
        //每三小时天气预报，全能版为每小时预报
            "date": "2015-07-02 01:00", //时间
            "hum": "43", //相对湿度（%）
            "pop": "0", //降水概率
            "pres": "1003", //气压
            "tmp": "25", //温度
            "wind": { //风力风向
                "deg": "320", //风向（360度）
                "dir": "西北风", //风向
                "sc": "微风", //风力
                "spd": "12" //风速（kmph）
*/
/*
 $resJsonArray['jsonData'][0]['suggestion']
      "suggestion": { //生活指数，仅限国内城市，国际城市无此字段
        "comf": { //舒适度指数
            "brf": "较不舒适", //简介
            "txt": "白天天气多云，同时会感到有些热，不很舒适。" //详细描述
        },
        "cw": { //洗车指数
            "brf": "较适宜",
            "txt": "较适宜洗车，未来一天无雨，风力较小，擦洗一新的汽车至少能保持一天。"
        },
        "drsg": { //穿衣指数
            "brf": "炎热",
            "txt": "天气炎热，建议着短衫、短裙、短裤、薄型T恤衫等清凉夏季服装。"
        },
        "flu": { //感冒指数
            "brf": "少发",
            "txt": "各项气象条件适宜，发生感冒机率较低。但请避免长期处于空调房间中，以防感冒。"
        },
        "sport": { //运动指数
            "brf": "较适宜",
            "txt": "天气较好，户外运动请注意防晒。推荐您进行室内运动。"
        },
        "trav": { //旅游指数
            "brf": "较适宜",
            "txt": "天气较好，温度较高，天气较热，但有微风相伴，还是比较适宜旅游的，不过外出时要注意防暑防晒哦！"
        },
        "uv": { //紫外线指数
            "brf": "中等",
            "txt": "属中等强度紫外线辐射天气，外出时建议涂擦SPF高于15、PA+的防晒护肤品，戴帽子、太阳镜。"
        }
    }
}
］
｝
*/
class WeatherController extends AclController {
    public function index(){
    	$token='b0a35323c60849429722da8bb216592e';
	    $ch2 = curl_init();
	   	$url = 'https://api.heweather.com/x3/condition?search=allcond&key='.$token;
	   	curl_setopt($ch2, CURLOPT_URL, $url);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
		$info = curl_exec($ch2);
		$resIconArray=json_decode($info,true);
		$resIconArray=$resIconArray['cond_info'];

	    $ch = curl_init();
	    $clientAddress=$this->getClientAddress();
	    $url = 'http://apis.baidu.com/heweather/weather/free?city='.$clientAddress;
	    $header = array(
	        'apikey: ef699c10af0bef00d52439b1ca60b732',
	    );
	    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch , CURLOPT_URL , $url);
	    $res = curl_exec($ch);
	    $resArray=array();
   		$resArray=json_decode($res,true);
   		$weatherSuggestion=$resArray['HeWeather data service 3.0'][0]['suggestion'];//建议
   		$weatherAqi=$resArray['HeWeather data service 3.0'][0]['aqi']['city'];//空气质量
   		$weatherNow=$resArray['HeWeather data service 3.0'][0]['now']; //实况天气
   		$weatherBasic=$resArray['HeWeather data service 3.0'][0]['basic']; //基本信息
   		$weatherDailyForecast=$resArray['HeWeather data service 3.0'][0]['daily_forecast']; //7天天气预报
   		$weatherHourlyForecast=$resArray['HeWeather data service 3.0'][0]['hourly_forecast']; //每三小时天气预报

		for($i=0;$i<count($resIconArray);$i++){
			if($resIconArray[$i]['code']==$weatherNow['cond']['code']){
				$weatherNow['cond']['icon']=$resIconArray[$i]['icon'];
				break;
			}
		}
		for($i=0;$i<count($resIconArray);$i++){
			for($j=0;$j<count($weatherDailyForecast);$j++){
				if($resIconArray[$i]['code']==$weatherDailyForecast[$j]['cond']['code_d']){
					$weatherDailyForecast[$j]['cond']['icon_d']=$resIconArray[$i]['icon'];
				}
				if($resIconArray[$i]['code']==$weatherDailyForecast[$j]['cond']['code_n']){
					$weatherDailyForecast[$j]['cond']['icon_n']=$resIconArray[$i]['icon'];
				}
			}
		}
		for($j=0;$j<count($weatherDailyForecast);$j++){
				$weatherDailyForecast[$j]['subDate']=date('m-d',strtotime($weatherDailyForecast[$j]['date']));
			}
  //   	echo "<pre>";
  //   		print_r($weatherHourlyForecast);
  //   	echo "</pre>";
		// exit();
	   $this->assign('clientAddress',$clientAddress);
	   $this->assign('weatherNow',$weatherNow);
	   $this->assign('weatherAqi',$weatherAqi);
	   $this->assign('weatherBasic',$weatherBasic);
	   $this->assign('weatherSuggestion',$weatherSuggestion);
	   $this->assign('weatherDailyForecast',$weatherDailyForecast);
	   $this->assign('weatherHourlyForecast',$weatherHourlyForecast);
       $this->display('index');
   }
	public function getClientAddress(){
        $queryIP=$this->getClientIp();
  		$taobaoIP = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$queryIP;
        $IPinfo = json_decode(file_get_contents($taobaoIP));
        $province = $IPinfo->data->region;
        $city = $IPinfo->data->city;
        $data=$city;
        $data=explode("市",$data);
        return $data[0];
	}
    public function getClientIp(){
        // $unknown = 'unknown'; 
        // if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) { 
        //     $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
        // } 
        // elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) { 
        //     $ip = $_SERVER['REMOTE_ADDR']; 
        // } 
         $ip = get_client_ip();
         // var_dump($ip);
        // if (false !== strpos($ip, ',')) $ip = reset(explode(',', $ip)); 
        // if(!preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/', $str)){
        //     // $ip="183.192.90.44";
        //    return $ip; 
        // }else{
        //    return $ip; 
        // }
        return $ip;
    }

}