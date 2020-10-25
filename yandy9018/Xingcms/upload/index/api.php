<?php

if (!defined('APP_IN')) exit('Access Denied');
include ('page.php');
include (INC_DIR.'api.func.php');
$mod_name = '车源api';
$ac_arr = array('search'=>'查询车源','cars'=>'车源详情','brand'=>'品牌列表','price'=>'价格列表','age'=>'车龄列表','kilometre'=>'里程列表','transmission'=>'变速箱列表','gas'=>'排放量列表','color'=>'颜色列表','source'=>'来源列表','class'=>'级别列表','picture'=>'图片列表','subass'=>'提交评估信息','subcars'=>'提交发布车源信息','consult'=>'返回我的咨询信息','appraiser'=>'评估师信息','compcar'=>'对比车源信息');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : '';
if($ac=="search"){
$page = isset($_GET['page']) ?intval($_GET['page']) : 0;
$status = isset($_GET['s']) ?intval($_GET['s']) : 0;
$brand = isset($_GET['brand']) ?intval($_GET['brand']) : 0;
$subbrand = isset($_GET['subbrand']) ?intval($_GET['subbrand']) : 0;
$price = isset($_GET['price']) ?intval($_GET['price']) : 0;
$age = isset($_GET['age']) ?intval($_GET['age']) : 0;
$kilometre = isset($_GET['kilometre']) ?intval($_GET['kilometre']) : 0;
$transmission = isset($_GET['transmission']) ?intval($_GET['transmission']) : 0;
$gas = isset($_GET['gas']) ?intval($_GET['gas']) : 0;
$color = isset($_GET['color']) ?intval($_GET['color']) : 0;
$source = isset($_GET['source']) ?intval($_GET['source']) : 0;
$class = isset($_GET['class']) ?intval($_GET['class']) : 0;
$picture = isset($_GET['picture']) ?intval($_GET['picture']) : 0;
$keywords = isset($_GET['keywords']) ?intval($_GET['keywords']) : 0;
echo search_cars($status,$page,$brand,$subbrand,$price,$age,$kilometre,$transmission,$gas,$color,$source,$class,$picture,$aid,$cid,$uid,$keywords);
}
elseif($ac=="cars"){
$id = isset($_GET['id']) ?intval($_GET['id']) : 0;
echo search_cars_detail($id);
}
elseif($ac=="subass"){
$uid = isset($_GET['uid']) ?intval($_GET['uid']) : 0;
$brand = isset($_GET['brand']) ?intval($_GET['brand']) : 0;
$subbrand = isset($_GET['subbrand']) ?intval($_GET['subbrand']) : 0;
$kilometre = isset($_GET['kilometre']) ?intval($_GET['kilometre']) : 0;
echo submit_assess($uid,$brand,$subbrand,$kilometre);
}
elseif($ac=="subcars"){
$brand = isset($_GET['brand']) ?intval($_GET['brand']) : 0;
$subbrand = isset($_GET['subbrand']) ?intval($_GET['subbrand']) : 0;
$kilometre = isset($_GET['kilometre']) ?intval($_GET['kilometre']) : 0;
$pics = isset($_GET['pics']) ?intval($_GET['pics']) : 0;
$year = isset($_GET[' year']) ?intval($_GET[' year']) : 0;
$month = isset($_GET['month']) ?intval($_GET['month']) : 0;
$color = isset($_GET['color']) ?intval($_GET['color']) : 0;
$price = isset($_GET['price']) ?intval($_GET['price']) : 0;
$transmission = isset($_GET['transmission']) ?intval($_GET['transmission']) : 0;
$details = isset($_GET['details']) ?intval($_GET['details']) : 0;
$uid = isset($_GET['uid']) ?intval($_GET['uid']) : 0;
$carname = isset($_GET['carname']) ?intval($_GET['carname']) : 0;
$model = isset($_GET['model']) ?intval($_GET['model']) : 0;
echo submit_cars($uid,$brand,$subbrand,$carname,$year,$month,$color,$details,$price,$transmission,$kilometre,$pics,$model,$tel);
}
elseif($ac=="brand"){
echo select_brands();
}
elseif($ac=="price"){
echo select_price();
}
elseif($ac=="age"){
echo select_age();
}
elseif($ac=="kilometre"){
echo select_kilometre();
}
elseif($ac=="transmission"){
echo select_transmission();
}
elseif($ac=="gas"){
echo select_gas();
}
elseif($ac=="color"){
echo select_color();
}
elseif($ac=="class"){
echo select_class();
}
elseif($ac=="picture"){
echo select_picture();
}
elseif($ac=="consult"){
$uid = isset($_GET['uid']) ?intval($_GET['uid']) : 0;
echo select_consult($uid);
}
elseif($ac=="appraiser"){
echo select_appraiser();
}
elseif($ac=="compcar"){
$ids=isset($_GET['ids']) ?$_GET['ids'] : 0;
echo select_compcar($ids);
}
else{
exit;
}

?>