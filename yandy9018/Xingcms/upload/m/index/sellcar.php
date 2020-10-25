<?php

if (!defined('APP_IN')) exit('Access Denied');
if (submitcheck('action')) {
$arr_not_empty = array('brand'=>'请选择品牌','subbrand'=>'请选择子品牌','p_tel'=>'请填写手机号');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('brand','subbrand','p_tel');
$post['p_allname'] = $commoncache['brandlist'][$post['brand']].$commoncache['subbrandlist'][$post['subbrand']];
$post['p_keyword'] = $commoncache['brandkeyword'][$post['brand']].$commoncache['subbrandkeyword'][$post['subbrand']];
$post['p_brand'] = intval($post['brand']);
$post['p_subbrand'] = intval($post['subbrand']);
$post['p_tel'] = trim($post['p_tel']);
$post['p_addtime'] = TIMESTAMP;
$post['listtime'] = TIMESTAMP;
$post['uid'] = 0;
$post['isshow'] = 0;
unset($post['brand']);
unset($post['subbrand']);
$db ->row_insert('cars',$post);
mshowmsg('恭喜您，卖车登记成功！',-1);
}
$tpl ->display('m/sellcar.html');

?>