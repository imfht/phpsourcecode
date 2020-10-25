<?php

if (!defined('APP_IN')) exit('Access Denied');
$mod_name = '关键词管理';
$ac_arr = array('list'=>'关键词列表','add'=>'添加关键词','edit'=>'编辑关键词','del'=>'删除关键词','bulkdel'=>'批量删除','bulksort'=>'更新排序');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$mod_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
$arr_keywordscategory = arr_keywordscategory();
$page_g = isset($_REQUEST['page']) ?intval($_REQUEST['page']) : 1;
$tpl->assign( 'page_g',$page_g );
if ($ac == 'list')
{
include(INC_DIR.'Page.class.php');
$Page = new Page($db->tb_prefix.'keywords','1=1','*','20','orderid,catid');
$list = $Page->get_data();
$page = $Page ->page;
foreach($list as $key =>$value){
if(!empty($value['catid'])) $list[$key]['catname'] = $arr_keywordscategory[$value['catid']];
}
$button_basic = $Page->button_basic();
$button_select = $Page->button_select();
$tpl->assign( 'keywordslist',$list );
$tpl->assign( 'button_basic',$button_basic );
$tpl->assign( 'button_select',$button_select );
$tpl->assign( 'page',$page );
$tpl->display( 'admin/keywords_list.html');
exit;
}
elseif ($ac == 'del')
{
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('keywords',"id=$id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('keywords',"id in($str_id)");
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('keywords',array('orderid'=>$_POST['orderid'][$v]),"id=".intval($v));
}
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$arr_not_empty = array('keywords'=>'关键词名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('catid','keywords');
$post['mark'] = getinitial($post['keywords']);
if ($ac == 'add')
{
$post['orderid'] = 0;
$rs = $db->row_insert('keywords',$post);
}
else
{
$rs = $db->row_update('keywords',$post,"id=".intval($_POST['id']));
}
}
else 
{
if (empty($_GET['id'])) $data = array('a_name'=>'');
else $data = $db->row_select_one('keywords',"id=".intval($_GET['id']));
$select_category = select_make($data['catid'],$arr_keywordscategory,'请选择分类');
$tpl->assign( 'selectcategory',$select_category );
$tpl->assign( 'keywords',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_keywords.html');
exit;
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list&page=".$page_g);

?>