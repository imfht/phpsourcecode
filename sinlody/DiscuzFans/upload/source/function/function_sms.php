<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: function_sms.php 33961 2013-09-06 07:39:33Z pmonkey_w $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

set_time_limit(0);
function sendsms($tosms, $message, $template) {
	global $_G;
	if(!is_array($_G['setting']['sms'])) {
		$_G['setting']['sms'] = dunserialize($_G['setting']['sms']);
	}

	list($sign,$temp,$smsid) = explode("\n", str_ireplace("\r\n", "\n", $template));

	if(is_array($message)){
	    $msg = $temp;
	    foreach ($message as $k=>$v){
	        $msg = str_ireplace("{{$k}}", $v, $msg);
	    }
	    $message = $msg;
	}else{
	    $message = preg_replace("/href\=\"(?!(http|https)\:\/\/)(.+?)\"/i", 'href="'.$_G['siteurl'].'\\2"', $message);
	}

	$smscookie = md5($tosms.$message);
	$smscookiename = substr($smscookie, 16, 6);
	if(getcookie($smscookiename)) return false;

	$_G['setting']['sms']['type'] = $_G['setting']['sms']['type'] ? $_G['setting']['sms']['type'] : 1;

	if($_G['setting']['sms']['type'] == 1) {
	    $default = array('SMS_6797345','SMS_3050401','SMS_3050400','SMS_3050399','SMS_3050398','SMS_3050397','SMS_3050396','SMS_3050395');
	    if(in_array($smsid, $default) && strpos($temp, '{product}') === false){
	        if(strpos($temp, '{sitename}') !== false){
	            $temp = str_ireplace('{sitename}', '{product}', $temp);
	        }elseif(strpos($temp, '{bbname}') !== false){
	            $temp = str_ireplace('{bbname}', '{product}', $temp);
	        }elseif(strpos($temp, '{username}') !== false){
	            $temp = str_ireplace('{username}', '{product}', $temp);
	        }
	    }
		$result = @sendmsg_by_alidayu($_G['setting']['sms']['auth_username'], $_G['setting']['sms']['auth_passwd'], $tosms, $message, $sign, $smsid, $temp);
	}
	if($result){
	    dsetcookie($smscookiename, $smscookie, 60);
	    dsetcookie('sendsms', '1', 300);
	}
	return $result;
}

function sendsms_cron($tosms, $message, $template) {
    global $_G;
	$tosms = addslashes($tosms);

	$value = C::t('common_smscron')->fetch_all_by_sms($tosms, 0, 1);
	$value = $value[0];
	if($value) {
		$cid = $value['cid'];
	} else {
		$cid = C::t('common_smscron')->insert(array('sms' => $tosms), true);
	}
	if(is_array($message)){
	    $message = serialize($message);
	}else{
	    $message = preg_replace("/href\=\"(?!(http|https)\:\/\/)(.+?)\"/i", 'href="'.$_G['siteurl'].'\\1"', $message);
	}
	list($sign,$temp,$smsid) = explode("\n", $template);
	$setarr = array(
		'cid' => $cid,
		'message' => $message,
		'dateline' => $_G['timestamp'],
	    'temptype' => $_G['setting']['sms']['type'] ? $_G['setting']['sms']['type'] : 1,
	    'tempsign' => trim($sign),
	    'template' => $template,
	);
	C::t('common_smsqueue')->insert($setarr);

	return true;
}

