<?php
# embeded in xml/info_objecttbl.xml, sync field modifications to tbl
# wadelau@ufqi.com, Mon Jul  2 16:37:07 CST 2012
# Wed Oct 22 08:29:09 CST 2014

if(1){
    
	$objFieldTbl = $_CONFIG['tblpre']."info_objectfieldtbl";
	
    if($objectid == ""){
        $objectid = $user->getObjId();
    }
    if($objectid == ""){
        $isql = "select id from ".$_CONFIG['tblpre']."info_objecttbl where tblname='".$tbl."' limit 1";
        $hm = $gtbl->execBy($isql, null);
        if($hm[0]){
            $hm = $hm[1][0];
            $objectid = $hm['id'];
        }
    }
	$objectid = $objectid == '' ? 0 : $objectid;
	
	# bugfix for delete trigger, Thu Aug  9 21:44:39 CST 2018
    # in case that delete in info_objecttbl
    $tmpId = trim($_REQUEST[$gtbl->getMyId()]);
    if($tmpId > 0 && $act == 'list-dodelete'){
        $hm = $gtbl->execBy($tmpsql="delete from ".$objFieldTbl." where parentid='".$tmpId."'", null);
        debug("objfieldtbl sync: sql:[$tmpsql] result:[".serialize($hm)."]");
    }
    else if($act != 'list-dodelete'){

    # tblfield 
    $tblfield = $objFieldTbl;
    #error_log(__FILE__.": tbl:[$tbl] ");
    $sql = "desc $tbl";
    $hm = $gtbl->execBy($sql, null);
    if($hm[0]){
        $hm = $hm[1];
        foreach($hm as $k=>$v){
            $isql = "replace into $tblfield set parentid='".$objectid."', ";
            $fieldtype = $v['Type'];
            $fieldlength = 0;
            $tmpcount = preg_match("/([a-z]+)\(([0-9]*)\)/", $v['Type'], $matches);
            if($tmpcount > 0)
            {
                $fieldtype = $matches[1];
                $fieldlength = $matches[2];
            }
            if(strpos($fieldtype,"'") !== false || strpos($fieldtype,'"') !== false){
              $fieldtype = addslashes($fieldtype);    
            }
            $isql .= " fieldname='".$v['Field']."', fieldtype='".$fieldtype."', fieldlength=$fieldlength,";
            $isql .= " defaultvalue='".$v['Default']."', otherset='".$v['Extra']."'";
            $gtbl->execBy($isql, null);
            error_log(__FILE__.": records:".$gtbl->toString($v).", isql:$isql");
        }
    }

    #tblindex
    $tblindex = "".$_CONFIG['tblpre']."info_objectindexkeytbl";
    $sql = "show create table $tbl";
    $hm = $gtbl->execBy($sql, null);
    if($hm[0]){
        $hm = $hm[1];
        $keys = $hm[0]['Create Table'];
        $keysarr = explode("\n", $keys);
        foreach($keysarr as $k=>$v){
            $v = trim($v);
            $indextype = "";
            $indexname = "";
            $indexfield = "";
            $tmpcount = 0;
            if(strpos($v, " KEY ") !== false){
                if(strpos($v, "PRIMARY KEY ") !== false){
                    $tmpcount = preg_match("/(.+) KEY \((.*)\).*/", $v, $matches);
                    if($tmpcount > 0){
                        $indextype = $matches[1];
                        $indexname = "";
                        $indexfield = $matches[2];
                    }
                }else if(strpos($v, "UNIQUE KEY ") !== false){
                    $tmpcount = preg_match("/(.+) KEY ([^\(]+) \((.*)\).*/", $v, $matches);
                    if($tmpcount > 0){
                        $indextype = $matches[1];
                        $indexname = $matches[2];
                        $indexfield = $matches[3];
                    }
                }else{
                    $tmpcount = preg_match("/KEY ([^\(]+) \((.*)\).*/", $v, $matches);
                    if($tmpcount > 0){
                        $indextype = '';
                        $indexname = $matches[1];
                        $indexfield = $matches[2];
                    }
                }
                if($tmpcount > 0){
                    #error_log(__FILE__.": key:$v, matches:[".$gtbl->toString($matches)."]");
                }
                $isql = "replace into $tblindex set parentid='".$objectid."', indexname='".$indexname."',";
                $isql .= " indextype='".$indextype."', onfield='".$indexfield."'";
                $gtbl->execBy($isql, null);
                error_log(__FILE__.": keysarr:[".$gtbl->toString($v)."], isql:[$isql] matches:[".$gtbl->toString($matches)."]");
            }
        }
    }
	
	}
	
}

?>
