<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '单页管理';
$ac_arr = array('list'=>'单页列表','add'=>'添加单页','edit'=>'编辑单页','del'=>'删除单页','bulkdel'=>'批量删除','bulksort'=>'更新排序','html'=>'生成静态','bulkhtml'=>'批量生成静态');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
$array_pagesort = arr_pagesort();
$page_g = isset($_REQUEST['page']) ?intval($_REQUEST['page']) : 1;
$tpl->assign( 'page_g',$page_g );
if ($ac == 'list')
{
include(INC_DIR.'Page.class.php');
$Page = new Page($db->tb_prefix.'page','1=1','*','20','orderid');
$list = $Page->get_data();
$page = $Page ->page;
foreach($list as $key =>$value){
$list[$key]['sortname'] = $array_pagesort[$value['s_id']];
}
$button_basic = $Page->button_basic();
$button_select = $Page->button_select();
$tpl->assign( 'button_basic',$button_basic );
$tpl->assign( 'button_select',$button_select );
$tpl->assign( 'pagelist',$list );
$tpl->assign( 'page',$page );
$tpl->display( 'admin/page_list.html');
exit;
}
elseif ($ac == 'del')
{
$p_id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('page',"p_id=$p_id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('page',"p_id in($str_id)");
}
elseif ($ac == 'bulksort')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v)
{
$rs = $db->row_update('page',array('orderid'=>$_POST['orderid'][$v]),"p_id=".intval($v));
}
}
elseif ($ac == 'html')
{
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = html_page($id);
}
elseif ($ac == 'bulkhtml')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach($_POST['bulkid'] as $key =>$value) {
$rs = html_page($value);
}
}
elseif ($ac == 'add'||$ac == 'edit')
{
if (submitcheck('a'))
{
$arr_not_empty = array('p_title'=>'单页名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
foreach (array('p_info') as $v)
{
$_POST[$v] = htmlspecialchars($_POST[$v]);
}
$post = post('s_id','p_title','p_info','p_tql','p_page','orderid');
$post['orderid'] = intval($post['orderid']);
if ($ac == 'add')
{
$rs = $db->row_insert('page',$post);
$insertid = $db->insert_id();
if(empty($post['p_page'])){
$rs = $db->row_update('page',array('p_page'=>$insertid.'.html'),"p_id=".$insertid);
}
html_page(intval($insertid));
}
else
{
$rs = $db->row_update('page',$post,"p_id=".intval($_POST['id']));
html_page(intval($_POST['id']));
}
}
else 
{
if (empty($_GET['id']))
{
$data = array('s_id'=>'','p_title'=>'','p_info'=>'','p_tql'=>'about.html','p_page'=>'','orderid'=>'');
}
else
{
$data = $db->row_select_one('page',"p_id=".intval($_GET['id']));
}
$select_pagesort = select_make($data['s_id'],$array_pagesort);
$tpl->assign( 'selectpagesort',$select_pagesort );
$tpl->assign( 'page',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/add_page.html');
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