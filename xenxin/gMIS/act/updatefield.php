<?php

$field = $_REQUEST['field'];

# remedy by wadelau Tue Jun 30 16:17:53 CST 2015
# same as act/doaddmodi.php
$fieldv = urldecode(trim($_REQUEST[$field]==''?$hmfield[$field."_default"]:$_REQUEST[$field]));
if(1){
	if(strpos($fieldv,"<") !== false){ # added by wadelau on Sun Apr 22 22:09:46 CST 2012
		if($fieldInputType == 'textarea'){	
			# allow all html tags except these below 
			$fieldv = str_replace("<script","&lt;script", $fieldv);
			$fieldv = str_replace("<iframe","&lt;iframe", $fieldv);
			$fieldv = str_replace("<embed","&lt;embed", $fieldv);
		}
		else{
			$fieldv = str_replace("<","&lt;", $fieldv);
		}
	}
	else if(strpos($fieldv,"&lt;") !== false){
		#error_log(__FILE__.": 0 $fieldv");
		$fieldv = preg_replace("/&lt;[^>]+?>/", "", $fieldv); # remedy on Fri, 26 Aug 2016 16:32:13 +0800
		$fieldv = str_replace("&nbsp;", "", $fieldv);
		#error_log(__FILE__.": 1 $fieldv");
	}
	if(strpos($fieldv, "\n") !== false){
		$fieldv = str_replace("\n", "<br/>", $fieldv);
	}
}

$gtbl->set($field, $fieldv);
$gtbl->setId($id);

$hm = $gtbl->setBy("$field", "");

if($hm[0]){
    $hm = $hm[1];
    $out .= "--SUCC--";
}

# read newly-written data, Tue Sep 27 13:28:06 CST 2016
$hmNew = $gtbl->getBy('*', $gtbl->myId.'="'.$id.'"');
if($hmNew[0]){
	$hmNew = $hmNew[1][0];
	#debug(__FILE__.": resultset-tag:[".$gtbl->resultset."]");
	$gtbl->set($gtbl->resultset, $hmNew);
	#debug($gtbl->get($gtbl->resultset));
}
# some triggers bgn, added on Fri Mar 23 21:51:12 CST 2012
include("./act/trigger.php");
# some triggers end, added on Fri Mar 23 21:51:12 CST 2012

$gtbl->setId(''); # clear targetId

?>
