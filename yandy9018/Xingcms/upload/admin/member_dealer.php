<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '销售代表管理';
$ac_arr = array('list'=>'销售代表列表','add'=>'添加销售代表','edit'=>'编辑销售代表','del'=>'删除销售代表','bulkdel'=>'批量删除','html'=>'生成静态','bulkhtml'=>'批量生成静态');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
$page_g = isset($_REQUEST['page']) ?intval($_REQUEST['page']) : 1;
$tpl->assign( 'page_g',$page_g );
if ($ac == 'list')
{
$where = '1=1';
if (!empty($_GET['keywords']))
{
$keywords = $_GET['keywords'];
$where .= " AND name LIKE '%{$keywords}%' or tel LIKE '%{$keywords}%'";
}
include(INC_DIR.'Page.class.php');
$Page = new Page($db->tb_prefix.'member_dealer',$where,'*','20','id desc');
$list = $Page->get_data();
$page = $Page ->page;
foreach($list as $key =>$value){
$user = $db ->row_select_one('member','id ='.$value['uid'],'id,username');
$list[$key]['username'] = $user['username'];
}
$button_basic = $Page->button_basic();
$button_select = $Page->button_select();
$tpl->assign( 'dealerlist',$list );
$tpl->assign( 'button_basic',$button_basic );
$tpl->assign( 'button_select',$button_select );
$tpl->assign( 'page',$page );
$tpl->display( 'admin/member_dealer_list.html');
exit;
}
elseif ($ac == 'del')
{
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('member_dealer',"id=$id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('member_dealer',"id in($str_id)");
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$arr_not_empty = array('name'=>'姓名不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('name','tel','pic');
if ($ac == 'add')
{
$rs = $db->row_insert('member_dealer',$post);
$insertid = $db ->insert_id();
}
else
{
$rs = $db->row_update('member_dealer',$post,"id=".intval($_POST['id']));
}
}
else 
{
if (empty($_GET['id'])) $data = array('id'=>'','name'=>'','pic'=>'','tel'=>'');
else $data = $db->row_select_one('member_dealer',"id=".intval($_GET['id']));
print
$tpl->assign( 'dealer',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_member_dealer.html');
exit;
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list&page=".$page_g);

?>