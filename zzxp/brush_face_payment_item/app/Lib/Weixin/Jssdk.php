<?php
namespace App\Lib\Weixin;
use Redis;
class Jssdk {
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage($url = '') {
    $jsapiTicket = $this->getJsApiTicket();
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    !isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] = 'wx.buketech.com';
    if($url == ''){
      $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
    //\Log::info($string);
    $signature = sha1($string);
    //\Log::info($signature);
    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage;
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket($bool = false) {
    // return 'ssssssssss';
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    // $data = json_decode(file_get_contents("./jsapi_ticket.json"));

    $wxappid = $this->appId . '_ticket';
    if(class_exists('Redis') && \Config::get('weixin.redis_lock')){
        $redis = new \Redis();
        $redis->connect(\Config::get('weixin.redis_lock'), 6379);
        $ticket = $redis->hGet($wxappid,'ticket');
        $expire_time = $redis->hGet($wxappid,'expire_time');
        if(empty($access_token) && $expire_time > time() && $bool === false){
            return $ticket;
        }
    }else{
        $file = storage_path() . '/weixin.token';
        $expire_time = 0;
        $data = ['',''];
        if(file_exists($file)){
            $data = file($file);
            $expire_time = $data[1];
            $ticket = str_replace("\r\n", '', $data[0]);
        }
    }
    if ($expire_time < time() ) {
      $accessToken = $this->getAccessToken();
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = isset($res->ticket) ? $res->ticket : '';
      if ($ticket) {
        $expire_time = time() + 7200;
        if(class_exists('Redis') && \Config::get('weixin.redis_lock')){
            $redis = new \Redis();
            $redis->connect(\Config::get('weixin.redis_lock'), 6379);
            $token = $redis->hSet($wxappid,'ticket',$ticket);
            $expire_time = $redis->hSet($wxappid,'expire_time',$expire_time);
        }else{
            file_put_contents($file, $ticket . "\r\n" . $expire_time);
        }
        $data = [$ticket,$expire_time];
      }
    }       
    return $ticket;
  }

  private function getAccessToken() {
    // return 'ssssssssss';
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    // $data = json_decode(file_get_contents("./access_token.json"));
    $wxappid = $this->appId;
    if(class_exists('Redis') && \Config::get('weixin.redis_lock')){
        $redis = new \Redis();
        $redis->connect(\Config::get('weixin.redis_lock'), 6379);
        $access_token = $redis->hGet($wxappid,'access_token');
        $expire_time = $redis->hGet($wxappid,'expire_time');
        if(empty($access_token) && $expire_time > time() && $bool === false){
            return $access_token;
        }
    }else{
        $file = storage_path() . '/access.token';
        $expire_time = 0;
        $data = array('','');
        if(file_exists($file)){
            $data = file($file);
            $expire_time = $data[1];
            $access_token = str_replace("\r\n","",$data[0]);
        }
    }
    if ($expire_time < time()) {
        // 如果是企业号用以下URL获取access_token
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
        $res = json_decode($this->httpGet($url));
        $access_token = $res->access_token;
        if ($access_token) {
            $expire_time = time() + 7200;
            $data = [$access_token,$expire_time];
            if(class_exists('Redis') && \Config::get('weixin.redis_lock')){
                $redis = new \Redis();
                $redis->connect(\Config::get('weixin.redis_lock'), 6379);
                $token = $redis->hSet($wxappid,'access_token',$access_token);
                $expire_time = $redis->hSet($wxappid,'expire_time',$expire_time);
            }else{
                file_put_contents($file, $access_token . "\r\n" . $expire_time);
            }
        }
    } 
    
    
    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }
}

