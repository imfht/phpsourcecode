<?php
$store = require('store.php');
//允许跨域
header('Access-Control-Allow-Origin:*');
/*
    微信JS-SDK使用权限签名算法
    参考文档地址 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141115
*/
//需要配置的公众号信息
$appId = 'wxd0803523dcadc007';
$appSecret = '6413fed34058b3f6465ac07112cb96ca';

//存储
$storeFile = 'store.php';

//微信文档地址配置
$tokenUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
$ticketUrl = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi';
$nonceStr = 'wantongtest';

/*
  从微信获取token 有效默认7200ms 需要存储 不能一直刷新 否则会被屏蔽  
*/
function getAccessTokenFromWx() {
    global $tokenUrl, $appId, $appSecret;
    $url = sprintf($tokenUrl, $appId, $appSecret);
    $data = curl_get_https($url);
    //echo 'token->'.$data['access_token'];

    return $data['access_token'];
}

/*
  从微信获取ticket 有效默认7200ms 需要存储 不能一直刷新 否则会被屏蔽  
*/
function getTicketFromWx($token) {
    global $ticketUrl;
    $url = sprintf($ticketUrl, $token);
    $data = curl_get_https($url);

    return $data;
}

/*
    存储ticket
*/
function storeTicket($ticket, $expires_in) {
    global $storeFile;
    $newConfig = '<?php return array("ticket" => "' . $ticket . '","expires_in" => "' . $expires_in . '");';
    @file_put_contents($storeFile, $newConfig);
}

function getTicket($authUrl) {
    global $store, $appId, $nonceStr;
    //当前时间戳
    $timestamp = time();
    //现从本地获取看是否过期
    $ticket = $store['ticket'];
    if (intval($store['expires_in']) > $timestamp) {
        //未过期
        $ticket = $store['ticket'];
    } else {
        //先微信获取
        $token = getAccessTokenFromWx();
        $ticketData = getTicketFromWx($token);
        //取出数据
        $ticket = $ticketData['ticket'];
        $expires_in = intval($ticketData['expires_in']) + $timestamp;
        //存储到本地
        storeTicket($ticket, $expires_in);
    }

    //签名
    $signature = getSignature($ticket, $nonceStr, $timestamp, $authUrl);
    $res = array(
        'appId' => $appId,
        'timestamp' => $timestamp,
        'nonceStr' => $nonceStr,
        'signature' => $signature,
        'ticket' => $ticket,
        'url' => $authUrl,
        'code' => 0
    ); //【关联数组】

    return json_encode($res);
}
function getSignature($jsapi_ticket, $nonceStr, $timestamp, $url) {
    // $array = array(
    //     $jsapi_ticket,
    //     $nonceStr,
    //     $timestamp,
    //     $url
    // );
    // sort($array, SORT_STRING);
    // $preSign = implode($array);
    $preSign = 'jsapi_ticket='.$jsapi_ticket.'&noncestr='.$nonceStr.'&timestamp='.$timestamp.'&url='.$url;
    //echo 'str->'.$preSign.'<br>';
    $rtn = sha1($preSign);

    return $rtn;
}

//https请求
function curl_get_https($url) {
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
    $tmpInfo = curl_exec($curl); //返回api的json对象
    //关闭URL请求
    curl_close($curl);
    //echo 'res->'.$tmpInfo.'<br>';
    $jsonData = json_decode($tmpInfo, true);
    //errcode
    if (isset($jsonData['errcode'])) {
        echo json_encode(array(
            'code' => $jsonData['errcode'],
            'msg' => $jsonData['errmsg']
        ));

        exit(1);
    }

    return $jsonData; 
}


if (is_array($_GET) && count($_GET) > 0) {
    if (isset($_GET["url"])) {
        $url = $_GET["url"]; 
        echo getTicket($url);
        
        return;
    }
}

echo json_encode(array(
    'code' => 1,
    'msg' => '参数不存在'
));