<?php

if (!defined('APP_IN')) exit('Access Denied');
$id = isset($_GET['id']) ?intval($_GET['id']) : 0;
$filmlist = $db ->row_select('news',"catid=64 and isrecom=1",'n_id,n_pic','5','n_addtime desc');
$tpl ->assign('film_list',$filmlist);
$data = $db ->row_select_one('member',"id=".$id);
$data['shopdetail'] = htmlspecialchars_decode($data['shopdetail']);
if (!$data) showmsg('参数错误',-1);
if ($data['ischeck']!=1) showmsg('该会员未开通店铺',-1);
$tpl ->assign('shop',$data);
include(dirname(dirname(dirname(__FILE__))).'/'.INC_DIR .'Page.class.php');
$Page = new Page($db ->tb_prefix .'cars','isshow=1 and issell=0 and uid='.$id,'*','10','listtime desc');
$listnum = $Page ->total_num;
$list = $Page ->get_data();
foreach($list as $key =>$value) {
if (!empty($value['p_mainpic'])) {
$pic = explode(".",$value['p_mainpic']);
$list[$key]['p_mainpic'] = WEB_DOMAIN."/".$pic[0] ."_small".".".$pic[1];
}
$list[$key]['p_shortname'] = _substr($value['p_allname'],0,26);
$list[$key]['listtime'] = date('Y-m-d',$value['listtime']);
$list[$key]['p_details'] = _substr($value['p_details'],0,80);
$list[$key]['p_price'] = intval($value['p_price']) == 0 ?"面议": "￥".$value['p_price']."万";
if (!empty($value['p_model'])) $list[$key]['p_modelname'] = $commoncache['modellist'][$value['p_model']];
$list[$key]['p_url'] ="index.php?m=cars&id=".$value['p_id'];
}
$button_basic = $Page ->button_basic_num();
$tpl ->assign('button_basic',$button_basic);
$tpl ->assign('carlist',$list);
$tpl ->display('m/shop.html');
?>