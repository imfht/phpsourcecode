<?php

if (!defined('APP_IN')) exit('Access Denied');
$tpl ->assign('menustate',10);
$brand_search = arr_brand_with_search();
$select_brand_search = select_make('',$brand_search,'请选择品牌');
$tpl->assign( 'selectbrandsearch',$select_brand_search );
$where = "isshow=1";
if(!empty($_COOKIE['city'])){
$where .= " and cid = ".$_COOKIE['city'];
}
if (isset($_GET['clear']) and $_GET['clear'] == 1) {
setMyCookie("area",'',time() -COOKIETIME);
setMyCookie("keywords",'',time() -COOKIETIME);
setMyCookie("brand",'',time() -COOKIETIME);
setMyCookie("subbrand",'',time() -COOKIETIME);
setMyCookie("price",'',time() -COOKIETIME);
setMyCookie("model",'',time() -COOKIETIME);
setMyCookie("show",'',time() -COOKIETIME);
setMyCookie("order",'',time() -COOKIETIME);
}
$arr_p = array('1'=>'3万以下','2'=>'3-5万','3'=>'5-8万','4'=>'8-12万','5'=>'12-18万','6'=>'18-24万','7'=>'24-35万','8'=>'35-50万','9'=>'50-100万','10'=>'100万以上');
$tpl ->assign('arr_price',$arr_p);
$arr_b = arr_brand_recom();
$tpl ->assign('arr_brand',$arr_b);
if (isset($_GET['c'])) {
$arr_c = explode("_",trim($_GET['c']));
if ($arr_c['0'] == "p") {
if (isset($arr_c[1])) {
setMyCookie("price",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['price']) and $_COOKIE['price'] == 0) {
setMyCookie("price",'',time() -COOKIETIME);
}
}
elseif ($arr_c['0'] == "m") {
if (isset($arr_c[1])) {
setMyCookie("model",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['model']) and $_COOKIE['model'] == 0) {
setMyCookie("model",'',time() -COOKIETIME);
}
}
elseif ($arr_c['0'] == "a") {
if (isset($arr_c[1])) {
setMyCookie("age",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['age']) and $_COOKIE['age'] == 0) {
setMyCookie("age",'',time() -COOKIETIME);
}
}
elseif ($arr_c['0'] == "b") {
if (isset($arr_c[1])) {
setMyCookie("brand",intval($arr_c[1]),time() +COOKIETIME);
if(!empty($_GET['sb'])){
setMyCookie("subbrand",intval($_GET['sb']),time() +COOKIETIME);
}
}
if (isset($_COOKIE['brand']) and $_COOKIE['brand'] == 0) {
setMyCookie("brand",'',time() -COOKIETIME);
setMyCookie("subbrand",'',time() -COOKIETIME);
}
}
elseif ($arr_c['0'] == "c") {
if (isset($arr_c[1])) {
setMyCookie("area",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['area']) and $_COOKIE['area'] == 0) {
setMyCookie("area",'',time() -COOKIETIME);
}
}
}
if (isset($_COOKIE['subbrand']) and isset($_GET['sb']) and  $_GET['sb'] == 0) {
setMyCookie("subbrand",'',time() -COOKIETIME);
}
if (isset($_COOKIE['brand']) and $_COOKIE['brand']<>0) {
$where .= " and p_brand = ".$_COOKIE['brand'];
}
if (isset($_COOKIE['subbrand']) and $_COOKIE['subbrand'] <>0) {
$subbrand = $db ->row_select_one('brand','b_id='.$_COOKIE['subbrand'],'b_name');
$tpl ->assign('subrandname',$subbrand['b_name']);
$where .= " and p_subbrand = ".$_COOKIE['subbrand'];
}
if (isset($_COOKIE['price']) and $_COOKIE['price']<>0) {
switch ($_COOKIE['price']) {
case 1:
$where .= " and p_price > 0 and p_price <= 3";
break;
case 2:
$where .= " and p_price > 3 and p_price <= 5";
break;
case 3:
$where .= " and p_price > 5 and p_price <= 8";
break;
case 4:
$where .= " and p_price > 8 and p_price <= 12";
break;
case 5:
$where .= " and p_price > 12 and p_price <= 18";
break;
case 6:
$where .= " and p_price > 18 and p_price <= 24";
break;
case 7:
$where .= " and p_price > 24 and p_price <= 35";
break;
case 8:
$where .= " and p_price > 35 and p_price <= 50";
break;
case 9:
$where .= " and p_price > 50 and p_price <= 100";
break;
case 10:
$where .= " and p_price > 100";
break;
default:
$where .= "";
}
}
if (isset($_COOKIE['age']) and $_COOKIE['age']<>0) {
$where .= " and p_age = ".$_COOKIE['age'];
}
if($settings['version']==3){
if (isset($_COOKIE['area']) and $_COOKIE['area']<>0) {
$where .= " and cid = ".$_COOKIE['area'];
}
}
if (isset($_GET['k']) and $_GET['k'] != ""and $_GET['keywords'] != "请输入要搜索的关键词,如:宝马") {
setMyCookie("keywords",$_GET['k'],time() +COOKIETIME);
}elseif (isset($_GET['k']) and $_GET['k'] == "") {
setMyCookie("keywords",'',time() -COOKIETIME);
}
if (!empty($_COOKIE['keywords'])) {
$where .= " AND (`p_allname` like '%".$_COOKIE['keywords'] ."%' or `p_keyword` like '%".$_COOKIE['keywords'] ."%' or `p_no` like '%".$_COOKIE['keywords'] ."%')";
}
if (isset($_GET['order'])) {
setMyCookie("order",$_GET['order'],time() +COOKIETIME);
}else {
setMyCookie("order",1,time() +COOKIETIME);
}
$orderby = "";
if (!empty($_COOKIE['order'])) {
switch ($_COOKIE['order']) {
case 1:
$orderby = "p_addtime desc";
break;
case 2:
$orderby = "p_addtime asc";
break;
case 3:
$orderby = "p_price asc";
break;
case 4:
$orderby = "p_price desc";
break;
default:
$orderby = "p_addtime desc";
}
}
if (isset($_GET['showtype'])) {
setMyCookie("showtype",$_GET['showtype'],time() +COOKIETIME);
}else {
setMyCookie("showtype",'list',time() +COOKIETIME);
}
include(INC_DIR .'Page.class.php');
$Page = new Page($db ->tb_prefix .'buycars',$where,'*','24',$orderby);
$listnum = $Page ->total_num;
$list = $Page ->get_data();
foreach($list as $key =>$value) {
$list[$key]['p_addtime'] = date('Y-m-d',$value['p_addtime']);
$list[$key]['p_url'] = HTML_DIR ."qiugou/".date('Y/m/d',$value['p_addtime']) ."/".$value['p_id'] .".html";
$list[$key]['age'] = $arr_age_b[$value['p_age']];
if(!empty($value['uid'])){
$user = $db ->row_select_one('member','id='.$value['uid'],'isdealer,ischeck');
if($user['isshop']==2 and $user['ischeck']==1){
$list[$key]['isshop'] = 1;
}
else{
$list[$key]['isshop'] = 0;
}
}
if(!empty($value['cid'])){
$area = $db ->row_select_one('area','id = '.$value['cid']);
$list[$key]['area'] = $area['name'];
}
}
$button_basic = $Page ->button_basic();
$button_select = $Page ->button_select();
$tpl ->assign('allnum',$listnum);
$tpl ->assign('button_basic',$button_basic);
$tpl ->assign('button_select',$button_select);
$tpl ->assign('buycarslist',$list);
$tpl ->display('default/'.$settings['templates'] .'/buylist.html');

?>