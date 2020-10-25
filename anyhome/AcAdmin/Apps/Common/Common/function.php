<?php

function getMillisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

function cutPic($url = '',$w=0,$h=0){
    if (!$url) return;
    if ($w == 0 || $h == 0) return $url;
    if (!strstr($url, 'clouddn.com')) return $url;
    return $url."?imageView2/1/w/".$w."/h/".$h."/q/90";

}


//将数字转换为目录 例如  31  =   000/000/31
function numberDir($num = 0) {
    if ($num == 0) $num = date('Ymd');
    $num = sprintf("%09d", $num);
    $dir1 = substr($num, 0, 3);
    $dir2 = substr($num, 3, 2);
    $dir3 = substr($num, 5, 2);
    return $dir1.'/'.$dir2.'/'.$dir3.'/';
}


//将cookie 转换为数组
function cookieToArr($ck = ''){
    $c_arr = explode(';',$ck);
    $cookie = array();
    foreach($c_arr as $item) {
        $kitem = explode('=',trim($item));
        if (count($kitem)>1) {
            $key = trim($kitem[0]);
            $val = trim($kitem[1]);
            if (!empty($val)) $cookie[$key] = $val;
        }
    }
    return $cookie;
}


//根据字符串返回被包含的字符
function getWordsByStr($inStr,$findStr)
{
    $rt = array();
    foreach ($findStr as $k) {
        if (strstr($inStr,$k)) {
            array_push($rt, $k);
        }
    }
    return $rt;
}


function cut( $Str, $Length,$sss = '' ) {
    //$Str为截取字符串，$Length为需要截取的长度
    global $s;
    $i = 0;
    $l = 0;
    $ll = strlen( $Str );
    $s = $Str;
    $f = true;
    //if(isset($Str{$i}))
    while ( $i <= $ll ) {
    if ( ord( $Str{$i} ) < 0x80 ) {
    $l++; $i++;
    } else if ( ord( $Str{$i} ) < 0xe0 ) {
    $l++; $i += 2;
    } else if ( ord( $Str{$i} ) < 0xf0 ) {
    $l += 2; $i += 3;
    } else if ( ord( $Str{$i} ) < 0xf8 ) {
    $l += 1; $i += 4;
    } else if ( ord( $Str{$i} ) < 0xfc ) {
    $l += 1; $i += 5;
    } else if ( ord( $Str{$i} ) < 0xfe ) {
    $l += 1; $i += 6;
    }

    if ( ( $l >= $Length - 1 ) && $f ) {
    $s = substr( $Str, 0, $i );
    $f = false;
    }

    if ( ( $l > $Length ) && ( $i < $ll ) ) {
    $s = $s . '...'; break; //如果进行了截取，字符串末尾加省略符号“...”
    // $s = $s . $sss; break; //如果进行了截取，字符串末尾加省略符号“...”
    }
    }
    return $s;
}

function getBrowser(){
    $agent=$_SERVER["HTTP_USER_AGENT"];
    if(strpos($agent,'MSIE')!==false || strpos($agent,'rv:11.0')) //ie11判断
    return "ie";
    else if(strpos($agent,'Firefox')!==false)
    return "firefox";
    else if(strpos($agent,'Chrome')!==false)
    return "chrome";
    else if(strpos($agent,'Opera')!==false)
    return 'opera';
    else if((strpos($agent,'Chrome')==false)&&strpos($agent,'Safari')!==false)
    return 'safari';
    else
    return 'unknown';
}

/** 获取当前时间戳，精确到毫秒 */
function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function getRandChar($length,$t){
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    if ($t == 1) {
        $strPol = "0123456789";
    }elseif ($t == 2) {
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    $max = strlen($strPol)-1;

    for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];
    }

    return $str;
}


function toDatetime( $time, $format = 'Y-m-d H:i:s' ) {
  if ( empty ( $time ) ) {
    return "";
  }
  if ( is_numeric( $time ) ) {
    return date( $format, $time );
  }
  $format = str_replace( '#', ':', $format );
  return date( $format, strtotime( $time ) );
}

function toDate( $time, $format = 'Y-m-d' ) {
  if ( empty ( $time ) ) {
    return $time;
  }
  $format = str_replace( '#', ':', $format );
  return date( $format, $time );
}




function friendlyDate($sTime,$type = 'normal',$alt = 'false') {
    if (!$sTime)
    return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime      =   time();
    $dTime      =   $cTime - $sTime;
    $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if($type=='normal'){
        if( $dTime < 60 ){
            if($dTime < 10){
                return '刚刚';    //by yangjs
            }else{
                return intval(floor($dTime / 10) * 10)."秒前";
            }
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
            //今天的数据.年份相同.日期相同.
        }elseif( $dYear==0 && $dDay == 0  ){
            //return intval($dTime/3600)."小时前";
            return '今天'.date('H:i',$sTime);
        }elseif($dYear==0){
            return date("m月d日 H:i",$sTime);
        }else{
            return date("Y-m-d H:i",$sTime);
        }
    }elseif($type=='mohu'){
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif( $dDay > 0 && $dDay<=7 ){
            return intval($dDay)."天前";
        }elseif( $dDay > 7 &&  $dDay <= 30 ){
            return intval($dDay/7) . '周前';
        }elseif( $dDay > 30 ){
            return intval($dDay/30) . '个月前';
        }
        //full: Y-m-d , H:i:s
    }elseif($type=='full'){
        return date("Y-m-d , H:i:s",$sTime);
    }elseif($type=='ymd'){
        return date("Y-m-d",$sTime);
    }else{
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif($dYear==0){
            return date("Y-m-d H:i:s",$sTime);
        }else{
            return date("Y-m-d H:i:s",$sTime);
        }
    }
}
