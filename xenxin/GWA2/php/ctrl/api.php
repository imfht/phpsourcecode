<?php

# handle api requests 

include_once($appdir."/ctrl/include/language.php");

include_once($appdir."/mod/purl.class.php");

$ndnsR = "-R";

$isoput = 0;

if($act == 'query'){
    
      # cache control
    $needCache = 0;
    if(!$needCache){
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
    }
    else{
        header("Cache-Control: max-age=31536000");
        header("Expires: Fri, 31 Dec 2100 00:00:00 GMT");
    }
    header("Referer: -URL4P");
    if($succ){
        exit;
    }

}
else if($act == 'add'){

}
else if($act == 'edit'){

}
else{
    #$data['resp'] = "Unknown act:[$act].";
	error_log(__FILE__.": unknown act:[$act]");
    
}

$out .= "\n";

$data['time'] = date("H:i", time());
$data['ndnsR'] = $ndnsR;

# tpl

if($out == '' && $smttpl == ''){ # if other module do not define a smttpl and $conf['display_style_smttpl']? 
	$smttpl = 'homepage.html';
}

?>
