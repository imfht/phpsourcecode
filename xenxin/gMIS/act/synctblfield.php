<?php
# embeded in xml/hss_info_objectfieldtbl, write stat data when modifying
# wadelau@ufqi.com,  Mon Jul  2 21:58:25 CST 2012

if(1){
   
    $targettbl = '';
    $sql = "select tblname from ".$_CONFIG['tblpre']."info_objecttbl where id='".$_REQUEST['parentid']."' limit 1";
    $hm = $gtbl->execBy($sql, null);
    if($hm[0]){
        $targettbl = $hm[1][0]['tblname'];
    }
    
    $fieldlength = $_REQUEST['fieldlength'];
    

    if($act == 'list-dodelete'){
        $sql = "alter table $targettbl drop ".$hmorig['fieldname'];

    }else if($_REQUEST['id'] == ''){

        $sql = "alter table $targettbl add ".$_REQUEST['fieldname']." ".trim($_REQUEST['fieldtype']);
        if(intval($fieldlength) > 0){
            $sql .= "(".$fieldlength.")";   
        }else if(($_REQUEST['fieldtype'] == 'datetime' || $_REQUEST['fieldtype'] == 'date') && 
                ($_REQUEST['defaultvalue'] == '' || $_REQUEST['defaultvalue'] == '0') ){
            $_REQUEST['defaultvalue'] = '0000-00-00 00:00:00';
            $tmpsql = "update ".$_CONFIG['tblpre']
                ."info_objectfieldtbl set defaultvalue='0000-00-00 00:00:00' where fieldname='"
                .$_REQUEST['fieldname']."' and parentid='".$_REQUEST['parentid']."' limit 1";
            $gtbl->execBy($tmpsql, null);
        }
        $sql .= " not null default '".$_REQUEST['defaultvalue']."' ".$_REQUEST['otherset'];
    
    }else if(1){
        $sql = "alter table $targettbl modify ".$_REQUEST['fieldname']." ".trim($_REQUEST['fieldtype']);
        if(intval($fieldlength) > 0){
            $sql .= "(".$_REQUEST['fieldlength'].")";
        }else if($_REQUEST['fieldtype'] == 'datetime' && 
                ($_REQUEST['defaultvalue'] == '' || $_REQUEST['defaultvalue'] == '0') ){
            $_REQUEST['defaultvalue'] = '0000-00-00 00:00:00';
            $tmpsql = "update ".$_CONFIG['tblpre']
                ."info_objectfieldtbl set defaultvalue='0000-00-00 00:00:00' where fieldname='"
                .$_REQUEST['fieldname']."' and parentid='".$_REQUEST['parentid']."' limit 1";
            $gtbl->execBy($tmpsql, null);
        }
        $sql .= " not null default '".$_REQUEST['defaultvalue']."' ".$_REQUEST['otherset'];
    }

    $gtbl->execBy($sql, null);
    error_log(__FILE__.": act:$act, req_id:".$_REQUEST['id']."  sql:[".$sql."]");
}

?>
