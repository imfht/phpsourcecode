<?php

if(!defined('APP_IN')) exit('Access Denied');
if (!empty($_COOKIE['city'])) {
$citydata = $db ->row_select_one('area',"id='".$_COOKIE['city'] ."'",'parentid');
$select_province = select_make($citydata['parentid'],$commoncache['provincelist'],"请选择省份");
$select_city = select_make($_COOKIE['city'],$commoncache['citylist'],"请选择城市");
}else {
$select_province = select_make('',$commoncache['provincelist'],"请选择省份");
$select_city = select_make('',$commoncache['citylist'],"请选择城市");
}
$tpl ->assign('selectprovince',$select_province);
$tpl ->assign('selectcity',$select_city);
$array_dealer_category = arr_dealer_category();
$select_dealer_category = select_make('',$array_dealer_category,"请选择公司类型");
$tpl ->assign('select_dealer_category',$select_dealer_category);
if (!empty($_POST['param']) and $_POST['name']=="username")
{
$data = $db->row_count('member',"username='".$_POST['param']."'");
if($data==0){
echo '{"info":"用户名验证成功！","status":"y"}';
}
else{
echo '{"info":"用户名已存在！","status":"n"}';
}
exit;
}
if (!empty($_POST['param']) and $_POST['name']=="email")
{
$data = $db->row_count('member',"email='".$_POST['param']."'");
if($data==0){
echo '{"info":"邮箱验证成功！","status":"y"}';
}
else{
echo '{"info":"邮箱地址已存在！","status":"n"}';
}
exit;
}
if (!empty($_POST['param']) and $_POST['name']=="mobilephone")
{
$data = $db->row_count('member',"mobilephone='".$_POST['param']."'");
if($data==0){
echo '{"info":"手机号验证成功！","status":"y"}';
}
else{
echo '{"info":"手机号已存在！","status":"n"}';
}
exit;
}
if (!empty($_POST['param']) and $_POST['name']=="authcode")
{
if($_SESSION['authcode'] == $_POST['param']){
echo '{"info":"验证码正确！","status":"y"}';
}
else{
echo '{"info":"验证码不正确！","status":"n"}';
}
exit;
}
if (is_user_login()) {
redirect('','index.php?mod=user&ac=index');
}
if (submitcheck('username'))
{
$arr_not_empty = array('username'=>'用户名不能为空','password'=>'密码不能为空','repassword'=>'请再次输入密码','nicname'=>'请填写联系人','mobilephone'=>'请填写手机号','email'=>'电子邮箱不能为空','authcode'=>'验证码不能为空');
if ($_POST['authcode'] != $_SESSION['authcode']) showmsg('验证码不正确',-1);
$_POST['username'] = htmlspecialchars(trim($_POST['username']));
$_POST['password'] = trim($_POST['password']);
if ($db->row_count('member',"username='{$_POST['username']}'")) showmsg('用户已存在，请换一个用户名注册',-1);
if (!is_email($_POST['email'])) showmsg('错误的邮箱格式',-1);
$post = post('username','email','mobilephone','nicname','isdealer','company');
if($settings['version']==3){
$post['aid'] = intval($_POST['aid']);
$post['cid'] = intval($_POST['cid']);
}
$post['mobilephone'] = trim($post['mobilephone']);
$post['nicname'] = trim($post['nicname']);
$post['password'] = md5($_POST['password']);
$post['regtime'] = TIMESTAMP;
$post['company'] = trim($post['company']);
$post['isdealer'] = intval($post['isdealer']);
$post['ischeck'] = 0;
$rs = $db->row_insert('member',$post);
$insertid = $db ->insert_id();
if (!$rs) {
showmsg('注册失败，请稍后重新尝试',-1);
}
else{
$_SESSION['USER_ID'] = $insertid;
$_SESSION['USER_NAME'] = $_POST['username'];
$rs_user = $db->row_select_one('member',"username='".trim($_POST['username'])."'");
$db->row_update('member',array('last_login_time'=>TIMESTAMP,'last_login_ip'=>get_client_ip(),'login_count'=>$rs_user['login_count']+1),"id={$rs_user['id']}");
showmsg('登陆成功','index.php?mod=user&ac=index');
}
showmsg('注册成功','index.php?mod=login');
}
$tpl ->display('default/'.$settings['templates'].'/register.html');

?>