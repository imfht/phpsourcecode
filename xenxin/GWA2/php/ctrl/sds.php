<?php

# controller
include_once($appdir."/ctrl/include/language.php");
#include_once($appdir."/mod/poll.class.php");

#
# actions

$act = $act == '' ? 'index' : $act;

if($act == 'index'){
	#
	
}
else{
    $data['resp'] = "Unknown act:[$act].";
    
}

$data['time'] = date("H:i", time());


# tpl

if($out == '' && $smttpl == ''){ # if other module do not define a smttpl and $conf['display_style_smttpl']? 
	     
	$smttpl = 'sds.html';
}

?>
