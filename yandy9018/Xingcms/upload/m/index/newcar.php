<?php

if (!defined('APP_IN')) exit('Access Denied');
$list = $db ->row_select('cars','issell=0 and isshow=1','*','20','listtime');
foreach($list as $key =>$value) {
if (!empty($value['p_mainpic'])) {
$pic = explode(".",$value['p_mainpic']);
$list[$key]['p_mainpic'] = WEB_DOMAIN."/".$pic[0] ."_small".".".$pic[1];
}
$list[$key]['p_shortname'] = _substr($value['p_allname'],0,20);
$list[$key]['p_shortname2'] = _substr($value['p_allname'],0,30);
$list[$key]['listtime'] = date('Y-m-d',$value['listtime']);
$list[$key]['p_details'] = _substr($value['p_details'],0,80);
$list[$key]['p_price'] = intval($value['p_price']) == 0 ?"面谈": "￥".$value['p_price'] ."万";
$list[$key]['p_url'] ="index.php?m=cars&id=".$value['p_id'];
}
$tpl ->assign('carlist',$list);
$tpl ->display('m/newcar.html');

?>