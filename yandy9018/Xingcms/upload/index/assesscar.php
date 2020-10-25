<?php

if (!defined('APP_IN')) exit('Access Denied');
$data = $db->row_select('settings',"k='version'");
$version=$data[0]['v'];
$tpl ->assign('version',$version);
$tpl ->assign('menustate',13);
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
if (submitcheck('action'))
{
$arr_not_empty = array('p_brand'=>'请选择品牌','p_subbrand'=>'请选择子品牌','p_contact_name'=>'请输入您的姓名','p_contact_tel'=>'请输入您的联系电话');
can_not_be_empty($arr_not_empty,$_POST);
if (trim($_POST['authcode']) != $_SESSION['authcode']) showmsg('验证码不正确',-1);
$post = post('p_brand','p_subbrand','p_subsubbrand','p_allname','p_model','p_price','p_color','p_country','p_kilometre','p_transmission','p_year','p_month','p_gas','p_details','p_contact_name','p_contact_tel','aid','cid');
$post['aid'] = intval($post['aid']);
$post['cid'] = intval($post['cid']);
$post['p_brand'] =intval($post['p_brand']);
$post['p_subbrand'] =intval($post['p_subbrand']);
$post['p_subsubbrand'] = intval($_POST['p_subsubbrand']);
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
$post['p_color'] = trim($post['p_color']);
$post['p_model'] = trim($post['p_model']);
$post['p_country'] = trim($post['p_country']);
$post['p_kilometre'] =trim($post['p_kilometre']);
$post['p_transmission'] = trim($post['p_transmission']);
$post['p_year'] = intval($post['p_year']);
$post['p_month'] = intval($post['p_month']);
$post['p_gas'] = intval($post['p_gas']);
$post['p_details'] = htmlspecialchars(trim($post['p_details']));
$post['p_contact_name'] = $post['p_contact_name'];
$post['p_contact_tel']  = $post['p_contact_tel'];
$post['p_addtime'] = TIMESTAMP;
$db->row_insert('assesscars',$post);
showmsg('您的信息已提交成功！',-1);
}
$tpl ->display('default/'.$settings['templates'] .'/assess.html');

?>