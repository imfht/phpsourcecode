<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '车型管理';
$ac_arr = array('list'=>'车型列表','add'=>'添加车型','edit'=>'编辑车型','del'=>'删除车型','bulkdel'=>'批量删除','bulksort'=>'更新排序');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
if ($ac == 'list')
{
$list = $db->row_select('model','1=1','*',0,'orderid asc');
$tpl->assign( 'sortlist',$list );
$tpl->display( 'admin/model_list.html');
exit;
}
elseif ($ac == 'del')
{
$s_id = isset($_GET['s_id']) ?intval($_GET['s_id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('model',"s_id=$s_id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('model',"s_id in($str_id)");
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('model',array('orderid'=>$_POST['orderid'][$v]),"s_id=".intval($v));
}
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$arr_not_empty = array('s_name'=>'车型名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('s_name','s_pic');
if ($ac == 'add')
{
$rs = $db->row_insert('model',$post);
}
else
{
$rs = $db->row_update('model',$post,"s_id=".intval($_POST['s_id']));
}
}
else 
{
if (empty($_GET['s_id'])) $data = array('s_id'=>'','s_name'=>'','s_pic'=>'','orderid'=>'');
else $data = $db->row_select_one('model',"s_id=".intval($_GET['s_id']));
$tpl->assign( 'sort',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_model.html');
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