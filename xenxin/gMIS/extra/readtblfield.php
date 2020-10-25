<?php
# read field list of a table, answer ido.js::fillSubSelect 
# Sun Mar 11 15:44:20 CST 2012

require("../comm/header.inc.php");

$thefield = $_REQUEST['field'];
$url = $_SERVER['PHP_SELF']."?bkl=".$_REQUEST['bkl'];
$logicid = $_REQUEST['logicid'];

$out = "";

$objectid = $_REQUEST['objectid'];
$tbl = $_CONFIG['tblpre']."info_objecttbl";
if(isset($_REQUEST['tbl'])){ $tbl = $_CONFIG['tblpre'].$_REQUEST['tbl']; }

$objArr = array();
if($logicid == 'mingcheng' || startsWith($objectid, "fromtable")){
    $objArr = explode("__", $objectid); # <selectoption>hss_tuanduitbl__mingcheng:团款|hss_info_gouwudiantbl__mingcheng:商店流水|2:导游人头|3:摄像|4:其他</selectoption>
    $tbl = $objArr[0];
    $_REQUEST['tbl'] = $tbl;
    #print_r($objArr);
}

$gtbl = new GTbl($tbl, array(), $elementsep);

$hm = null;
if($logicid == 'mingcheng'){
	#
}else if($logicid == 'xiane'){
    $hm = $gtbl->getBy("*", "id='".$objectid."'");
    if($hm[0]){
        $hm = $hm[1];
        $tbl = $hm[0]['tblname'];
        $_REQUEST['tbl'] = $tbl;
    }
}else if($logicid == "leibie"){
	$tbl = $_REQUEST['tbl'];
		if(strpos($tbl,$_CONFIG['tblpre']) !== 0){
			$tbl = $_CONFIG['tblpre'].$tbl; # 兼容不是以 hss_ 开头的表名
		}

}
#print_r($hm);

include("../comm/tblconf.php");

if($logicid == 'xiane'){
    #print_r($hmfieldsort);
    foreach($hmfieldsort as $k=>$v){
        $out .= "$v:::".$gtbl->getCHN($v)."\n";
    }
}else if($logicid == "mingcheng"){
    $hm = $gtbl->getBy("id,".$objArr[1], null);
    if($hm[0]){
        $hm = $hm[1];
    }
    foreach($hm as $k=>$v){
       //print_r($v);
       $out .= $v['id'].":::".$v[$objArr[1]]."\n"; 
    }
}else if($logicid == "leibie"){
	$hm = $gtbl->getBy("id,chnname", "parentid='".$objectid."'");
    if($hm[0]){
        $hm = $hm[1];
    }
	#print_r($hm);
    foreach($hm as $k=>$v){
       //print_r($v);
       $out .= $v['id'].":::".$v['chnname']."\n"; 
    }
}
else if($logicid == 'tblname'){ # info_objectfieldtbl, remedy Thu, 8 Dec 2016 19:47:41 +0800
    $hm = $gtbl->getBy("id,$logicid", null);
    if($hm[0]){
        $hm = $hm[1];
    }
    $data['respobj'] = json_encode(array('thefield'=>''.$thefield, 'result_list'=>$hm, 'dispfield'=>$logicid));
    $fmt = $_REQUEST['fmt'] = 'json';
}
else if($logicid != ''){
    $hm = $gtbl->getBy("id,$logicid", "parentid='".$objectid."'");
	if($hm[0]){
        $hm = $hm[1];
	}
	$data['respobj'] = json_encode(array('thefield'=>''.$thefield, 'result_list'=>$hm, 'dispfield'=>$logicid));
	$fmt = $_REQUEST['fmt'] = 'json';
}
else{
	$out .= "Unknown logicid:[$logicid]. [1512081131]";
}

$isoput = false;

require("../comm/footer.inc.php");


?>
