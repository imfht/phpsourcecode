<?php
//快递查询类
namespace Scoreshop\Model;
use Think\Model;

class ScoreshopLogisticModel extends Model{
	//测试接口路径
	//const ReqURL = 'http://sandboxapi.kdniao.cc:8080/kdniaosandbox/gateway/exterfaceInvoke.json';
	//正式接口
	const ReqURL = 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx';
	/**
	 * Json方式 查询订单物流轨迹
	 * @param json $datas 提交的数据数组 "{'OrderCode':'','ShipperCode':'YTO','LogisticCode':'12345678'}"
	 */
	public function getOrderTracesByJson($requestData){
		
		$Ebusiness = modC('MUUSHOP_DELIVERY_EBUSINESS','','Muushop');//请到快递鸟官网申请http://kdniao.com/reg
		$AppKey = modC('MUUSHOP_DELIVERY_APPKEY','','Muushop');//电商加密私钥，快递鸟提供，注意保管，不要泄漏
		if(empty($Ebusiness) || empty($AppKey)){
			return '请完善接口Ebusiness或AppKey';
		}
		
		$datas = array(
	        'EBusinessID' => $Ebusiness,
	        'RequestType' => '1002',
	        'RequestData' => urlencode($requestData) ,
	        'DataType' => '2',
	    );
		
	    $datas['DataSign'] = $this->encrypt($requestData, $AppKey);
		$result=$this->sendPost(self::ReqURL, $datas);
		
		return $result;
	}
 
	/**
	 *  post提交数据 
	 * @param  string $url 请求Url
	 * @param  array $datas 提交的数据 
	 * @return url响应返回的html
	 */
	public function sendPost($url, $datas) {
	    $temps = array();	
	    foreach ($datas as $key => $value) {
	        $temps[] = sprintf('%s=%s', $key, $value);		
	    }
	    $post_data = implode('&', $temps);
	    $url_info = parse_url($url);
		if(empty($url_info['port']))
		{
			$url_info['port']=80;	
		}
	    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
	    $httpheader.= "Host:" . $url_info['host'] . "\r\n";
	    $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
	    $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
	    $httpheader.= "Connection:close\r\n\r\n";
	    $httpheader.= $post_data;
	    $fd = fsockopen($url_info['host'], $url_info['port']);
	    fwrite($fd, $httpheader);
	    $gets = "";
		$headerFlag = true;
		while (!feof($fd)) {
			if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
				break;
			}
		}
	    while (!feof($fd)) {
			$gets.= fread($fd, 128);
	    }
	    fclose($fd);  
	    
	    return $gets;
	}

	/**
	 * 电商Sign签名生成
	 * @param data 内容   
	 * @param appkey Appkey
	 * @return DataSign签名
	 */
	public function encrypt($data, $appkey) {
	    return urlencode(base64_encode(md5($data.$appkey)));
	}

}