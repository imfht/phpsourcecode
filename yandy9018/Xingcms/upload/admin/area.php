<?php

if (!defined('APP_IN')) exit('Access Denied');
$mod_name = '省市管理';
$ac_arr = array('list'=>'地区列表','add'=>'添加地区','edit'=>'编辑地区','recom'=>'推荐地区','del'=>'删除地区','bulkdel'=>'批量删除','bulksort'=>'更新排序');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$mod_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
if ($ac == 'list')
{
include(INC_DIR.'Page.class.php');
$Page = new Page($db->tb_prefix.'area','parentid=-1','*','20','orderid');
$list = $Page->get_data();
foreach($list as $key =>$value ){
$citylist = $db ->row_select('area',"parentid=".$value['id'],'id,name,isrecom,orderid',0,'orderid asc');
$list[$key]['city'] = $citylist;
}
$button_basic = $Page->button_basic();
$button_select = $Page->button_select();
$tpl->assign( 'arealist',$list );
$tpl->assign( 'button_basic',$button_basic );
$tpl->assign( 'button_select',$button_select );
$tpl->display( 'admin/area_list.html');
exit;
}
elseif ($ac == 'recom') {
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$isrecom  = intval($_GET['isrecom']);
$rs = $db ->row_update('area',array('isrecom'=>$isrecom ),"id=".$id);
}
elseif ($ac == 'del')
{
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('area',"id=$id or parentid=$id ");
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('area',array('orderid'=>$_POST['orderid'][$v]),"id=".intval($v));
}
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('area',"id in($str_id) or parentid in($str_id)");
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$arr_not_empty = array('name'=>'名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('id','name','orderid','parentid');
$post['id'] = intval($post['id']);
if ($ac == 'add')
{
$post['orderid'] = 0;
$rs = $db->row_insert('area',$post);
}
else
{
$rs = $db->row_update('area',$post,"id=".intval($_POST['id']));
}
}
else
{
if (empty($_GET['id'])) {
$data = array('id'=>'','name'=>'','orderid'=>'');
if(empty($_GET['parentid'])){
$data['parentid'] = -1;
}
else{
$data['parentid'] = $_GET['parentid'];
}
}
else{
$data = $db->row_select_one('area',"id=".intval($_GET['id']));
}
$tpl->assign( 'area',$data );
$tpl->display( 'admin/add_area.html');
exit;
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list");
?>