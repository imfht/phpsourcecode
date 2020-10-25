<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '分类管理';
$ac_arr = array('list'=>'分类列表','add'=>'添加分类','edit'=>'编辑分类','del'=>'删除分类','bulkdel'=>'批量删除','bulksort'=>'更新排序');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
if ($ac == 'list')
{
include(INC_DIR.'page.class.php');
$friendlink = new page($db->tb_prefix.'friendlink_sorts','1=1','*','20','orderid');
$list = $friendlink->get_data();
$button_basic = $friendlink->button_basic();
$button_select = $friendlink->button_select();
$tpl->assign( 'sortlist',$list );
$tpl->display( 'admin/flinksort_list.html');
exit;
}
elseif ($ac == 'del')
{
$s_id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('friendlink_sorts',"s_id=$s_id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('friendlink_sorts',"s_id in($str_id)");
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('friendlink_sorts',array('orderid'=>$_POST['orderid'][$v]),"s_id=".intval($v));
}
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('ac'))
{
$arr_not_empty = array('s_name'=>'分类名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('s_name','orderid','orderid');
$post['orderid'] = !empty($post['orderid']) ?intval($post['orderid']) : 0;
if ($ac == 'add')
{
$rs = $db->row_insert('friendlink_sorts',$post);
}
else
{
$rs = $db->row_update('friendlink_sorts',$post,"s_id=".intval($_POST['s_id']));
}
}
else 
{
if (empty($_GET['id']))
{
$data = array('s_id'=>'','s_name'=>'','orderid'=>'');
}
else
{
$data = $db->row_select_one('friendlink_sorts',"s_id=".intval($_GET['id']));
}
$tpl->assign( 'sort',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_flinksort.html');
exit;
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list");

?>