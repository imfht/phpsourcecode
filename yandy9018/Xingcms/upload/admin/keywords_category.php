<?php

if (!defined('APP_IN')) exit('Access Denied');
$mod_name = '关键词分类管理';
$ac_arr = array('list'=>'关键词分类列表','add'=>'添加关键词分类','edit'=>'编辑关键词分类','del'=>'删除关键词分类','bulkdel'=>'批量删除','bulksort'=>'更新排序');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$mod_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
if ($ac == 'list')
{
include(INC_DIR.'Page.class.php');
$Page = new Page($db->tb_prefix.'keywords_category','1=1','*','20','listorder');
$list = $Page->get_data();
$button_basic = $Page->button_basic();
$button_select = $Page->button_select();
$tpl->assign( 'keywordscategorylist',$list );
$tpl->assign( 'button_basic',$button_basic );
$tpl->assign( 'button_select',$button_select );
$tpl->display( 'admin/keywords_category_list.html');
exit;
}
elseif ($ac == 'del')
{
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('keywords_category',"catid=$id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('keywords_category',"catid in($str_id)");
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('keywords_category',array('listorder'=>$_POST['orderid'][$v]),"catid=".intval($v));
}
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$arr_not_empty = array('catname'=>'名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post['catname'] = $_POST['catname'];
if ($ac == 'add')
{
$post['listorder'] = 0;
$rs = $db->row_insert('keywords_category',$post);
}
else
{
$rs = $db->row_update('keywords_category',$post,"catid=".intval($_POST['id']));
}
}
else 
{
if (empty($_GET['id'])) $data = array('catname'=>'');
else $data = $db->row_select_one('keywords_category',"catid=".intval($_GET['id']));
$tpl->assign( 'keywordscategory',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_keywords_category.html');
exit;
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list");

?>