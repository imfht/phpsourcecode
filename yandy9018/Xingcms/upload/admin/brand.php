<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '品牌管理';
$ac_arr = array('list'=>'品牌列表','add'=>'添加品牌','edit'=>'编辑品牌','del'=>'删除品牌','bulkdel'=>'批量删除','bulksort'=>'更新排序','sign'=>'标记品牌');
$match_arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','All');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$sel_key = isset($_REQUEST['k']) &&in_array($_REQUEST['k'],$match_arr) ?$_REQUEST['k'] : 'All';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
$page_g = isset($_REQUEST['page']) ?intval($_REQUEST['page']) : 1;
$tpl->assign( 'page_g',$page_g );
if ($ac == 'list')
{
$where = 'b_parent = -1 and mark="A" ';
$pagesize = 0;
if ($sel_key != 'All'and $sel_key)
{
$pagesize = 100;
$where = " b_parent=-1 AND mark = '".$sel_key."'";
}
include(INC_DIR.'Page.class.php');
$Page = new Page($db->tb_prefix.'brand',$where,'*',$pagesize,'mark');
$list = $Page->get_data();
$page = $Page ->page;
foreach($list as $key =>$value){
$subbrandlist = $db->row_select('brand','b_parent = '.$value['b_id']);
foreach($subbrandlist as $subkey =>$subvalue){
$subsubbrandlist = $db->row_select('brand','b_parent = '.$subvalue['b_id']);
$subbrandlist[$subkey]['subbrands_list']=$subsubbrandlist;
}
$list[$key]['brands_list']=$subbrandlist;
}
$button_basic = $Page->button_basic();
$button_select = $Page->button_select();
$tpl->assign( 'sel_key',$sel_key );
$tpl->assign( 'match_arr',$match_arr );
$tpl->assign( 'button_basic',$button_basic );
$tpl->assign( 'button_select',$button_select );
$tpl->assign( 'brandlist',$list );
$tpl->assign( 'brandlist',$list );
$tpl ->assign('page',$page);
$tpl->display( 'admin/brand_list.html');
exit;
}
elseif ($ac == 'del')
{
$b_id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('brand',"b_id=$b_id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('brand',"b_id in($str_id)");
}
elseif ($ac == 'sign') {
$signid  = intval($_GET['signid']);
$rs = $db ->row_update('brand',array('sign'=>$signid ),"b_id=".intval($_GET['id']));
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('brand',array('orderid'=>$_POST['orderid'][$v]),"b_id=".intval($v));
}
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$arr_not_empty = array('b_name'=>'品牌名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
foreach (array('b_name','b_keyword') as $v)
{
$_POST[$v] = htmlspecialchars($_POST[$v]);
}
$post = post('b_name','b_keyword','b_parent','pic','mark','b_type','ishome');
$post['mark'] = getinitial($_POST['b_name']);
if(empty($post['b_type'])) $post['b_type']=0;
if ($ac == 'add')
{
$rs = $db->row_insert('brand',$post);
}
else
{
$rs = $db->row_update('brand',$post,"b_id=".intval($_POST['b_id']));
}
}
else
{
if (empty($_GET['id'])){
$data = array('b_id'=>'','b_name'=>'','b_keyword'=>'','b_parent'=>'-1','b_parentname'=>'根品牌','orderid'=>'','b_type'=>'','pic'=>'');
}
else{
$data = $db->row_select_one('brand',"b_id=".intval($_GET['id']));
if($ac == "add"){
$data = array('b_id'=>'','b_name'=>'','b_keyword'=>'','b_parent'=>$data['b_id'],'b_parentname'=>$data['b_name'],'orderid'=>'','b_type'=>'','pic'=>'');
}
else{
if ($data['b_parent'] == '-1'){
$data['b_parentname'] = "根品牌";
}
else{
$pdata = $db->row_select_one('brand',"b_id=".intval($data['b_parent']));
$data['b_parentname'] = $pdata['b_name'];
}
}
}
$tpl->assign( 'brand',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_brand.html');
exit;
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list&page=".$page_g);

?>