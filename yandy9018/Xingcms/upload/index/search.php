<?php

if (!defined('APP_IN')) exit('Access Denied');
$tpl ->assign('menustate',7);
$brand_search = arr_brand_with_search();
$select_brand_search = select_make('',$brand_search,'请选择品牌');
$tpl->assign( 'selectbrandsearch',$select_brand_search );
$comnewslist = get_comnews(53 ,10);
$tpl->assign( 'comnewslist',$comnewslist );
if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
$_COOKIE['city'] = 0;
}
if($settings['issell']==1){
$where = "isshow=1";
}
else{
$where = "issell=0 and isshow=1";
}
if($_COOKIE['city']!=0){
$where .= " and cid=".$_COOKIE['city'];
}
if (isset($_GET['clear']) and $_GET['clear'] == 1) {
setMyCookie("keywords",'',time() -COOKIETIME);
setMyCookie("brand",'',time() -COOKIETIME);
setMyCookie("subbrand",'',time() -COOKIETIME);
setMyCookie("price",'',time() -COOKIETIME);
setMyCookie("age",'',time() -COOKIETIME);
setMyCookie("model",'',time() -COOKIETIME);
setMyCookie("gas",'',time() -COOKIETIME);
setMyCookie("show",'',time() -COOKIETIME);
setMyCookie("order",'',time() -COOKIETIME);
setMyCookie("carfrom",'',time() -COOKIETIME);
}
$arr_p = array('1'=>'3万以下','2'=>'3-5万','3'=>'5-8万','4'=>'8-12万','5'=>'12-18万','6'=>'18-24万','7'=>'24-35万','8'=>'35-50万','9'=>'50-100万','10'=>'100万以上');
$tpl ->assign('arr_price',$arr_p);
$arr_a = array('1'=>'1年以内','2'=>'2年以内','3'=>'3年以内','4'=>'4年以内','5'=>'5年以内','6'=>'6年以内','7'=>'6年以上');
$tpl ->assign('arr_age',$arr_a);
$arr_g = array('1'=>'1.0L','2'=>'2.0L','3'=>'3.0L','4'=>'4.0L','5'=>'5.0L及以上');
$tpl ->assign('arr_gas',$arr_g);
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
elseif ($arr_c['0'] == "a") {
if (isset($arr_c[1])) {
setMyCookie("age",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['age']) and $_COOKIE['age'] == 0) {
setMyCookie("age",'',time() -COOKIETIME);
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
elseif ($arr_c['0'] == "g") {
if (isset($arr_c[1])) {
setMyCookie("gas",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['gas']) and $_COOKIE['gas'] == 0) {
setMyCookie("gas",'',time() -COOKIETIME);
}
}
elseif ($arr_c['0'] == "f") {
if (isset($arr_c[1])) {
setMyCookie("carfrom",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['carfrom']) and $_COOKIE['carfrom'] == 0) {
setMyCookie("carfrom",'',time() -COOKIETIME);
}
}
}
if (isset($_COOKIE['subbrand']) and isset($_GET['sb']) and  $_GET['sb'] == 0) {
setMyCookie("subbrand",'',time() -COOKIETIME);
}
if (isset($_COOKIE['brand']) and $_COOKIE['brand']<>0) {
$where .= " and p_brand = ".$_COOKIE['brand'];
}
if (isset($_COOKIE['subbrand']) and $_COOKIE['subbrand']<>0) {
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
$compareyear = date("Y") -$_COOKIE['age'];
switch ($_COOKIE['age']) {
case 7:
$where .= " and p_year < ".$compareyear;
break;
default:
$where .= " and p_year >= ".$compareyear;
}
}
if (isset($_COOKIE['model']) and $_COOKIE['model']<>0) {
$where .= " and p_model = ".$_COOKIE['model'];
}
if (isset($_COOKIE['gas']) and $_COOKIE['gas']<>0) {
switch ($_COOKIE['gas']) {
case 1:
$where .= " and p_gas = 1.0";
break;
case 2:
$where .= " and p_gas = 2.0";
break;
case 3:
$where .= " and p_gas = 3.0";
break;
case 4:
$where .= " and p_gas = 4.0";
break;
case 5:
$where .= " and p_gas >= 5.0";
break;
default:
$where .= "";
}
}
if (isset($_COOKIE['carfrom']) and $_COOKIE['carfrom']<>0) {
switch ($_COOKIE['carfrom']) {
case 1:
$where .= " and uid in( select id from ".$db_config['TB_PREFIX'] ."member where isdealer=2)";
break;
case 2:
$where .= " and (uid in( select id from ".$db_config['TB_PREFIX'] ."member where isdealer=1) or uid=0)";
break;
default:
$where .= "";
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
$orderby = "listtime desc";
break;
case 2:
$orderby = "listtime asc";
break;
case 3:
$orderby = "p_price asc";
break;
case 4:
$orderby = "p_price desc";
break;
case 5:
$orderby = "p_kilometre asc";
break;
case 6:
$orderby = "p_kilometre desc";
break;
case 7:
$orderby = "p_year desc,p_month desc";
break;
case 8:
$orderby = "p_year asc,p_month asc";
break;
default:
$orderby = "listtime desc";
}
}
if (isset($_GET['pagenum'])) {
setMyCookie("pagenum",$_GET['pagenum'],time() +COOKIETIME);
}else {
setMyCookie("pagenum",30,time() +COOKIETIME);
}
if (isset($_GET['showtype'])) {
setMyCookie("showtype",$_GET['showtype'],time() +COOKIETIME);
}else {
setMyCookie("showtype",'list',time() +COOKIETIME);
}
include(INC_DIR .'Page.class.php');
$Page = new Page($db ->tb_prefix .'cars',$where,'*',$_COOKIE['pagenum'],$orderby);
$listnum = $Page ->total_num;
$list = $Page ->get_data();
foreach($list as $key =>$value) {
if(!empty($value['p_mainpic'])){
$pic = explode(".",$value['p_mainpic']);
$list[$key]['p_mainpic'] = $pic[0]."_small".".".$pic[1];
}
$list[$key]['p_shortname'] = _substr($value['p_allname'],0,26);
$list[$key]['listtime'] = date('Y-m-d',$value['listtime']);
$list[$key]['p_details'] = _substr($value['p_details'],0,80);
$list[$key]['p_price'] = intval($value['p_price']) == 0 ?"面谈": "￥".$value['p_price'];
if (!empty($value['p_model'])) $list[$key]['p_modelname'] = $commoncache['modellist'][$value['p_model']];
$list[$key]['p_url'] = HTML_DIR ."buycars/".date('Y/m/d',$value['p_addtime']) ."/".$value['p_id'] .".html";
if($settings['version']==2 or $settings['version']==3){
if(!empty($value['uid'])){
$user = $db ->row_select_one('member','id='.$value['uid'],'isdealer');
$list[$key]['isdealer'] = $user['isdealer'];
}
}
}
$button_basic = $Page ->button_basic();
$button_select = $Page ->button_select();
$tpl ->assign('allnum',$listnum);
$tpl ->assign('button_basic',$button_basic);
$tpl ->assign('button_select',$button_select);
$tpl ->assign('select_model',$select_model);
$tpl ->assign('carslist',$list);
$tpl ->display('default/'.$settings['templates'] .'/carlist.html');
?>