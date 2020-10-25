<?php

if (!defined('APP_IN')) exit('Access Denied');
$id = isset($_GET['id']) ?intval($_GET['id']) : 0;
if($id==0){
$tpl ->assign('film_list',get_filmstrip(2));
include(dirname(dirname(dirname(__FILE__))).'/'.INC_DIR .'Page.class.php');
$Page = new Page($db ->tb_prefix .'news','catid=64','n_id,n_title,n_pic','30','n_addtime desc');
$list = $Page ->get_data();
$button_basic = $Page ->button_basic_num();
$tpl ->assign('newslist',$list);
$tpl ->assign('page_list',$button_basic);
$tpl ->assign('newslist',$list);
$tpl ->display('m/about.html');
}
else{
$data = $db->row_select_one('news','n_id='.$id);
$data['addtime'] = date('Y-m-d H:i:s',$data['n_addtime']);
$data['n_info'] = htmlspecialchars_decode($data['n_info']);
$tpl ->assign('news',$data);
$tpl ->display('m/news.html');
}

?>