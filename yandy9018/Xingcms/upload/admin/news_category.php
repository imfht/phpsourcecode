<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '新闻分类管理';
$ac_arr = array('list'=>'分类列表','add'=>'添加分类','edit'=>'编辑分类','del'=>'删除分类','bulkdel'=>'批量删除','bulksort'=>'更新排序');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$array_cattype = array('1'=>'内部分类','2'=>'外部链接');
function delete_child($catid) {
global $db;
$catid = intval($catid);
if (empty($catid)) return false;
$list = $db ->row_select('news_category',"parentid=".$catid);
if ($list) {
foreach($list as $key =>$value){
delete_child($value['catid']);
$db ->row_delete('news_category',"catid=".$value['catid']);
}
}
return true;
}
if ($ac == 'list') {
$str = "<tr>
				<td align='center'><input type='checkbox' name='bulkid[]' value='\$catid'></td>
				<td align='center'>\$catid</td>
				<td align='center'><input type='text' size='5' name='listorder[\$catid]' value='\$listorder' class='ip01'></td>
				<td align='left'>\$spacer <a href='".ADMIN_PAGE."?m=news&a=list&catid=\$catid'>\$catname</a></td>
				<td align='center' width='160' class='rightmenu'><a href='".ADMIN_PAGE."?m=news_category&a=add&parentid=\$catid' class='add'>添加子类</a> | <a href='".ADMIN_PAGE."?m=news_category&a=edit&id=\$catid' class='edit'>编辑</a> | <a href=javascript:if(confirm('确实要删除吗?'))location='".ADMIN_PAGE."?m=news_category&a=del&id=\$catid' class='del'>删除</a></td>
			</tr>";
$tree ->init($commoncache['news_category']);
$tree ->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
$tree ->nbsp = '&nbsp;&nbsp;&nbsp;';
$categorys = $tree ->get_tree(0,$str);
$tpl ->assign('sortlist',$categorys);
$tpl ->display('admin/news_category_list.html');
exit;
}
elseif ($ac == 'del') {
$catid = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
delete_child($catid);
$rs = $db ->row_delete('news_category',"catid=$catid");
$fzz = new fzz_cache;
$fzz->clear_all();
}
elseif ($ac == 'bulksort') {
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v) {
$rs = $db ->row_update('news_category',array('listorder'=>$_POST['listorder'][$v]),"catid=".intval($v));
}
$fzz = new fzz_cache;
$fzz->clear_all();
}
elseif ($ac == 'add'||$ac == 'edit') {
if (submitcheck('a')) {
$arr_not_empty = array('catname'=>'分类名称不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('catname','parentid','url','isshow','keywords','description');
$post['catname'] = trim($post['catname']);
$post['parentid'] = intval($post['parentid']);
$post['isshow'] = intval($post['isshow']);
$post['keywords'] = trim($post['keywords']);
$post['description'] = trim($post['description']);
if ($ac == 'add') {
$post['listorder'] = !empty($post['listorder']) ?intval($post['listorder']) : 0;
$rs = $db ->row_insert('news_category',$post);
}else {
$rs = $db ->row_update('news_category',$post,"catid=".intval($_POST['id']));
}
$fzz = new fzz_cache;
$fzz->clear_all();
}
else {
if (empty($_GET['id'])) {
$data = array('catid'=>'','parentid'=>'','catname'=>'','isshow'=>'','url'=>'','description'=>'');
}else {
$data = $db ->row_select_one('news_category',"catid=".intval($_GET['id']));
}
$parentid = isset($_GET['parentid']) ?$_GET['parentid'] : $data['parentid'];
$select_category = select_category('news_category',$parentid,'name="parentid" id="parentid"','-作为一级分类-',$data['parentid']);
$tpl ->assign('selectcategory',$select_category);
$tpl ->assign('category',$data);
$tpl ->assign('ac',$ac);
$tpl ->display('admin/add_news_category.html');
exit;
}
}
else {
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac] .($rs ?'成功': '失败'),ADMIN_PAGE."?m=$m&a=list");

?>