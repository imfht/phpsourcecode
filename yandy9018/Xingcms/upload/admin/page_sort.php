<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '分类管理';
$ac_arr = array('list'=>'分类列表','add'=>'添加分类','edit'=>'编辑分类','del'=>'删除分类','bulkdel'=>'批量删除','bulksort'=>'更新排序');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
if ($ac == 'list')
{
include(INC_DIR.'Page.class.php');
$Page = new Page($db->tb_prefix.'page_sorts','1=1','*','20','orderid');
$list = $Page->get_data();
$button_basic = $Page->button_basic();
$button_select = $Page->button_select();
$tpl->assign( 'sortlist',$list );
$tpl->display( 'admin/pagesort_list.html');
exit;
}
elseif ($ac == 'del')
{
$s_id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('page_sorts',"s_id=$s_id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('page_sorts',"s_id in($str_id)");
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('page_sorts',array('orderid'=>$_POST['orderid'][$v]),"s_id=".intval($v));
}
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$arr_not_empty = array('s_name'=>'分类名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('s_name','s_dir','orderid','orderid');
if(!empty($post['s_dir']) and !is_dir($post['s_dir'])) createFolder($post['s_dir']);
$post['orderid'] = !empty($post['orderid']) ?intval($post['orderid']) : 0;
if ($ac == 'add')
{
$rs = $db->row_insert('page_sorts',$post);
}
else
{
$rs = $db->row_update('page_sorts',$post,"s_id=".intval($_POST['s_id']));
}
}
else 
{
if (empty($_GET['id']))
{
$data = array('s_id'=>'','s_name'=>'','s_dir'=>'','orderid'=>'');
}
else
{
$data = $db->row_select_one('page_sorts',"s_id=".intval($_GET['id']));
}
$tpl->assign( 'sort',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_pagesort.html');
exit;
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list");

?>