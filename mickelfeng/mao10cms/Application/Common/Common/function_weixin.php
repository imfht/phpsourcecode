<?php 
//获取微信access_token
function mc_get_access_token($appid,$appsecret) {
	$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
	$ch = curl_init();//初始化curl
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HEADER, false);//设置header
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //协议头 https，curl 默认开启证书验证，所以应关闭
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
	$data = curl_exec($ch);//运行curl
	curl_close($ch); 
	$jsondata = json_decode($data);
	foreach($jsondata as $key=>$val) :
		if($key=='access_token') :
			return $val;
		endif;
	endforeach;
};
//上传图片
function mc_upload_media($filex,$access_token){
	$file = realpath($filex); //要上传的文件
	$fields['media'] = '@'.$file;
	$ch = curl_init("http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=image") ;
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch) ;
	if (curl_errno($ch)) {
		//return curl_error($ch);
	}
	curl_close($ch);
	$jsondata = json_decode($data);
	foreach($jsondata as $key=>$val) :
		if($key=='media_id') :
			return $val;
		endif;
	endforeach;
};
//上传图文信息
function mc_publish_to_weixin($articles,$access_token){
	$ch = curl_init("https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=$access_token") ;
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$articles);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch) ;
	if (curl_errno($ch)) {
		//return curl_error($ch);
	}
	curl_close($ch);
	$jsondata = json_decode($data);
	foreach($publish_to_weixin as $key=>$val) :
		if($key=='media_id') :
			$ptw_media_id = $val;
		endif;
	endforeach;
	return $ptw_media_id;
};
//群发信息
function mc_weixin_send_msg($msg,$access_token){
	$ch = curl_init("https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=$access_token") ;
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$msg);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch) ;
	if (curl_errno($ch)) {
		//return curl_error($ch);
	}
	curl_close($ch);
	$jsondata = json_decode($data);
	return $jsondata;
};
?>