<?php
$wxlogin =getWxLogin("SessionWxLogin");


function getWxLogin($classname){
    require_once "imp/$classname.php";
    return new $classname;
    
}
function getCurUrl(){
    $url=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
    if($_SERVER["SERVER_PROTOCOL"]=="HTTP/1.1"){
        $url="http://".$url;
    }
    return $url;
}
function getCurrentUrl($scriptname){
    $url=  getCurUrl();
    $curp=  strrpos($url, "/");
    if($curp>=0){
        return substr($url, 0,$curp+1).$scriptname;
    }
    
}

function getData($var,$key,$default){
    if(!isset($var[$key])){
        return $default;
    }else{
        return $var[$key];
}

    }