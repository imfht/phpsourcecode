<?php
$isoput = false;
require("../comm/header.inc.php");
#require("../class/gtbl.class.php");

$tbl = $field = $fieldv = $fieldargv = $filename = $act = '';
$act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$tbl = $_REQUEST['tbl'];
$mydb = $_CONFIG['appname'].'db';
$db = $_REQUEST['db']==''?$mydb:$_REQUEST['db'];
$field = $_REQUEST['field'];
$url = $rtvdir."/ido.php?sid=".$sid;

$url = mkUrl($url, $_REQUEST);

$otbl = $_REQUEST['otbl'];
$oid = $_REQUEST['oid'];
$lfield = $_REQUEST['linkfield'];
#if($oid == ''  && $otbl != $_CONFIG['maintbl']){
if($_REQUEST['linkfieldval'] != ''){ # added on Mon Mar 19 21:09:43 CST 2012
    
    $linkfv = $_REQUEST['linkfieldval'];
    $hmconf = GTbl::xml2hash($xmlpathpre, $elementsep, $db, $otbl);
    $gtbl = new GTbl($otbl, $hmconf[0], $elementsep);
    $gtbl->setId($oid);
    $hm = $gtbl->getBy($linkfv, "id=?");
    if($hm[0]){
        $hm = $hm[1][0];
        $id = $hm[$linkfv];
    }

}else{
    $id = $oid;
}

$url = preg_replace("/linkfield=([0-9a-zA-Z]*)/", "pnsk\$1=".$id, $url);
$url = preg_replace("/&id=([0-9]*)/", "", $url);

if(strpos($url,"linkfield2") > 1){
    $url = preg_replace("/linkfield2=([0-9a-z]*)/", "pnsk\$1=".$otbl, $url);
    $url .= "&pnsm=1"; # page navigator search mode, see in class/pagenavi.class.php
}

$out .= "<!-- <br/> --> <table width=\"100%\" >";

$out .= "<tr><td width=\"100%\" height=\"400px\">";
if($id == '' || $id == '0'){
    $out .= " &nbsp;&nbsp;&nbsp; id為空, 請先填寫其他信息，然後保存後再添加項內容.<br/>&nbsp;<br/>&nbsp;";   
}else{
    $out .= "<iframe id=\"linktblframe\" name=\"linktblframe\" width=\"100%\" height=\"100%\" src=\"".$url."&isheader=0&needautopickup=no\" frameborder=\"0\"></iframe>";
}
$out .= "</td></tr>";

$out .= "</table>";

require("../comm/footer.inc.php");

?>
