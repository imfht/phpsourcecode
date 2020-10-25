<?php

# controlling of homepage
include_once($appdir."/ctrl/include/language.php");
#include_once($appdir."/mod/poll.class.php");

#
# objects

#
# actions

$act = $act == '' ? 'index' : $act;

if($act == 'index'){ # something displayed in homepage only, 09:55 11 June 2016
	
	if($act == 'index'){
		#
		
	}
	else{
		
		$data['resp'] = "Unknown act:[$act].";
		
	}

}

# tpl

if($out == '' && $smttpl == ''){ # if other module do not define a smttpl and $conf['display_style_smttpl']? 
	if($fmt != ''){
		# API
	}
	else{
		$smttpl = 'homepage.html';
	}
}

?>
