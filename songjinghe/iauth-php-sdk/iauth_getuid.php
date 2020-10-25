<?php
require_once(dirname(__FILE__).'/iauth_config.php');

function iauth_login( $verifier, $state ){
    if (preg_match('/^[0-9a-zA-Z]{16}$/',$verifier)==0) die('FUNCTION(iauth_getuid): invalid verifier');
    $time=time()+IAUTH_TIME_OFFSET;
    $nonce= substr(md5(uniqid(rand() . $time, true)),0,16);
    /* print_r($_SERVER); */
    $params =array(
	'state' => $state,
	'appid'=>IAUTH_APP_ID,
	'time'=>$time,
	'sigmethod'=>'HMAC-SHA1',
	'version'=>'2.0',
	'verifier'=>$verifier
	);
    if(IAUTH_IP_CHECK=="ON") $params['ip']=$_SERVER['REMOTE_ADDR'];
    /*################ 生成BASE String ################*/
    ksort ( $params );
    $str_tmp="";
    foreach ( $params as $key => $val ) {
	$str_tmp .= "$key=" . rawurlencode( $val ) . "&";
	}
    $base_str = 'GET&' . IAUTH_GETUID_URL . '&' . substr( $str_tmp, 0, strlen( $str_tmp ) -1 );
    /* echo $base_str;echo '<hr />'; */
    /* 删去最后多出来的一个'&' */

    /*################ 签名 ################*/
    $secret= IAUTH_APP_SECRET ;
    $sig = base64_encode ( hash_hmac ( "sha1", $base_str, $secret, true ) );

    /*################ 生成请求头部 ################*/
    $params['sig'] = $sig;
    $str_tmp = '';
    foreach ( $params as $key => $val ) {
	$str_tmp .= "$key=\"" . rawurlencode( $val ) . "\",";
	}
    $query_str = substr( $str_tmp, 0, strlen( $str_tmp ) -1 ); /* 删去最后多出来的一个'&' */
    $header=array("Authorization:$query_str");


    /*################ 使用curl发送header ################*/
    $url = IAUTH_GETUID_URL;
    if(IAUTH_INNER_NET){ //与ihome服务器在同一内网网段需要的hack
        $header[]="Host: i.buaa.edu.cn";
        $url = str_replace("i.buaa.edu.cn","211.71.14.156",$url);
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30 );
    $html = curl_exec($curl); // execute the curl command
    curl_close($curl); // close the connection
    if ($html===false){
	die('发送请求失败 '. curl_error($curl));
	}
    /* echo $html . '<br />'; */
    /* exit(); */
    /* die($html); */
    /*################ 从返回数据中提取参数 ################*/
    $tmp=preg_match('/uid=([0-9]+)&access_token=([0-9a-zA-Z]{40})/',$html,$match);
    if($tmp==0) die( '服务器没有返回需要的数据: '. $html);
    $user_id=$match[1];
    $access_token=$match[2];
    return array(
	'token'=>$access_token,
	'uid'=>$user_id,
	);
    }

?>
