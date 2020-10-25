<?php
require_once 'comm.php';
$token=  getData($_REQUEST,"token", "");
$ar["status"]=0;

if(strlen($token)==0 || $wxlogin->isExprise($token)){

    $token=$wxlogin->getNewToken();
    if(strlen($token)==0 || $token==null){
        $ar["status"]=-1;
    }else{
        $ar["token"]=$token;
        $url=  getCurrentUrl("wxloginmobile.php?token=$token");
        $imgurl="http://s.jiathis.com/qrcode.php?url=".urlencode($url);
        $ar["qrurl"]=$imgurl;
    
    }
}else if($wxlogin->isLogined($token)){
    $ar["status"]=1;
    $ar["url"]="showinfo.php";
}else if (strlen($token)>0 && !$wxlogin->isExprise($token)){
      $ar["token"]=$token;
        $url=  getCurrentUrl("wxloginmobile?token=$token");
        $imgurl="http://s.jiathis.com/qrcode.php?url=".urlencode($url);
        $ar["imgurl"]=$imgurl;
}
//var_dump($_SESSION);
echo json_encode($ar);