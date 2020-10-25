<?php
if(1){
    $auditacts = array('list-addform','list-dodelete','updatefield','dosignin');
    if(in_array($act, $auditacts)){
		if(!$gtbl || ($db != '' && $db != $mydb)){
		    $gtbl = new GTbl($_CONFIG['operatelogtbl'], $hmconf=array('db'=>$mydb), null, null);
		}
        $gtbl->setTbl($_CONFIG['operatelogtbl']);
        $gtbl->set('userid', ($userid=='' ? '0' : $userid));
        #$gtbl->set('useremail', $user->getEmail());
        $gtbl->set('parentid', $_REQUEST['id']==''?0:$_REQUEST['id']);
        $gtbl->set('parenttype', $tbl);
        $actstr = "act:[".$act."] id:["
			.($_REQUEST[$gtbl->getMyId()]==''
				?$_REQUEST[$gtbl->getMyId().'.old']
				:$_REQUEST[$gtbl->getMyId()])."]";
		if($act == 'dosignin'){
		    $actstr .= " email:[".$_REQUEST['email']."] ip:[".$_CONFIG['client_ip']."]";
		}
        $gtbl->set('actionstr', $actstr); # see act/dodelete.php
        $hm = $gtbl->setBy("userid,parentid,parenttype,actionstr,inserttime",null);
    }
	else{
        #error_log(__FILE__.": log fail. act:[$act]");
    }
}
?>
