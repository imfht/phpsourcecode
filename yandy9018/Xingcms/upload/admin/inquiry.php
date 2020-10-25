<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '询价信息列表';
$ac_arr = array('list'=>'信息列表','del'=>'删除信息','bulkdel'=>'删除信息','show'=>'显示信息');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
$page_g = isset($_REQUEST['page']) ?intval($_REQUEST['page']) : 1;
if ($ac == 'list') {
$where = "1=1";
if(!empty($_GET['keywords'])) {
$keywords = $_GET['keywords'];
$where .= " and (name like '%{$keywords}%' or mobilephone like '%{$keywords}%')";
}
include(INC_DIR .'Page.class.php');
$Page = new Page($db ->tb_prefix .'inquiry',$where,'*','50','addtime desc');
$list = $Page ->get_data();
$page = $Page ->page;
foreach($list as $key =>$value) {
$list[$key]['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
$car = $db ->row_select_one('cars',"p_id=".$value['pid'],'p_id,p_allname,p_addtime');
$car['p_url'] = HTML_DIR ."buycars/".date('Y/m/d',$car['p_addtime']) ."/".$car['p_id'] .".html";
$list[$key]['p_allname'] = $car['p_allname'];
$list[$key]['p_url'] = $car['p_url'];
if (!empty($value['uid'])) {
$member = $db ->row_select_one('member','id = '.$value['uid']);
$list[$key]['username'] = $member['username'];
}
}
$button_basic = $Page ->button_basic();
$button_select = $Page ->button_select();
$tpl ->assign('button_basic',$button_basic);
$tpl ->assign('button_select',$button_select);
$tpl ->assign('inquirylist',$list);
$tpl ->assign('page',$page);
$tpl ->display('admin/inquiry_list.html');
exit;
}
elseif ($ac == 'del') {
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db ->row_delete('inquiry',"id=$id");
}
elseif ($ac == 'bulkdel') {
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db ->row_delete('inquiry',"id in($str_id)");
}
else {
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac] .($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list&page=".$page_g);
?>