function sendsms_touser($touid, $message, $template, $smstype='') {
	global $_G;

	if(empty($_G['setting']['sendsmsday'])) return false;

	require_once libfile('function/home');
	$tospace = getuserbyuid($touid);
	if(empty($tospace['sms'])) return false;

	space_merge($tospace, 'field_home');
	space_merge($tospace, 'status');

	$acceptsms = $tospace['acceptsms'];
	if(!empty($acceptsms[$smstype]) && $_G['timestamp'] - $tospace['lastvisit'] > $_G['setting']['sendsmsday']*86400) {
		if(empty($tospace['lastsendsms'])) {
			$tospace['lastsendsms'] = $_G['timestamp'];
		}
		$sendtime = $tospace['lastsendsms'] + $acceptsms['frequency'];

		$value = C::t('common_smscron')->fetch_all_by_touid($touid, 0, 1);
		$value = $value[0];
		if($value) {
			$cid = $value['cid'];
			if($value['sendtime'] < $sendtime) $sendtime = $value['sendtime'];
			C::t('common_smscron')->update($cid, array('sms' => $tospace['sms'], 'sendtime' => $sendtime));
		} else {
			$cid = C::t('common_smscron')->insert(array(
				'touid' => $touid,
				'sms' => $tospace['sms'],
				'sendtime' => $sendtime,
			), true);
		}
		if(is_array($message)){
    	    $message = serialize($message);
    	}else{
    	    $message = preg_replace("/href\=\"(?!(http|https)\:\/\/)(.+?)\"/i", 'href="'.$_G['siteurl'].'\\1"', $message);
    	}
    	list($sign,$temp,$smsid) = explode("\n", $template);
    	$setarr = array(
    		'cid' => $cid,
    		'message' => $message,
    		'dateline' => $_G['timestamp'],
    	    'temptype' => $_G['setting']['sms']['type'] ? $_G['setting']['sms']['type'] : 1,
    	    'tempsign' => trim($sign),
    	    'template' => $template,
    	);
		C::t('common_smsqueue')->insert($setarr);
		return true;
	}
	return false;
}

function sendmsg_by_alidayu($key,$secret,$sms,$content,$sign,$msgid,$msg){
    $url = "https://eco.taobao.com/router/rest";
    $preg = $msg;
    $param = $arr = array();
    if(preg_match_all('/\{(\w+)\}/', $msg, $match)){
        $addcslashes = array('[',']','.');
        foreach ($addcslashes as $v){
            $preg = str_ireplace($v, '\\'.$v, $preg);
        }
        foreach ($match[1] as $k=>$v){
            $preg = str_ireplace($match[0][$k], '(.*)', $preg);
            $arr[$k] = $v;
        }
        if(preg_match('/^'.$preg.'$/', $content, $match2)){
            array_shift($match2);
            foreach ($arr as $k=>$v){
                $param[$v] = diconv($match2[$k], CHARSET, 'UTF-8');
            }
        }
        $data = array(
            'method'=>'alibaba.aliqin.fc.sms.num.send',
            'app_key'=>$key,
            'timestamp'=>date('Y-m-d H:i:s'),
            'format'=>'json',
            'v'=>'2.0',
            'sign_method'=>'md5',
            'sign'=>'',//API输入参数签名结果
            'sms_type'=>'normal',
            'sms_free_sign_name'=>diconv($sign, CHARSET, 'UTF-8'),
            'sms_param'=>json_encode($param),
            'rec_num'=>$sms,
            'sms_template_code'=>$msgid,
        );

        $signparam = $data;
        unset($signparam['sign']);
        ksort($signparam);
        $stringToBeSigned = $secret;
        foreach ($signparam as $k => $v){
            if(is_string($v) && "@" != substr($v, 0, 1)){
                $stringToBeSigned .= "$k$v";
            }
        }
        $stringToBeSigned .= $secret;
        $data['sign'] = strtoupper(md5($stringToBeSigned));
        return post_by_alidayu($url,$data);
    }else{
        return false;
    }
}

function post_by_alidayu($url, $post = array()) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    if (is_array($post) && 0 < count($post)){
        $postBodyString = "";
        $postMultipart = false;
        foreach ($post as $k => $v){
            if(!is_string($v)){
                continue ;
            }
            if("@" != substr($v, 0, 1)){
                $postBodyString .= "$k=" . urlencode($v) . "&";
            }else{
                $postMultipart = true;
                if(class_exists('\CURLFile')){
                    $postFields[$k] = new \CURLFile(substr($v, 1));
                }
            }
        }
        unset($k, $v);
        curl_setopt($curl, CURLOPT_POST, true);//post方式提交
        if ($postMultipart){
            if (class_exists('\CURLFile')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            } else {
                if (defined('CURLOPT_SAFE_UPLOAD')) {
                    curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
                }
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        }else{
            $header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
            curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
            curl_setopt($curl, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
        }
    }
    $rs = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($rs,true);
    if($result && !isset($result['error_response']) && $result['alibaba_aliqin_fc_sms_num_send_response']['result']['err_code']==0){
        return true;
    }else{
        return false;
    }
}

?>