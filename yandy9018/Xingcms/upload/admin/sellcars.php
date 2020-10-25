<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '卖车信息列表';
$ac_arr = array('list'=>'车源列表','del'=>'删除信息','bulkdel'=>'删除信息');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
$array_brand_with_index = arr_brand_with_index();
$array_brand = arr_brand();
$array_subbrand = arr_subbrand();
$array_brand_keyword = arr_brand_keyword();
$array_subbrand_keyword = arr_subbrand_keyword();
$array_model = arr_model();
$array_year = arr_year();
$array_color = arr_color();
$array_gas = arr_gas();
$array_transmission = arr_transmission();
if ($ac == 'list') {
$where = "p_id > 0 ";
if(!empty($_GET['keywords'])) {
$keywords = $_GET['keywords'];
$where .= " and (p_contact_name like '%{$keywords}%' or p_contact_tel like '%{$keywords}%' or p_brand like '%{$keywords}%' or p_subbrand like '%{$keywords}%')";
}
include(INC_DIR .'Page.class.php');
$Page = new Page($db ->tb_prefix .'sellcars',$where,'*','50','p_id desc');
$list = $Page ->get_data();
foreach($list as $key =>$value) {
$list[$key]['p_addtime'] = date('Y-m-d H:i:s',$value['p_addtime']);
$p_contact = explode('|',$value['p_contact']);
$list[$key]['p_contact_name'] = $p_contact[0];
$list[$key]['p_contact_tel']  = $p_contact[1];
if(!empty($value['p_model'])) $list[$key]['p_modelname'] = $array_model[$value['p_model']];
if(!empty($value['p_brand'])) $list[$key]['p_brandname'] = $array_brand[$value['p_brand']];
if(!empty($value['p_subbrand'])) $list[$key]['p_subbrandname'] = $array_subbrand[$value['p_subbrand']];
}
$button_basic = $Page ->button_basic();
$button_select = $Page ->button_select();
$tpl ->assign('button_basic',$button_basic);
$tpl ->assign('button_select',$button_select);
$tpl ->assign('carslist',$list);
$tpl ->display('admin/sellcars_list.html');
exit;
}
elseif ($ac == 'del') {
$p_id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$car = $db ->row_select_one('sellcars',"p_id=$p_id");
if(!empty($car['p_pics'])) {
$pic_list = explode('|',$car['p_pics']);
foreach($pic_list as $key =>$value) {
$pic = str_replace(WEB_PATH .'/','',$value);
$smallpic = str_replace('upload/upload','upload/small',$pic);
unlink($pic);
unlink($smallpic);
}
}
$rs = $db ->row_delete('sellcars',"p_id=$p_id");
}
elseif ($ac == 'bulkdel') {
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach($_POST['bulkid'] as $key =>$value) {
$car = $db ->row_select_one('sellcars',"p_id=".$value);
if(!empty($car['p_pics'])) {
$pic_list = explode('|',$car['p_pics']);
foreach($pic_list as $key =>$value) {
$pic = str_replace(WEB_PATH .'/','',$value);
$smallpic = str_replace('upload/upload','upload/small',$pic);
unlink($pic);
unlink($smallpic);
}
}
}
$str_id = return_str_id($_POST['bulkid']);
$rs = $db ->row_delete('sellcars',"p_id in($str_id)");
}
else {
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac] .($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list");
?>