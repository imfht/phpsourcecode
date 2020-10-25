<?php
require_once('../config.php');
if(RENREN_PUBLISH_ENABLED != true){exit('RenRen Pushing is disabled by config.php');}
if(isset($_GET['error'])||empty($_GET['code'])){
  $GetData['client_id'] = RENREN_APIKEY;
  $GetData['response_type'] = 'code';
  $GetData['redirect_uri'] = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/renren.php';
  $GetData['scope'] = 'status_update,admin_page';
  header('Location:'.'https://graph.renren.com/oauth/authorize?'.http_build_query($GetData));
  exit();
}
$PostData['grant_type'] = 'authorization_code';
$PostData['client_id'] = RENREN_APIKEY;
$PostData['client_secret'] = RENREN_APPSECRET;
$PostData['redirect_uri'] = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/renren.php';
$PostData['code'] = $_GET['code'];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://graph.renren.com/oauth/token");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_HEADER,0);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($PostData));
$RenRenInfo = json_decode(curl_exec($ch));
curl_close($ch);
if(!empty($RenRenInfo->error)){
  exit('RenRen Error: ' . $RenRenInfo->error);
}
if($RenRenInfo->scope != 'status_update admin_page'){
  exit('You must grant the admin access to public pages and the capability to update status!');
}
$KVDB = new SaeKV();
if(!$KVDB->init()){exit('KVDB Error! Please check whether the configuration of KVDB is correct.');}
$KVDB->set('RENREN-AccessToken',$RenRenInfo->access_token);
$KVDB->set('RENREN-AccessToken-Expires',$RenRenInfo->expires_in + time());
$KVDB->set('RENREN-RefreshToken',$RenRenInfo->refresh_token);
$KVDB->set('RENREN-RefreshToken-Expires',518400 + time()); //根据人人网接口文档，RefreshToken的有效期默认为2个月
echo 'RenRen Configuration Status: OK!';