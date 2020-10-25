<?php

include_once($appdir.'/class/insitesearch.class.php');

$MIN_CHAR_LENGTH = 4;
$MAX_CHAR_LENGTH = 255;
$tblpre = $_CONFIG['tblpre'];
$tbl_black_white = $tblpre.'issblackwhitetbl';

$fieldBlackList = array();
$tblBlackList = array();
$fieldWhiteList = array();
$tblWhiteList = array();
$tblBlackList_Init = array($tblpre.'insitesearchtbl'=>1, $tblpre.'info_objecttbl'=>1,
        $tbl_black_white=>1);
#print_r($tblBlackList);

#$iss = new InSiteSearch($gtbl->get('args_to_parent')); # args see class/GTbl
$iss = new InSiteSearch(); # not specified table related?

# in-site search sorting
if(true){

    # read black and white list
    $hm = $iss->execBy($sqlcfg="select * from $tbl_black_white "
            ." where istate=1", null, $withCache=array("iss-tbl-black-white"));
    if($hm[0]){
        $hm = $hm[1];
        foreach($hm as $k=>$v){
            if($v['isblack'] == 1){
                if($v['ifield'] == ''){
                    $tblBlackList[$v['itbl']] = 1;
                    $tblBlackList[$tblpre.$v['itbl']] = 1;
                }
                else{
                    $fieldBlackList[$v['itbl']][$v['ifield']] = 1;
                    $fieldBlackList[$tblpre.$v['itbl']][$v['ifield']] = 1;
                }
            }
            else if($v['iswhite'] == 1){
                if($v['ifield'] == ''){
                    $tblWhiteList[$v['itbl']] = 1;
                    $tblWhiteList[$tblpre.$v['itbl']] = 1;
                }
                else{
                    $fieldWhiteList[$v['itbl']][$v['ifield']] = 1;
                    $fielWhiteList[$tblpre.$v['itbl']][$v['ifield']] = 1;
                }
            }
            else{
                debug("neither black nor white with row:".serialize($v));
            }
        }
    }
    else{
        debug("$tbl_black_white read failed. 201806031132.");
    }
    $tblBlackList = array_merge($tblBlackList, $tblBlackList_Init);

    $issTblList = array();
    $sql = "show tables";
    $hm = $gtbl->execBy($sql, null, $withCache=array('key'=>$db.'-show-tables'));
    if($hm[0]){
        $hm = $hm[1];
        $issTblList = $hm;
    }
    else{
        debug("read tables failed. 201805291213.");
    }
    
    # tables
    foreach($issTblList as $k=>$tmpTblArr){
        #$tmpTbl = $tmpTblArr['Tables_in_adSystem'];
        $tmpTbl = '';
        foreach($tmpTblArr as $k2=>$v2){
            if(inString('Tables_in_', $k2)){
                $tmpTbl = $v2;
                break;
            }
        }
        #debug("k:$k tbl:$tmpTbl seri:".serialize($tmpTblArr)." k2:".$k2);
        # @todo  whitelist
        if(isset($tblBlackList[$tmpTbl])){
            continue;
        }
        else if(inString('_temp', $tmpTbl) || inString('temp_', $tmpTbl)){
            #debug($tmpMsg="found temp tmptbl:$tmpTbl, skip...\n");
            #$out .= $tmpMsg;
            continue;
        }
        else if(inString('_old', $tmpTbl) || inString('old_', $tmpTbl)){
            #debug($tmpMsg="found old tmptbl:$tmpTbl, skip...\n");
            #$out .= $tmpMsg;
            continue;
        }
        else if(preg_match("/_[0-9]+$/", $tmpTbl)){
            #debug($tmpMsg="found rotating tmptbl:$tmpTbl, skip...\n");
            #$out .= $tmpMsg;
            continue;
        }
        $issFieldList = array();
        $sql = "desc $tmpTbl";
        $hm = $gtbl->execBy($sql, null, $withCache=array('key'=>$db.'-'.$tmpTbl.'-desc'));
        if($hm[0]){
            $hm = $hm[1];
            $issFieldList = $hm;
        }
        else{
            debug("desc table:$tmpTbl failed. 201805291217.");
        }

        # fields
        $issTargetField = array();
        foreach($issFieldList as $fk=>$tmpField){
            $tmpFieldName = $tmpField['Field'];
            $tmpFieldType = $tmpField['Type'];
            $tmpName = strtolower($tmpFieldName);
            ## @todo  whitelist
            if(startsWith($tmpFieldType, 'char') || startsWith($tmpFieldType, 'varchar')){
                if(isset($fieldBlackList[$tmpTbl][$tmpFieldName])){
                    #debug($tmpMsg="found field:$tmpFieldName for blacklist, skip...\n");
                    $out .= $tmpMsg;
                    continue;
                }
                else if(checkSqlKeyword($tmpFieldName)){
                    #debug($tmpMsg="found field:$tmpFieldName for sql keyword, skip...\n");
                    #$out .= $tmpMsg;
                    continue;
                }
                else if(inString('md5', $tmpName) || inString('url', $tmpName)
                    || inString('size', $tmpName)){
                    #debug($tmpMsg="found field:$tmpFieldName for sql potential md5/url, skip...\n");
                    #$out .= $tmpMsg;
                    continue;
                }
                else if(inString('password', $tmpName) || inString('pwd', $tmpName)){
                    continue;
                }
                else{
                    $tmpLen = 0;
                    if(preg_match("/char\(([0-9]+)\)/", $tmpFieldType, $matchArr)){
                        $tmpLen = $matchArr[1];
                        if($tmpLen < $MIN_CHAR_LENGTH || $tmpLen > $MAX_CHAR_LENGTH){
                            #debug($tmpMsg="matchArr:".serialize($matchArr)." length:".$matchArr[1]
                            #    ." for $tmpFieldName, skip for out of range\n");
                            #$out .= $tmpMsg;
                        }
                        else{
                            $issTargetField[] = $tmpFieldName;
                            #debug("$tmpTbl found field:$tmpFieldName for type:$tmpFieldType."
                            #    ." targetfield:".serialize($issTargetField));
                        }
                    }
                    else{
                        debug("char field:$tmpFieldName length failed.");
                    }
                }
            }
            else{
                debug("$tmpTbl skip field:$tmpFieldName for type:$tmpFieldType.");
            }
        }
    
        # data
        if(count($issTargetField) > 0){
            $sql = "select ".implode(',', $issTargetField)." from $tmpTbl limit 0, 200";
            #debug($tmpMsg=" run sql:$sql\n");
            $out .= $tmpMsg;
            $hm = $gtbl->execBy($sql, null,
                            $withCache=array('key'=>$db.'-'.$tmpTbl.'-content-page-size=first-100'));
            if($hm[0]){
                $hm = $hm[1];
                #debug($tmpMsg="tbl-".($tbli++).":$tmpTbl read val:".count($hm)." try to save...\n");
                #$out .= $tmpMsg;
                $result = $iss->saveInfo($hm, $itbl=$tmpTbl, $idb);
                $out .= "tbl:$tmpTbl succ-count:".$result."\n\n";
            }
        }
        else{
            debug($tmpMsg="no field left for tbl:$tmpTbl. 201805291224.\n");
            $out .= $tmpMsg;
        }

    }

    # drop old
    $iss->rmOldField();

}

?>
