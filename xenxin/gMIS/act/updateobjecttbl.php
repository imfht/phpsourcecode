<?php

# read xml conf and save table chnname in hss_info_objecttbl
# Mon Feb 13 22:26:18 CST 2012

require("../comm/header.inc.php");

$act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$tbl = $_REQUEST['tbl'];
$mydb = $_CONFIG['appname'].'db';
$db = $_REQUEST['db']==''?$mydb:$_REQUEST['db'];
$field = $_REQUEST['field'];

$db =  $_CONFIG['maindb'] ; # 
$objecttbl = $_CONFIG['tblpre']."info_objecttbl";

$hmconf = GTbl::xml2hash($xmlpathpre, $elementsep, $db, $tbl);
$gtbl = new GTbl($tbl, $hmconf[0], $elementsep);

$out = ""; # no more output is needed, Mon Jul  2 17:57:35 CST 2012

$sql = "show tables";
$hm = $gtbl->execBy($sql, null);
if($hm[0]){
    $hm = $hm[1];
    #print_r($hm);
    foreach($hm as $k=>$v){
       $tbl = $v['Tables_in_'.trim($db)];
        #print "$k => ".$tbl."\n"; 
       if($tbl != ""){
           $tmpconf = GTbl::xml2hash($xmlpathpre, $elementsep, $db, $tbl);
           $tmpgtbl = new GTbl($tbl, $tmpconf[0], $elementsep);
           $tblchn = $tmpgtbl->getTblCHN();
            #print "tbl:$tbl, chnname:".$tblchn;
           if($tblchn == ''){
               $tblchn = $tbl;     
           }
           $tmphm = $tmpgtbl->execBy("select id from $objecttbl where tblname='".$tbl."'",null);
            #print_r($tmphm);
           $objectid = $tmphm[1][0]['id'];
           if($objectid > 0){
               $sql = "update IGNORE $objecttbl set objname='".$tblchn."',operator='sysop',updatetime=NOW() where tblname='".$tbl."'";
           }else{
               $sql = "insert IGNORE into $objecttbl set objname='".$tblchn."', tblname='".$tbl."',operator='sysop',updatetime=NOW()";
           }
            #print "sql:$sql\n";
           $tmpgtbl->execBy($sql, null);
           $out .= "<br/>".$sql;

            # syncfield bgn, Mon Jul  2 16:42:49 CST 2012
           include($appdir."/act/updateobjectfieldtbl.php");
            # syncfield end, Mon Jul  2 16:42:49 CST 2012

       }else{
           $out .= "<br/> Empty tblname found. k:[$k] v:[$v].";
       }
    }
    $out .= "<br/><br/>&nbsp;";
}

require("../comm/footer.inc.php");

print $out;

?>
