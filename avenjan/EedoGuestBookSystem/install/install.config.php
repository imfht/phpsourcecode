<?php
if(!defined("CMS")) exit("Access Denied");
function gdversion(){
    //没启用php.ini函数的情况下如果有GD默认视作2.0以上版本
    if(!function_exists('phpinfo')){
        if(function_exists('imagecreate')) return '2.0';
        else return 0;
    }
    else{
        ob_start();
        phpinfo(8);
        $module_info = ob_get_contents();
        ob_end_clean();
        if(preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info,$matches)) {   $gdversion_h = $matches[1];  }
        else {  $gdversion_h = 0; }
        return $gdversion_h;
    }
}

function TestWrite($d)
{
    $tfile = 'cms.txt';
    $d = preg_replace("#\/$#", '', $d);
    $fp = fopen("..".$d.'/'.$tfile, 'w');
    if(!$fp) return 0;
    else{
        fclose($fp);
        $rs = @unlink("..".$d.'/'.$tfile);
        if($rs) return 1;
        else return 0;
    }
}
?>