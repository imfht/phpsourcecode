<?php
require_once(dirname(__FILE__).'/iauth_config.php');

function iauth_getdata( $accessToken, $accessSecret, $apiUrl, $pTmp ){
    if (preg_match('/^[0-9a-zA-Z]{40}$/',$accessToken)==0) die('{error:invalid access token}');
    if (preg_match('/^[0-9a-zA-Z]{32}$/',$accessSecret)==0) die('{error:invalid access secret}');

    $time=time()+IAUTH_TIME_OFFSET;
    $nonce= substr(md5(uniqid(rand() . $time, true)),0,16);
    /*################ 生成hash ################*/
    ksort ( $pTmp );
    $str_tmp="";
    foreach ( $pTmp as $key => $val ) {
	$str_tmp .= "$key=" . rawurlencode( $val ) . "&";
	}
    $base_str = substr( $str_tmp, 0, strlen( $str_tmp ) -1 );

    $hash = md5($base_str);

    $params =array(
	'appid'=>IAUTH_APP_ID,
	'time'=>$time,
	'hash'=>$hash,
	'hashmethod'=>'MD5',
	'sigmethod'=>'HMAC-SHA1',
	'version'=>'2.0',
	'nonce'=>$nonce,
	'token'=>$accessToken,
	);

    /*################ 生成BASE String ################*/
    ksort ( $params );
    $str_tmp="";
    foreach ( $params as $key => $val ) {
	$str_tmp .= "$key=" . rawurlencode( $val ) . "&";
	}
    $base_str = 'POST&' . $apiUrl . '&' . substr( $str_tmp, 0, strlen( $str_tmp ) -1 );
    /* echo $base_str;echo '<hr />'; */


    /*################ 签名 ################*/
    $secret= IAUTH_APP_SECRET . '&' . $accessSecret ;
    $sig = base64_encode ( hash_hmac ( "sha1", $base_str, $secret, true ) );

    /*################ 生成请求头部 ################*/
    $params['sig'] = $sig;
    $str_tmp = '';
    foreach ( $params as $key => $val ) {
	$str_tmp .= "$key=\"" . rawurlencode( $val ) . "\",";
	}
    $query_str = substr( $str_tmp, 0, strlen( $str_tmp ) -1 );
    $header=array("Authorization:$query_str");
    /* print_r($header);exit(); */

    /*################ 使用curl发送header ################*/
    if(IAUTH_INNER_NET){  //与ihome服务器在同一内网网段需要的hack
        $header[]="Host: i.buaa.edu.cn";
        $apiUrl = str_replace("i.buaa.edu.cn","211.71.14.156",$apiUrl);
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30 );
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $pTmp);
    $html = curl_exec($curl); // execute the curl command
    curl_close($curl); // close the connection
    if ($html===false){
	die('API请求失败 '. curl_error($curl));
	}
    return $html;
      }
?>
