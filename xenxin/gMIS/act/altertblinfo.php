<?php
# embeded in xml/hss_info_objecttbl, write stat data when modifying
# wadelau@ufqi.com,  Mon Jul  9 20:58:11 CST 2012


if(1){
   
    $objecttbl = $_CONFIG['tblpre']."info_objecttbl";

    $targettbl = trim($_REQUEST['tblname']);
    if($targettbl == ''){
        $targettbl = $hmorig['tblname'];
    }
    if(!startsWith($targettbl, $_CONFIG['tblpre'])){
        $targettbl = $_CONFIG['tblpre'].$targettbl; 
    }
    
    if($act == 'list-dodelete'){
        $sql = "show tables like '%$targettbl%'";
        $hm = $gtbl->execBy($sql, null);
        if($hm[0]){
            $isql = "select * from $targettbl limit 1";
            $ihm = $gtbl->execBy($isql, null);
            if($ihm[0]){
                $ihm = $ihm[1][0];
                if($ihm['id'] > 0){
                    # 
                    debug(__FILE__.": ERROR! act:$act, req_id:".$_REQUEST['id']
                            ."  targettbl:[$targettbl] is not empty.");
                }else{
                
                    $sql = "drop table $targettbl";
                }
            }else{
                
                $sql = "drop table $targettbl";
            }

        }
		else{
            $sql = "";
            debug(__FILE__.": act:$act, req_id:".$_REQUEST['id']."  sql:["
                    .$sql."] targettbl:[$targettbl] does not exist.");
        }
        if($sql != ""){
            $gtbl->execBy($sql, null);
        }
		
		$tblorig = $tbl;
        $tbl = $targettbl;
        # syncfield bgn, Mon Jul  2 16:42:49 CST 2012
        include($appdir."/act/updateobjectfieldtbl.php");
        # syncfield end, Mon Jul  2 16:42:49 CST 2012
        $tbl = $tblorig;

    }
	else if(trim($_REQUEST['id']) == ''){

        $sql = "create table $targettbl(id int(11) not null auto_increment,primary key(id))";
        if($targettbl != trim($_REQUEST['tblname'])){
            $isql = "update $objecttbl set tblname='".$targettbl
                ."' where tblname='".$_REQUEST['tblname']."' limit 1";
            $gtbl->execBy($isql, null);
        }
        
        if($sql != ""){
            $gtbl->execBy($sql, null);
        }
        
        $tblorig = $tbl;
        $tbl = $targettbl;
        # syncfield bgn, Mon Jul  2 16:42:49 CST 2012
        include($appdir."/act/updateobjectfieldtbl.php");
        # syncfield end, Mon Jul  2 16:42:49 CST 2012
        $tbl = $tblorig;
    
    }else if(1){

        # modify does not need action
    }

    debug(__FILE__.": act:$act, req_id:".$_REQUEST['id']."  sql:[".$sql."]");
}

?>
