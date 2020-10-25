<?php

if (!defined('APP_IN')) exit('Access Denied');
if (submitcheck('action')) {
$arr_not_empty = array('brand'=>'请选择品牌','subbrand'=>'请选择子品牌','year'=>'请选择上牌年份','month'=>'请选择上牌月份','kilometre'=>'请填写行驶里程','p_tel'=>'请填写手机号');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('brand','subbrand','p_tel','year','month','kilometre');
$post['p_allname'] = $commoncache['brandlist'][$post['brand']].$commoncache['subbrandlist'][$post['subbrand']];
$post['p_brand'] = intval($post['brand']);
$post['p_subbrand'] = intval($post['subbrand']);
$post['p_year'] = trim($post['year']);
$post['p_month'] = trim($post['month']);
$post['p_kilometre'] = trim($post['kilometre']);
$post['p_contact_tel'] = trim($post['p_tel']);
$post['p_addtime'] = TIMESTAMP;
unset($post['brand']);
unset($post['subbrand']);
unset($post['year']);
unset($post['month']);
unset($post['kilometre']);
unset($post['p_tel']);
$db ->row_insert('assesscars',$post);
mshowmsg('恭喜您，评估登记成功！',-1);
}
$tpl ->display('m/assesscar.html');
?>