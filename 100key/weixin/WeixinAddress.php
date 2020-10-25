<?php
class WeixinAddress{
    const AK='BEb67adb8889b2570f05b687084d5aa2';//开发者密钥
	
// 	public function __construct($ak){
// 		$this->ak=$ak;
// 	}
	/**
	 * 坐标系转换
	 * 
	 * 该接口适用于需将非百度地图坐标的坐标进行转化，进而将其运用到百度地图开发的用户。该接口还支持批量坐标转化，一次最多可转换100个坐标点。
	 * 使用坐标转服服务前，首先需要登录API控制台申请密钥ak，坐标转换服务属于for server类应用
	 * 坐标转换服务无日请求次数限制。 坐标转换服务每次最多支持100个坐标点的转换且并发数为1000次/秒。
	 * 
	 * 使用方法
	 * 第一步，申请密钥（ak），作为访问服务的依据；
	 * 第二步，按照请求参数说明拼写发送http请求的url，注意需使用第一步申请的ak；
	 * 第三步，接收返回的数据（json或者xml格式）。
	 * 注：本接口支持回调。另外，同一个GPS坐标多次转为百度坐标时，每次转换结果都不完全一样，误差在2米范围内，属于正常误差，不影响正常使用。
	 * 
	 * from 取值为如下：
	 * 1：GPS设备获取的角度坐标;
	 * 2：GPS获取的米制坐标、sogou地图所用坐标;
	 * 3：google地图、soso地图、aliyun地图、mapabc地图和amap地图所用坐标
	 * 4：3中列表地图坐标对应的米制坐标
	 * 5：百度地图采用的经纬度坐标
	 * 6：百度地图采用的米制坐标
	 * 7：mapbar地图坐标;
	 * 8：51地图坐标
	 * 默认为1，即GPS设备获取的坐标
	 * 
	 * 返回值说明
	 * status 状态码  正常0，异常非0. 1内部错误 21from非法 22to非法 24coords格式非法 25coords个数非法，超过限制
	 * {
	 * 		status : 0,
	 * 		result : 
	 * 		[
	 * 			{
	 * 				x : 114.23074789746,
	 * 				y : 29.579086404502
	 * 			}
	 * 		]
	 * }
	 * 
	 * 本方法已对json做了处理，返回纬度和经度
	 */
	private function geoconv($coords, $from=3, $to=5, $output='json'){
		$url='http://api.map.baidu.com/geoconv/v1/?';//服务地址
		$data=array(
			'coords'=>$coords,//源坐标.格式：经度,纬度;经度,纬度…  限制：最多支持100个  格式举例：114.21892734521,29.575429778924;
			'ak'=>self::AK,//开发者密钥
			'from'=>$from,//源坐标类型
			'to'=>$to,//目的坐标类型 有两种可供选择：5、6。 5：bd09ll(百度经纬度坐标), 6：bd09mc(百度米制经纬度坐标); 默认为5，即bd09ll(百度坐标)
			'output'=>$output//返回结果格式  json或者xml
		);
		$query=http_build_query($data);
		$result=$this->curlGet($url.$query);
		$result=json_decode($result, true);
		if($r->status==0){
			return $result['result'][0]['x'].','.$result['result'][0]['y'];
		}else{
			//return $result['status'];
			return 0;
		}
	}
	
	/**
	 * 经纬度坐标到地址的转换
	 * Geocoding API包括地址解析和逆地址解析功能。
	 * 百度地图Geocoding API是一套免费对外开放的API，默认配额100万次/天。使用方法：
	 * 第一步：申请ak（即获取密钥），若无百度账号则首先需要注册百度账号。
	 * 第二步，拼写发送http请求的url，注意需使用第一步申请的ak。
	 * 第三步，接收http请求返回的数据（支持json和xml格式）。
	 */
	public function coordinateToAddress($location, $pois=0, $output='json', $coordtype='bd09ll'){//[kəʊ'ɔ:dɪneɪt]
		$location=$this->geoconv($location);
		if($location==0){
			return '暂未查处该地址';
			return false;
		}
		$url='http://api.map.baidu.com/geocoder/v2/?';//服务地址
		$data=array(
			'ak'=>self::AK,//开发者密钥
			'location'=>$location,//例如38.76623,116.43213
			'pois'=>$pois,//是否显示指定位置周边的poi，0为不显示，1为显示。当值为1时，显示周边100米内的poi。
			'output'=>$output,//输出格式为json或者xml
			'coordtype'=>$coordtype,//坐标的类型，目前支持的坐标类型包括：bd09ll（百度经纬度坐标）、gcj02ll（国测局经纬度坐标）、wgs84ll（ GPS经纬度）
		);
		$query=http_build_query($data);
		$result=$this->curlGet($url.$query);
		return $result=json_decode($result, true);
		
// 		$result['status']=0;//返回成功
// 		$result['result']['location']['lat'];//百度纬度
// 		$result['result']['location']['lng'];//百度经度
// 		$result['result']['formatted_address'];//上海市浦东新区浦东大道2056
// 		$result['result']['business'];//杨浦大桥,洋泾,源深体育中心
// 		$result['result']['addressComponent']['country'];//中国
// 		$result['result']['addressComponent']['city'];//上海市
// 		$result['result']['addressComponent']['direction'];//东
// 		$result['result']['addressComponent']['distance'];//58
// 		$result['result']['addressComponent']['district'];//浦东新区
// 		$result['result']['addressComponent']['province'];//上海市
// 		$result['result']['addressComponent']['street'];//浦东大道
// 		$result['result']['addressComponent']['street_number'];//2056
// 		$result['result']['addressComponent']['country_code'];//0
// 		$result['result']['poiRegions'][0]['direction_desc'];//内
// 		$result['result']['poiRegions'][0]['name'];//海防新村
// 		$result['result']['sematic_description'];//海防新村内,永安新村东北99米
// 		$result['result']['cityCode'];//289
	}
	
	/**
	 * 地址到经纬度坐标的转换
	 * 特别说明： 若解析status字段为OK，但结果内容为空，原因分析及可尝试方法：
	 * 地址库里无此数据，本次结果为空
	 * 加入city字段重新解析
	 * 将过于详细或简单的地址更改至省市区县街道重新解析
	 */
	public function addressToCoordinate($address, $city='', $output='json', $callback='showLocation'){
		$url='http://api.map.baidu.com/geocoder/v2/?';
		$data=array(
			'ak'=>self::AK,//开发者密钥
			'address'=>$address,
			'city'=>$city,//地址所在的城市名 该参数是可选项，用于指定上述地址所在的城市，当多个城市都有上述地址时，该参数起到过滤作用。
			'output'=>$output,//输出格式为json或者xml
			'callback'=>$callback,//callback=showLocation(JavaScript函数名) 将json格式的返回值通过callback函数返回以实现jsonp功能
		);
		$query=http_build_query($data);
		return $this->curlGet($url.$query);
	}
	
	//cURL post提交
	private function curlPost($url, $data=''){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		return curl_exec($ch);
		curl_close ( $ch );
	}
	
	//cURL get提交
	private function curlGet($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		return curl_exec($ch);
		curl_close ( $ch );
	}
}
?>