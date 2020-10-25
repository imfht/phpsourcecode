<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '幻灯片管理';
$ac_arr = array('list'=>'幻灯片列表','add'=>'添加幻灯片','edit'=>'编辑幻灯片','del'=>'删除幻灯片','bulkdel'=>'批量删除','bulksort'=>'更新排序');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
$typeid = isset($_REQUEST['typeid']) ?intval($_REQUEST['typeid']) : 1;
$tpl->assign( 'typeid',$typeid );
if ($ac == 'list')
{
include(INC_DIR.'Page.class.php');
$Page = new Page($db->tb_prefix.'filmstrip','typeid='.$typeid);
$list = $Page->get_data();
$button_basic = $Page->button_basic();
$button_select = $Page->button_select();
$tpl->assign( 'filmlist',$list );
$tpl->assign( 'button_basic',$button_basic );
$tpl->assign( 'button_select',$button_select );
$tpl->display( 'admin/filmstrip_list.html');
exit;
}
elseif ($ac == 'del')
{
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('filmstrip',"id=$id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('filmstrip',"id in($str_id)");
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('filmstrip',array('orderid'=>$_POST['orderid'][$v]),"id=".intval($v));
}
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$post = post('pic','url','typeid');
if(!empty($_FILES['upload']['name'])){
$newname = time();
$post['pic'] = upload_pic($newname,1,'common/');
}
if ($ac == 'add')
{
$post['orderid'] = 0;
$rs = $db->row_insert('filmstrip',$post);
}
else
{
$rs = $db->row_update('filmstrip',$post,"id=".intval($_POST['id']));
}
}
else
{
if (empty($_GET['id'])) $data = array('id'=>'','pic'=>'','orderid'=>'','typeid'=>'');
else $data = $db->row_select_one('filmstrip',"id=".intval($_GET['id']));
$tpl->assign( 'filmstrip',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_filmstrip.html');
exit;
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list&typeid=".$typeid);

?>