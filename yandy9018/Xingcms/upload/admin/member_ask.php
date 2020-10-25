<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '问答管理';
$ac_arr = array('list'=>'问答管理列表','replyask'=>'回复','del'=>'删除问答管理','bulkdel'=>'批量删除');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl->assign( 'mod_name',$m_name );
$tpl->assign( 'ac_arr',$ac_arr );
$tpl->assign( 'ac',$ac );
$page_g = isset($_REQUEST['page']) ?intval($_REQUEST['page']) : 1;
$tpl->assign( 'page_g',$page_g );
if ($ac == 'list')
{
$where = '1=1';
if (!empty($_GET['keywords']))
{
$keywords = $_GET['keywords'];
$where .= " AND ask LIKE '%{$keywords}%' or reply LIKE '%{$keywords}%'";
}
include(INC_DIR.'Page.class.php');
$Page = new Page($db->tb_prefix.'member_feedback',$where,'*','20','id desc');
$list = $Page->get_data();
$page = $Page ->page;
foreach($list as $key =>$value){
$list[$key]['asktime'] = date('Y-m-d H:i:s',$value['asktime']);
$user = $db ->row_select_one('member','id ='.$value['uid'],'id,username');
$list[$key]['username'] = $user['username'];
if($value['auid']!=0){
$askuser = $db ->row_select_one('member','id ='.$value['auid'],'id,username');
$list[$key]['askusername'] = $user['username'];
}
}
$button_basic = $Page->button_basic();
$button_select = $Page->button_select();
$tpl->assign( 'asklist',$list );
$tpl->assign( 'button_basic',$button_basic );
$tpl->assign( 'button_select',$button_select );
$tpl->assign( 'page',$page );
$tpl->display( 'admin/member_ask_list.html');
exit;
}
elseif ($ac == 'del')
{
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = $db->row_delete('member_feedback',"id=$id");
}
elseif ($ac == 'bulkdel')
{
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db->row_delete('member_feedback',"id in($str_id)");
}
elseif ($ac == 'replyask')
{
if (submitcheck('a'))
{
$arr_not_empty = array('reply'=>'回复不能为空');
can_not_be_empty($arr_not_empty,$_POST);
$post['reply'] = $_POST['reply'];
$post['replytime'] = time();
$rs = $db->row_update('member_feedback',$post,"id=".intval($_POST['id']));
}
else 
{
if (empty($_GET['id'])) $data = array('reply'=>'','asktime'=>'','ask'=>'');
else $data = $db->row_select_one('member_feedback',"id=".intval($_GET['id']));
$data['asktime'] = date('Y-m-d H:i:s',$data['asktime']);
$data['replytime'] = date('Y-m-d H:i:s',$data['replytime']);
$tpl->assign( 'ask',$data );
$tpl->assign( 'ac',$ac );
$tpl->display( 'admin/member_replyask.html');
exit;
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list&page=".$page_g);
?>