<?php

if (!defined('APP_IN')) exit('Access Denied');
include ('page.php');
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$car = $db ->row_select_one('cars',"p_id=".intval($id));
$car['listtime'] = date('Y-m-d',$car['p_addtime']);
$car['p_price'] = intval($car['p_price']) == 0 ?"面谈": $car['p_price'] ."万元";
if (!empty($car['p_details'])) {
$car['p_details'] = $car['p_details'];
}
if (!empty($car['p_model'])) $car['p_modelname'] = $commoncache['modellist'][$car['p_model']];
if (!empty($car['p_pics'])) {
$piclist = explode('|',$car['p_pics']);
$spiclist = array();
foreach($piclist as $k =>$v) {
$pic = explode(".",$v);
$spiclist[$k] = $pic[0] ."_small".".".$pic[1];
}
$tpl ->assign('pic_list',$piclist);
}else {
$tpl ->assign('pic_list',"");
}
if($settings['version']==2 or $settings['version']==3){
if($car['uid']!=0){
$user = $db ->row_select_one('member','id='.$car['uid']);
$tpl ->assign('shop',$user);
}
}
$tpl ->assign('cars',$car);
$samecarlist = get_carlist(0,"issell=0 and isshow=1 and p_brand=".$car['p_brand']." and p_id!=".$car['p_id'],'5','listtime desc');
$tpl ->assign('samecar',$samecarlist);
if(!empty($car['p_price'])) {
$where = "issell=0 and isshow=1 and p_id!=".$car['p_id'];
$price = $car['p_price'];
if ($price <= 5) {
$where .= " and p_price<5";
}elseif ($price >5 and $price <= 8) {
$where .= " and p_price>=5 and p_price<8";
}elseif ($price >8 and $price <= 12) {
$where .= " and p_price>=8 and p_price<12";
}elseif ($price >12 and $price <= 18) {
$where .= " and p_price>=12 and p_price<18";
}elseif ($price >18 and $price <= 25) {
$where .= " and p_price>=18 and p_price<25";
}elseif ($price >25 and $price <= 35) {
$where .= " and p_price>=25 and p_price<35";
}elseif ($price >35 and $price <= 50) {
$where .= " and p_price>=35 and p_price<50";
}elseif ($price >50) {
$where .= " and p_price>=50";
}
$samepricecarlist = get_carlist(0,$where,'5','listtime desc');
$tpl ->assign('samepricecars',$samepricecarlist);
}
$tpl ->display('m/cars.html');

?>