<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '商家类型管理';
$ac_arr = array('list'=>'商家类型列表','add'=>'添加商家类型','edit'=>'编辑商家类型','del'=>'删除商家类型','bulkdel'=>'批量删除','bulksort'=>'更新排序');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
if ($ac == 'list')
{
$list = $db->row_select('dealer_category','1=1','*',0,'orderid asc');
$tpl->assign( 'categorylist',$list );
$tpl->display( 'admin/dealer_category_list.html');
exit;
}
elseif ($ac == 'del')
{
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('dealer_category',"id=$id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('dealer_category',"id in($str_id)");
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('dealer_category',array('orderid'=>$_POST['orderid'][$v]),"id=".intval($v));
}
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$arr_not_empty = array('catname'=>'商家类型名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('catname','orderid');
$post['catname'] = trim($post['catname']);
$post['orderid'] = intval($post['orderid']);
if ($ac == 'add')
{
$rs = $db->row_insert('dealer_category',$post);
}
else
{
$rs = $db->row_update('dealer_category',$post,"id=".intval($_POST['id']));
}
}
else 
{
if (empty($_GET['id'])) $data = array('id'=>'','catname'=>'','orderid'=>'');
else $data = $db->row_select_one('dealer_category',"id=".intval($_GET['id']));
$tpl->assign( 'category',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_dealer_category.html');
exit;
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list");
?>