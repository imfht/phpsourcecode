<?php

if(!defined('APP_IN')) exit('Access Denied');
$id = isset($_GET['id']) ?intval($_GET['id']) : 0;
if($id!=0){
$adstr = "";
$data = $db->row_select_one('ad','id='.$id);
if($data['isshow']==1 and ($data['endtime']-time()>=0)){
if($data['adtype']==1){
$adstr = "<a href='".$data['url']."' title='".$data['url_note']."' target='_blank'><img src='".$data['pic']."' alt='".$data['url_note']."' width=".$data['picwidth']." height=".$data['picheight']."/></a>";
}
else{
$adstr = "<a href='".$data['url']."' title='".$data['url_note']."' target='_blank'>".$data['name']."</a>";
}
}
echo "document.write(\"".$adstr."\");";
}

?>