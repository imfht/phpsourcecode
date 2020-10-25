<?php
# embeded in xml/hss_info_objectindexkeytbl, write stat data when modifying
# wadelau@ufqi.com,  Tue Jul  3 21:53:25 CST 2012


if(1){
   
    $targettbl = '';
    $sql = "select tblname from ".$_CONFIG['tblpre']."info_objecttbl where id='".$_REQUEST['parentid']."' limit 1";
    $hm = $gtbl->execBy($sql, null);
    if($hm[0]){
        $targettbl = $hm[1][0]['tblname'];
    }
    
    $myindexname = trim($_REQUEST['indexname']);
    $myindexname = str_replace("`","", $myindexname);
    $myonfield = trim($_REQUEST['onfield']);
    $myonfield = str_replace("`","", $myonfield);

    if($act == 'list-dodelete'){
        $sql = "alter table $targettbl drop index ".$hmorig['fieldname'];

    }else if($_REQUEST['id'] == ''){

        $sql = "alter table $targettbl add ".$_REQUEST['indextype']." index ".$myindexname;
        $sql .= "(".$myonfield.")";
    
    }else if(1){

        $sql = "alter table $targettbl drop index ".$hmorig['fieldname'];
        $gtbl->execBy($sql, null);

        $sql = "alter table $targettbl add ".$_REQUEST['indextype']." index ".$myindexname;
        $sql .= "(".$myonfield.")";

    }

    $gtbl->execBy($sql, null);
    error_log(__FILE__.": act:$act, req_id:".$_REQUEST['id']."  sql:[".$sql."]");
}

?>
