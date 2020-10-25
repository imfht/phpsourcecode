<?php

#embedded in act/doaddmodi.php or act/dodelete.php

#error_log(__FILE__.": act:[$act] id:[$id] triggers:[".$triggers=$gtbl->getTrigger($id)."]");
$IDTAG = 'id';
if(!isset($fieldlist)){
	$fieldlist = array();
}
#if($act == 'list-dodelete'){
if(true){
    if($id != ''){
        $tblTrigger = $gtbl->getTrigger('');
        #error_log(__FILE__.": act:[$act] id:[$id] tbl triggers:[".$triggers=$gtbl->getTrigger()."]");
        if($tblTrigger != ''){
            $gtbl->setTrigger($IDTAG, $tblTrigger);
            debug("id triggers:[".$triggers=$gtbl->getTrigger($IDTAG)."]");
            $fieldlist[] = $IDTAG;
        }
        if(is_array($hmorig) && count($hmorig) > 0){
            foreach($hmorig as $k=>$v){
                if(!defined($_REQUEST[$K]) || $_REQUEST[$k] == ''){
                    $_REQUEST[$k] = $v;
                }
            }
        }
    }
}

# some triggers bgn, added on Fri Mar 23 21:51:12 CST 2012
# see xml/fin_todotbl.xml
foreach($fieldlist as $i=>$field){
# e.g. <trigger>1::copyto::dijietbl::tuanid=id::tuanid=id</trigger>
#		0:currentFieldValue, 1:action, 2:targetTbl, 3:targetField, 4:where, 5:extra
    $triggers = $gtbl->getTrigger($field);
    if($triggers != ''){
        debug("field:$field triggers:[".$triggers."]");
        $fstArr = explode("|",$triggers);
		$tmpGtbl = new GTbl('', array(), '');
        foreach($fstArr as $k=>$trigger){
            $tArr = explode("::", $trigger);
            if($tArr[0] == 'ALL' || $tArr[0] == $gtbl->get($field)){
                $tmptbl = $tArr[2];
				$tmptbl = $tmpGtbl->setTbl($tmptbl);
                if($tArr[1] == 'copyto'){
                    $sqlchk = "select id from $tmptbl where ";
                    $chkFArr = explode(",", $tArr[4]);
                    foreach($chkFArr as $k=>$v){
                        if($v != ''){
                            $chkField = explode("=", $v);
                            $sqlchk .= $chkField[0]."='".$gtbl->get($chkField[1])."' and ";
                        }
                    }
                    $sqlchk = substr($sqlchk, 0, strlen($sqlchk)-4);
                    $sqlchk .= " limit 1";
                    $sql = "insert into ".$tmptbl." set ";
                    $sqlupd = "update ".$tmptbl." set ";
                    $fieldArr = explode(",",$tArr[3]);
                    foreach($fieldArr as $k=>$v){
                        if($v != ''){
                            $vArr = explode("=",$v);
                            if(strpos($vArr[1],"'") === 0 ||strpos($vArr[1],'"') === 0){ # hss_fin_applylogtbl.xml
                                $gtbl->set($vArr[1], substr($vArr[1],1,strlen($vArr[1])-2));
                            }
							else if($vArr[1] == 'THIS'){
                                $vArr[1] = $field;
                            }
                            $tmpfieldv = $gtbl->get($vArr[1]);
                            if($vArr[1] == 'THIS_TABLE' || $vArr[1] == 'THIS_TBL'){
                                $tmpfieldv = $tbl;
                            }
							else if($vArr[1] == 'THIS_ID'){
                                $tmpfieldv = $id;
                            }
							else if(in_array($vArr[0], $timefield)){
								$tmpfieldv = date("Y-m-d H:i:s", time()); # 'NOW()';
							}
                            $sql .= " ".$vArr[0]."='".$tmpfieldv."',";
                            $sqlupd .= " ".$vArr[0]."='".$tmpfieldv."',";
                        }
                    }
                    $sql = substr($sql, 0, strlen($sql)-1);
                    $sqlupd = substr($sqlupd, 0, strlen($sqlupd)-1);
                    debug(" trigger: sqlchk:[".$sqlchk."]");
					$tmpExtraArr = explode(',', $tArr[5]); # extra
                    $allowInsert = true;
                    if(in_array('NO_INSERT', $tmpExtraArr)){
                        $allowInsert = false;
                    }
                    $tmphm = $gtbl->execBy($sqlchk, null);
                    if(!$tmphm[0]){
                        if($allowInsert){
                            $tmphm = $gtbl->execBy($sql,null);
                        }
                        else{
                            debug('trigger skip for not allow insert. sql:['.$sql.']');
                        }
                        debug(" trigger insert sql:[".$sql."] extraArr:[".serialize($tmpExtraArr)."]");
                    }
                    else{
                        $newtmphm = $tmphm[1];
                        $sqlupd = $sqlupd." where "; #id='".$newtmphm[0]['id']."' limit 1";
                        $chkFArr = explode(",", $tArr[4]);
                        foreach($chkFArr as $k=>$v){
                            if($v != ''){
                                $chkField = explode("=", $v);
                                $sqlupd .= $chkField[0]."='".$gtbl->get($chkField[1])."' and ";
                            }
                        }
                        $sqlupd = substr($sqlupd, 0, strlen($sqlupd)-4);
                        # no limit 1?
                        $tmphm = $gtbl->execBy($sqlupd, null);
                        debug(" trigger upd sql:[".$sqlupd."]");
                    }
					#print_r($tmphm);
                }
				else if($tArr[1] == 'lockto'){
                    $sql = "replace into ".$tmptbl." set inserttime=NOW(), operator='".$userid."', ";
                    $sqlchk = "select id from $tmptbl where ";
                    $fieldArr = explode(",",$tArr[3]);
                    foreach($fieldArr as $k=>$v){
                        if($v != ''){
                            $vArr = explode("=",$v);
                            if(strpos($vArr[1],"'") === 0 ||strpos($vArr[1],'"') === 0)
                            {
                                $gtbl->set($vArr[1], substr($vArr[1],1,strlen($vArr[1])-1));
                            }else if($vArr[1] == 'THIS'){
                                $vArr[1] = $field;
                            }
                            $tmpfieldv = $gtbl->get($vArr[1]);
                            if($vArr[1] == 'THIS_TABLE' || $vArr[1] == 'THIS_TBL'){
                                $tmpfieldv = $tbl;
                            }else if($vArr[1] == 'THIS_ID'){
                                $tmpfieldv = $id;
                            }
                            $sql .= " ".$vArr[0]."='".$tmpfieldv."',";
                            $sqlchk .= " ".$vArr[0]."='".$tmpfieldv."' and";
                        }
                    }
                    $sql .= " mode='r' ";
                    $sqlchk = substr($sqlchk, 0, strlen($sqlchk)-3);
                    $tmphm = $gtbl->execBy($sql,null);
					#print_r($tmphm);
                }
				else if($tArr[1] == 'extraact'){ 
                    # see xml/hss_tuandui_shouzhitbl.xml
                    # e.g. <trigger>ALL::extraact::extra/sendmail.php::Offer入口调整修改id=THIS_ID</trigger>
                    $extraact = $tArr[2];
					$args = $tArr[3];
                    include($appdir."/".$extraact);
                }    
            }
        }
    }
}
# some triggers end, added on Fri Mar 23 21:51:12 CST 2012

?>
