<?php
# be part of /jdo.php

$isadmin = false;
$hlcolor = '#afc4e2'; $form_cols = 6; $hashiddenfield = false;

$hmconf = GTbl::xml2hash($xmlpathpre, $elementsep, $db, $tbl);
$hmconf[0]['mydb'] = $mydb;
$hmconf[0]['lang'] = $lang; # + $lang, Tue Nov  5 04:14:33 UTC 2019
#print_r($hmconf);
$gtbl = new GTbl($tbl, $hmconf[0], $elementsep, $tblrotate);
$tbl = $gtbl->getTbl();
$hmfield = $hmfieldsort = array();
$hmfieldsortinxml = $hmconf[1];

$sql = "desc $tbl";
$hm = $gtbl->execBy($sql, null, $withCache=array('key'=>$tbl."-desc"));

$max_idx = $hmi = 99; # max number of fields count
$min_idx = 0; $dispi = 0; $max_disp_cols = $gtbl->getListFieldCount(); # display field count
$hasid = false; $hmj = count($hmfieldsortinxml); #1; remedy Sun Jul 22 22:26:09 CST 2012
$priuni = array(); # primary key and/or unique key
if($hm[0]){
    $hm = $hm[1];
    foreach($hm as $k=>$v){
        $field = $v['Field'];
        $fieldv = "fieldtype=".$v['Type'];
        if(strtolower($field)=='id'){
            $field = strtolower($field);
        }
        else if(strtolower($field) == 'name' || strtolower($field) == 'type'
			|| checkSQLKeyword($field)){
            $out .= __FILE__.": field:[".$field."] in tbl:[".$tbl
				."]. It's bad idea to name a field as 'name' or 'type' or SQL reserved keywords. Plz change it to xxxname or namexxx.\n";
        }
        $hmfield[$field] = $fieldv;
        $hmfield[$field."_default"] = $v['Default'];
        $tmpsort = $hmfieldsortinxml[$field];
        if($tmpsort == null || $tmpsort == ''){
            $tmpsort = $hmj++; # $hmi; # remedy on Wed Jul 11 18:57:32 CST 2012
            $hmi--;
        }
        $hmfieldsort[$tmpsort] = $field;
        $min_index = $tmpsort;
        if(!$hasid && $field == $gtbl->getMyId()){
            $hasid = true;
        }
		if($v['Key'] == 'PRI'){
            $priuni[$v['Key']][] = $v['Field'];
        }
        else if($v['Key'] == 'UNI' || $v['Key'] == 'MUL'){
            $priuni[$v['Key']][] = $v['Field'];
        }
    }
}
#print_r($hmfield);
#print_r($hmfieldsort);
if(count($priuni) < 1){ # no pri and no uni, fucking the tbl!
    foreach ($hmfield as $k=>$v){
        if(strpos($k, '_default') === false){
            $priuni['UNI'][] = $k;
            if($ki++ > $max_disp_cols){
                break;
            }
        }
    }
}
else{
    if(count($priuni['PRI']) == 1){
        $tmpId = $priuni['PRI'][0];
        if($tmpId != $gtbl->getMyId()){
            $gtbl->setMyId($tmpId);
            if(!$hasid){ $hasid = true; }
        }
    }
    else if(count($priuni['UNI']) == 1){
        $tmpId = $priuni['UNI'][0];
        if($tmpId != $gtbl->getMyId()){
            $gtbl->setMyId($tmpId);
            if(!$hasid){ $hasid = true; }
        }
    }
}
if($gtbl->getMyId() == 'id' && !isset($hmfield['id'])){
    $gtbl->setMyId(''); $hasid = false;
}
else if($hasid){
	$myId = $gtbl->getMyId();
    $hmfieldsortN = array(0=>$myId); # put id as the first if available
    foreach ($hmfieldsort as $k=>$v){
        if($v == $myId){ continue; }
        $hmfieldsortN[$k+1] = $v;
    }
    $hmfieldsort = $hmfieldsortN;
}
$gtbl->set($gtbl->PRIUNI, $priuni);
$gtbl->set('hasid', $hasid);

$hmsize = count($hmfield) + 1;
$gtbl->setFieldSort($hmfieldsort, $hmsize, $hmi);
$gtbl->setFieldList($hmfield);

$opfield = array('operator','author','op','creator','operatorid', 'authorid', 'creatorid',
        'insertu', 'updateu', 'ioperator', 'soperator');
$timefield = array('inserttime','insertime','updatetime','starttime','endtime','editime','edittime',
        'modifytime','created','dinserttime', 'dupdatetime', 'dstarttime', 'dendtime');

$idName = $gtbl->getMyId(); # need to replace all following, Fri, 16 Dec 2016 19:43:16 +0800
$id = $_REQUEST[$idName];
if($id == '' && isset($_REQUEST['pnsk'.$idName])){
    $id = $_REQUEST['pnsk'.$idName];
}
#print __FILE__.": hasid:[$hasid] id-name:[".$gtbl->getMyId()."] id-value:[$id] priuni:";
#print_r($priuni);

?>
