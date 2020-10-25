<?php

if (!defined('APP_IN')) exit('Access Denied');
$mod_name = '栏目管理';
$ac_arr = array('list'=>'栏目列表','add'=>'添加栏目','edit'=>'编辑栏目','del'=>'删除栏目','bulkdel'=>'批量删除','bulksort'=>'更新排序');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$mod_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
if ($ac == 'list')
{
include(INC_DIR.'Page.class.php');
$Page = new Page($db->tb_prefix.'channel','1=1','*','20','c_orderid');
$list = $Page->get_data();
$button_basic = $Page->button_basic();
$button_select = $Page->button_select();
$tpl->assign( 'channellist',$list );
$tpl->assign( 'button_basic',$button_basic );
$tpl->assign( 'button_select',$button_select );
$tpl->display( 'admin/channel_list.html');
exit;
}
elseif ($ac == 'del')
{
$c_id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('channel',"c_id=$c_id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('channel',"c_id in($str_id)");
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('channel',array('c_orderid'=>$_POST['c_orderid'][$v]),"c_id=".intval($v));
}
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$arr_not_empty = array('c_name'=>'栏目名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('c_name','c_url','c_target');
if ($ac == 'add')
{
$post['c_orderid'] = 0;
$rs = $db->row_insert('channel',$post);
}
else
{
$rs = $db->row_update('channel',$post,"c_id=".intval($_POST['id']));
}
}
else 
{
if (empty($_GET['id'])) $data = array('c_name'=>'','c_url'=>'','c_target'=>'');
else $data = $db->row_select_one('channel',"c_id=".intval($_GET['id']));
$tpl->assign( 'channel',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_channel.html');
exit;
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list");
?>