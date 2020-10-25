<?php

if (!defined('APP_IN')) exit('Access Denied');
include ('page.php');
include(INC_DIR .'html.func.php');
$tpl ->assign('menustate',2);
if (!empty($_GET['ajax']) &&isset($_GET['p_pic'])) {
$str = $_GET['p_pic'];
$arr_picid = explode("/",$str);
$arr_length = count($arr_picid);
$picstr = explode(".",$arr_picid[$arr_length-1]);
$picpath = substr($str,1);
if (file_exists($picpath)) unlink($picpath);
echo $picstr[0];
exit;
}
if($settings['version']==3){
if (!empty($_COOKIE['city'])) {
$citydata = $db ->row_select_one('area',"id='".$_COOKIE['city'] ."'",'parentid');
$select_province = select_make($citydata['parentid'],$commoncache['provincelist'],"请选择省份");
$array_city = arr_city($citydata['parentid']);
$select_city = select_make($_COOKIE['city'],$array_city,"请选择城市");
}else {
$array_city = array();
$select_province = select_make('',$commoncache['provincelist'],"请选择省份");
$select_city = select_make('',$array_city,"请选择城市");
}
$tpl ->assign('selectprovince',$select_province);
$tpl ->assign('selectcity',$select_city);
}
if (submitcheck('action')) {
if(empty($_SESSION['USER_ID'])){
$arr_not_empty = array('p_brand'=>'请选择品牌','p_subbrand'=>'请选择子品牌','p_price'=>'请填写价格','p_username'=>'请输入您的姓名','p_tel'=>'请输入您的联系电话');
}
else{
$arr_not_empty = array('p_brand'=>'请选择品牌','p_subbrand'=>'请选择子品牌','p_price'=>'请填写价格');
}
can_not_be_empty($arr_not_empty,$_POST);
if (trim($_POST['authcode']) != $_SESSION['authcode']) showmsg('验证码不正确',-1);
$post = post('p_brand','p_subbrand','p_subsubbrand','p_allname','p_sort','p_price','p_color','p_model','p_country','p_kilometre','p_transmission','p_year','p_month','p_gas','p_details','p_addtime','p_emission');
if($settings['version']==3){
$post['aid'] = intval($_POST['aid']);
$post['cid'] = intval($_POST['cid']);
}
else{
$post['aid'] = 0;
$post['cid'] = 0;
}
if(empty($_SESSION['USER_ID'])){
$post['p_username'] = trim($_POST['p_username']);
$post['p_tel'] = trim($_POST['p_tel']);
}
$post['p_brand'] = intval($post['p_brand']);
$post['p_subbrand'] = intval($post['p_subbrand']);
$post['p_subsubbrand'] = intval($post['p_subsubbrand']);
$post['p_allname'] = "";
if(!empty($post['p_subbrand'])){
$bname = $commoncache['brandlist'][$post['p_brand']];
$subbname = arr_brandname($post['p_subbrand']);
$compareword = strstr($subbname,$bname);
if(!empty($compareword)){
$post['p_allname'] .= arr_brandname($post['p_subbrand']);
}
else{
$post['p_allname'] .= $bname ." ".arr_brandname($post['p_subbrand']);
}
}
if(!empty($post['p_subsubbrand'])){
$post['p_allname'] .= " ".arr_brandname($post['p_subsubbrand']);
}
$post['p_price'] = trim($post['p_price']);
$post['p_color'] = trim($post['p_color']);
$post['p_model'] = trim($post['p_model']);
$post['p_country'] = trim($post['p_country']);
$post['p_kilometre'] = trim($post['p_kilometre']);
$post['p_transmission'] = trim($post['p_transmission']);
$post['p_year'] = intval($post['p_year']);
$post['p_month'] = intval($post['p_month']);
$post['p_gas'] = trim($post['p_gas']);
$post['p_details'] = htmlspecialchars(trim($post['p_details']));
if(empty($post['p_kilometre'])){
$post['p_kilometre'] = 0;
}
if (isset($_POST['p_pics'])) {
$post['p_pics'] = implode("|",$_POST['p_pics']);
if (isset($_POST['p_mainpic'])) {
$post['p_mainpic'] = $_POST['p_mainpic'];
}else {
$post['p_mainpic'] = $_POST['p_pics'][0];
}
}else {
$post['p_pics'] = "";
}
$post['p_addtime'] = TIMESTAMP;
$post['listtime'] = TIMESTAMP;
if(!empty($_SESSION['USER_ID'])){
$post['uid'] = $_SESSION['USER_ID'];
$userinfo = $db ->row_select_one('member',"id={$_SESSION['USER_ID']}");
if($userinfo['isdealer']==2 and $userinfo['ischeck']==1){
$post['isshow'] = 1;
}
else{
$post['isshow'] = 0;
}
}
else{
$post['uid'] = 0;
$post['isshow'] = 0;
}
$db ->row_insert('cars',$post);
$insertid = $db ->insert_id();
html_cars($insertid);
showmsg('您的信息已提交成功！',-1);
}
$tpl ->display('default/'.$settings['templates'] .'/sell.html');

?>