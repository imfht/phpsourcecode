<?php
# restore to open-end policy, 16:41 Tuesday, April 14, 2020
if(1){
    $chkAccess = $user->chkAccess($_REQUEST);
    $chkResult = true; 
    $denymsg = "访问被拒绝, 请联系管理员或者上级申请权限. \\n\\n UserName:[".$user->getEmail()
        ."] UserId:[".$userid."] ObjectId:[".$user->getObjId()."]";
    if(!$chkAccess['result']){
        $chkResult = false;
        $denymsg .= "\n<br/><script type='text/javascript'>window.alert('".$denymsg.". \\n\\n Refer: "
                .$chkAccess['reason'].".\\n201203132113.'); window.history.go(-1);</script>";
    }
    # 写权限判断
    $writeAct = array('add','modify','list-doaddform','updatefield', 'import');
    if(in_array($_REQUEST['act'],$writeAct)){
        if(!$user->canWrite('',$_REQUEST['tbl'])){
            $chkResult = false; 
            $denymsg .= " 201204172112.";
        }
    }
    # 删权限判断
    $deleteAct = array('list-dodelete');
    if(in_array($_REQUEST['act'],$deleteAct)){
        if(!$user->canDelete($_REQUEST['tbl'])){
            $chkResult = false; 
            $nexturl = $_SERVER['REQUEST_URI']."?".$_SERVER['QUERY_STRING'];
            $nexturl = str_replace($_REQUEST['act'], 'list', $nexturl);
            $denymsg .= "\n<br/><script type='text/javascript'>window.alert('".$denymsg.". \\n\\n"
                    .$chkAccess['reason'].".\\n201204172118.'); window.location.href='".$nexturl."';</script>";
        }
    }
    # 字段级内容权限判断
    if(1){
       if(($id != '' && $act != 'list') || ($id == '' && $act == 'add')){
            #$out .= "--- ---id:[".$id."]\n";
            $hmconf = GTbl::xml2hash($xmlpathpre, $elementsep, $db, $tbl);
            $tmpgtbl = new GTbl($tbl, $hmconf[0], $elementsep);
            $tmpsa = $tmpgtbl->getSuperAccess();
            if($tmpsa != ''){
                #$out .= "tmpsupera:[".$tmpsa."]";
                $saArr = explode("::", $tmpsa);
                $arr2 = explode("=", $saArr[1]);
                $rgt = $saArr[2];
                if($id != ''){
                    $tmpgtbl->setId($id);
                    $tmphm = $tmpgtbl->getBy("*", null);
                    $tmphm = $tmphm[1];
                    #print_r(__FILE__.":tmphm:[".$gtbl->toString($tmphm[0])."]");
                    $var0 = $tmphm[0][$arr2[0]];
                    $var1 = $arr2[1];
                    if($var1 == 'USER_ID'){
                        $var1 = $userid;
                    }
                    #print "var0:$var0, var1:$var1 rgt:$rgt\n";
                    if($var0 == $var1){
                        $user->setSupAcc('supacc',true);
                        if(strpos($rgt,'r') !== false){
                            $user->setSupAcc('r', true);
                            $chkResult = true;
                        }
                        if(strpos($rgt, 'w') !== false){
                            $user->setSupAcc('w', true);
                            $chkResult = true;
                        }
                    }
                }else{
                    $user->setSupAcc('supacc',true);
                    if(strpos($rgt,'r') !== false){
                        $user->setSupAcc('r', true);
                        $chkResult = true;
                    }
                    if(strpos($rgt, 'w') !== false){
                        $user->setSupAcc('w', true);
                        $chkResult = true;
                    }
                }
            }
            $tmpgtbl = null;
       }         
    } 

    if(!$chkResult){
        //-- no cache
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
		//- return & exit
        print $out.$denymsg;
        exit;
    }
}

?>
