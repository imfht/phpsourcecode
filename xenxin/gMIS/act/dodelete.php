<?php
# do delete for act=list-dodelete, Fri Apr  6 20:46:13 CST 2012

$fieldlist = array();
if(!isset($fieldargv) || !is_array($fieldargv)){ $fieldargv = array(); }

if($hasid){
    $gtbl->setId($id);
    $tmpVal = $gtbl->getMyId()."=?";
    $fieldargv[] = $tmpVal;
}
else{
    #$fieldargv = "";
    for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
        $field = $gtbl->getField($hmi);
        if($field == null | $field == '' 
                || $field == $gtbl->getMyId()){
            continue;
        }
        if(array_key_exists($field, $_REQUEST)){
            $gtbl->set($field, $_REQUEST[$field]);
            $fieldargv[] = $field."=?";
        }

        $fieldlist[] = $field;
    } 
}
$hmorig = $gtbl->getBy("*", implode(" and ", $fieldargv));
if($hmorig[0]){
    $hmorig = $hmorig[1][0]; # the first row
}

include("./act/checkconsistence.php");
$hm = $gtbl->rmBy(implode(" and ", $fieldargv));
#print_r(__FILE__.": delete:[".$hm."]\n");

$doDeleteResult = true;

# some triggers bgn, added on Sat May 26 10:22:14 CST 2012
include("./act/trigger.php");
# some triggers end, added on Sat May 26 10:22:27 CST 2012

$gtbl->setId('');
$_REQUEST[$gtbl->getMyId().'.old'] = $_REQUEST[$gtbl->getMyId()];
$_REQUEST[$gtbl->getMyId()] = ''; # remedy Thu Apr 17 08:41:11 CST 2014
$id = '';

if($hm[0] && $doDeleteResult){
    $out .= "<script> parent.sendNotice(true,'操作成功！'); parent.switchArea('contentarea_outer','off'); </script>";
}
else{
	if(!$doDeleteResult){
		$out .= "<script> parent.sendNotice(false,'遗憾！操作失败，请重试！".$out."');</script>";
	}
	else{
		$out .= "<script> parent.sendNotice(false,'遗憾！操作失败，请重试！');</script>";
		$deleteErrCode = '201811241202';
	}
}

?>
