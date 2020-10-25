<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '自定义参数管理';
$ac_arr = array('list'=>'参数列表','add'=>'添加参数','edit'=>'编辑参数','del'=>'删除参数','bulkdel'=>'批量删除','bulksort'=>'更新排序','bulksort'=>'更新排序','show'=>'显示参数');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
if ($ac == 'list')
{
$list = $db->row_select('selfdefine','1=1','*',0,'orderid asc');
$tpl->assign( 'paralist',$list );
$tpl->display( 'admin/selfdefine_list.html');
exit;
}
elseif ($ac == 'del')
{
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('selfdefine',"id=$id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('selfdefine'," id in($str_id)");
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('selfdefine',array('orderid'=>$_POST['orderid'][$v]),"id=".intval($v));
}
}
elseif ($ac == 'show') {
$ptate = intval($_GET['p_state']);
$rs = $db ->row_update('selfdefine',array('isshow'=>$ptate),"id=".intval($_GET['id']));
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$arr_not_empty = array('c_name'=>'参数名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('c_name','type_value','type_name','isshow');
$post['type_value']=trim($post['type_value']);
if ($ac == 'add')
{
$post['isshow'] = 1;
$rs = $db->row_insert('selfdefine',$post);
}
else
{
$rs = $db->row_update('selfdefine',$post,"id=".intval($_POST['id']));
}
}
else 
{
if (empty($_GET['id'])) $data = array('id'=>'','c_name'=>'','orderid'=>'');
else $data = $db->row_select_one('selfdefine',"id=".intval($_GET['id']));
$tpl->assign( 'paralist',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_selfdefine.html');
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