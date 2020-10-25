<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '卖车信息列表';
$ac_arr = array('list'=>'车源列表','del'=>'删除信息','bulkdel'=>'删除信息','show'=>'显示信息');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
$page_g = isset($_REQUEST['page']) ?intval($_REQUEST['page']) : 1;
if ($ac == 'list') {
$where = "p_id > 0 ";
if(!empty($_GET['keywords'])) {
$keywords = $_GET['keywords'];
$where .= " and (p_contact_name like '%{$keywords}%' or p_allname like '%{$keywords}%')";
}
include(INC_DIR .'Page.class.php');
$Page = new Page($db ->tb_prefix .'buycars',$where,'*','50','p_id desc');
$list = $Page ->get_data();
$page = $Page ->page;
foreach($list as $key =>$value) {
$list[$key]['p_addtime'] = date('Y-m-d H:i:s',$value['p_addtime']);
if(!empty($value['p_contact'])){
$p_contact = explode('|',$value['p_contact']);
}
if(!empty($value['p_model'])) $list[$key]['p_modelname'] = $commoncache['modellist'][$value['p_model']];
if (!empty($value['aid'])) {
$area = $db ->row_select_one('area','id = '.$value['aid']);
$list[$key]['province'] = $area['name'];
}
if (!empty($value['cid'])) {
$area = $db ->row_select_one('area','id = '.$value['cid']);
$list[$key]['city'] = $area['name'];
}
if (!empty($value['uid'])) {
$member = $db ->row_select_one('member','id = '.$value['uid']);
$list[$key]['username'] = $member['username'];
}
else{
$list[$key]['username'] = "游客";
}
}
$button_basic = $Page ->button_basic();
$button_select = $Page ->button_select();
$tpl ->assign('button_basic',$button_basic);
$tpl ->assign('button_select',$button_select);
$tpl ->assign('carslist',$list);
$tpl ->assign('page',$page);
$tpl ->display('admin/buycars_list.html');
exit;
}
elseif ($ac == 'del') {
$p_id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$car = $db ->row_select_one('buycars',"p_id=$p_id");
$rs = $db ->row_delete('buycars',"p_id=$p_id");
}
elseif ($ac == 'bulkdel') {
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db ->row_delete('buycars',"p_id in($str_id)");
}
elseif ($ac == 'show') {
$ptate = intval($_GET['state']);
$rs = $db ->row_update('buycars',array('isshow'=>$ptate),"p_id=".intval($_GET['id']));
html_qiugoucars(intval($_GET['id']));
}
else {
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac] .($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list&page=".$page_g);

?>