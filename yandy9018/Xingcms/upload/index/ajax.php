<?php

if(!defined('APP_IN')) exit('Access Denied');
if (!empty($_GET['ajax']) &&isset($_GET['login']))
{header('Content-Type:text/plain; charset=utf-8');
if(!empty($_SESSION['USER_ID']) ||!empty($_SESSION['USER_NAME'])){
$loginstr=$_SESSION['USER_NAME']."。<a href='".WEB_PATH."/index.php?m=user'>[会员中心]</a> <a href='".WEB_PATH."/index.php?m=user&a=logout'>[退出]</a>";
}
else{
$loginstr = "<a href='".WEB_PATH."/index.php?m=login' target='_blank'>[请登录]</a>&nbsp;&nbsp; 新用户？&nbsp;&nbsp;<a href='".WEB_PATH."/index.php?m=register' target='_blank'>[请注册]</a>";
}
echo $loginstr;
exit;
}
if (!empty($_GET['ajax']) &&isset($_GET['contact']))
{header('Content-Type:text/plain; charset=utf-8');
$str = "";
if(empty($_SESSION['USER_ID'])){
$str = "<tr><th><span class='red'>*</span> 车主姓名：</th>
					<td colspan='5'><input name='p_username' type='text' size='30' class='inp01' value='' datatype='s' nullmsg='请填写车主姓名！'/></td></tr>
				<tr><th><span class='red'>*</span> 手机号：</th>
					<td colspan='5'><input name='p_tel' type='text' size='30' class='inp01' value='' datatype='m' nullmsg='请填写手机号！' errormsg='手机号码格式不正确！'/></td>
				</tr>";
}
echo $str;
exit;
}
if (!empty($_GET['ajax']) &&!empty($_GET['bid']))
{header('Content-Type:text/plain; charset=utf-8');
$arr_b = explode("_",trim($_GET['bid']));
$brandlist = "<option value='' selected>请选择车系</option>";
$list = $db->row_select('brand',"b_parent='".$arr_b[1]."'");
if($list){
foreach($list as $key =>$value){
$brandlist .= "<optgroup label=".$value['b_name']." style='font-style: normal; background: none repeat scroll 0% 0% rgb(239, 239, 239); text-align: center;'></optgroup>";
$sublist = $db->row_select('brand',"b_parent='".$value['b_id']."'");
foreach($sublist as $subkey =>$subvalue){
$brandlist .= "<option value=".$subvalue['b_id'].">".$subvalue['b_name']."</option>";
}
}
}
echo $brandlist;
exit;
}
if (!empty($_GET['ajax']) &&!empty($_GET['brandid']))
{header('Content-Type:text/plain; charset=utf-8');
$brandlist = "<option value='' selected>请选择车系</option>";
$list = $db->row_select('brand',"b_parent='".$_GET['brandid']."'");
if($list){
foreach($list as $key =>$value){
$brandlist .= "<optgroup label=".$value['b_name']." style='font-style: normal; background: none repeat scroll 0% 0% rgb(239, 239, 239); text-align: center;'></optgroup>";
$sublist = $db->row_select('brand',"b_parent='".$value['b_id']."'");
foreach($sublist as $subkey =>$subvalue){
$brandlist .= "<option value=".$subvalue['b_id'].">".$subvalue['b_name']."</option>";
}
}
}
echo $brandlist;
exit;
}
if (!empty($_GET['ajax']) &&!empty($_GET['subbrandid']))
{header('Content-Type:text/plain; charset=utf-8');
$brandlist = "<option value='' selected>请选择款式</option>";
$list = $db->row_select('brand',"b_parent='".$_GET['subbrandid']."'");
if($list){
foreach($list as $key =>$value){
$brandlist .= "<optgroup label='".$value['b_name']."' style='font-style: normal; background: none repeat scroll 0% 0% rgb(239, 239, 239); text-align: center;'></optgroup>";
$sublist = $db->row_select('brand',"b_parent='".$value['b_id']."'");
foreach($sublist as $subkey =>$subvalue){
$brandlist .= "<option value=".$subvalue['b_id'].">".$subvalue['b_name']."</option>";
}
}
}
echo $brandlist;
exit;
}
if (!empty($_GET['ajax']) &&!empty($_GET['subsubbrandid']))
{header('Content-Type:text/plain; charset=utf-8');
$brandlist = "<option value='' selected>请选择款式</option>";
$list = $db->row_select('brand',"b_parent='".$_GET['subsubbrandid']."'");
if($list){
foreach($list as $key =>$value){
$brandlist .= "<option value=".$value['b_id'].">".$value['b_name']."</option>";
}
}
echo $brandlist;
exit;
}
if (!empty($_GET['ajax']) &&isset($_GET['cityid']))
{header('Content-Type:text/plain; charset=utf-8');
$provincelist = "<option value='' selected>请选择城市</option>";
$list = $db->row_select('area',"parentid='".$_GET['cityid']."'");
if($list){
foreach($list as $key =>$value){
$provincelist .= "<option value=".$value['id'].">".$value['name']."</option>";
}
}
echo $provincelist;
exit;
}
if (!empty($_GET['ajax']) &&isset($_GET['searchcityid']))
{header('Content-Type:text/plain; charset=utf-8');
$citylist = "";
$list = $db->row_select('area',"parentid='".$_GET['searchcityid']."'");
if($list){
foreach($list as $key =>$value){
$citylist .= "<option value='c_".$value['id']."'>".$value['name']."</option>";
}
}
echo $citylist;
exit;
}
if (!empty($_GET['ajax']) &&isset($_GET['carcount']))
{header('Content-Type:text/plain; charset=utf-8');
$todaytime = strtotime(date('Y')."-".date('m')."-".date('d')." 00:00:00");
$count1 = $db->row_count('cars','issell=0');
$count2 = $db->row_count('cars','issell=0 and p_addtime>'.$todaytime);
$carcount = "<p>目前有 <span class='counts'>".$count1."</span> 辆二手车供您选择</p><p>今天新增 <span class='counts'>".$count2."</span> 辆</p>";
echo $carcount;
exit;
}
if (!empty($_GET['ajax']) &&isset($_GET['carshit']))
{header('Content-Type:text/plain; charset=utf-8');
$rs = $db->query_unbuffered("update ".$db->tb_prefix."cars set p_hits = p_hits+1 where p_id=".intval($_GET['id']));
$data = $db->row_select_one('cars',"p_id=".intval($_GET['id']),'p_hits');
echo $data['p_hits'];
exit;
}
if (!empty($_GET['ajax']) &&isset($_GET['rentcarshit']))
{header('Content-Type:text/plain; charset=utf-8');
$rs = $db->query_unbuffered("update ".$db->tb_prefix."rentcars set p_hits = p_hits+1 where p_id=".intval($_GET['id']));
$data = $db->row_select_one('rentcars',"p_id=".intval($_GET['id']),'p_hits');
echo $data['p_hits'];
exit;
}
if (!empty($_GET['ajax']) &&isset($_GET['newshit']))
{header('Content-Type:text/plain; charset=utf-8');
$rs = $db->query_unbuffered("update ".$db->tb_prefix."news set n_hits = n_hits+1 where n_id=".intval($_GET['id']));
$data = $db->row_select_one('news',"n_id=".intval($_GET['id']),'n_hits');
echo $data['n_hits'];
exit;
}
if (!empty($_POST['param']) and $_POST['name']=="authcode")
{
if($_SESSION['authcode'] == $_POST['param']){
echo '{"info":"验证码正确！","status":"y"}';
}
else{
echo '{"info":"验证码不正确！","status":"n"}';
}
exit;
}
if(!empty($_GET['ajax']) &&isset($_GET['cartype']) &&$_GET['cartype']=="hot"){
header('Content-Type:text/plain; charset=utf-8');
$where = "ishot=1 and p_mainpic!='' and isshow=1";
if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
$_COOKIE['city'] = 0;
}
$list = get_carlist($_COOKIE['city'],$where,'10','listtime desc');
$str = "";
foreach($list as $key =>$value) {
$str .= "<div class='hotcarlist'><a href=".$value['p_url']." target='_blank'><img src=".$value['p_mainpic']."></a>
			<p class='mt5'><span class='orange01 fb fr'>".$value['p_price']."</span><a href=".$value['p_url']." target='_blank' >".$value['p_shortname']."</a></p>
			<p class='gray01'>".$value['p_year']."年".$value['p_month']."月上牌</p></div>";
}
echo $str;
exit;
}
if(!empty($_GET['ajax']) &&isset($_GET['cartype']) &&$_GET['cartype']=="indexhot"){
header('Content-Type:text/plain; charset=utf-8');
$where = "ishot=1 and p_mainpic!='' and isshow=1";
if(!empty($_COOKIE['city'])){
$where .= " and cid = ".$_COOKIE['city'];
}
if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
$_COOKIE['city'] = 0;
}
$list = get_carlist($_COOKIE['city'],$where,'10','listtime desc');
$str = "";
foreach($list as $key =>$value) {
if($key<3){
$class = "class='num01'";
}
else{
$class = "class='num02'";
}
$str .= "<p class='clearfix'><span class='orange01 fb fr'>".$value['p_price']."</span><span ".$class.">".($key+1)."</span><a href='".$value['p_url']."' target='_blank' class='fl pl10'>".$value['p_shortname']."</a></p>";
}
echo $str;
exit;
}
if(!empty($_GET['ajax']) &&isset($_GET['yuyue']) &&$_GET['yuyue']==1 ){
header('Content-Type:text/plain; charset=utf-8');
if(!empty($_POST['name']) and !empty($_POST['mobilephone']) and !empty($_POST['ordertime']) and !empty($_POST['orderinfo'])){
$rs = $db->row_insert('subscribe',array('pid'=>intval($_POST['pid']),'uid'=>intval($_POST['uid']),'name'=>trim($_POST['name']),'mobilephone'=>trim($_POST['mobilephone']),'ordertime'=>strtotime(trim($_POST['ordertime'])),'orderinfo'=>trim($_POST['orderinfo']),'addtime'=>time()));
if($rs){
$status = 1;
}
else{
$status = 0;
}
}
else{
$status = 0;
}
echo $status;
exit;
}
if(!empty($_GET['ajax']) &&isset($_GET['xunjia']) &&$_GET['xunjia']==1 ){
header('Content-Type:text/plain; charset=utf-8');
if(!empty($_POST['name']) and !empty($_POST['mobilephone'])){
$rs = $db->row_insert('inquiry',array('pid'=>intval($_POST['pid']),'uid'=>intval($_POST['uid']),'name'=>trim($_POST['name']),'mobilephone'=>trim($_POST['mobilephone']),'addtime'=>time()));
if($rs){
$status = 1;
}
else{
$status = 0;
}
}
else{
$status = 0;
}
echo $status;
exit;
}
if(!empty($_GET['ajax']) &&isset($_GET['compare']) &&$_GET['compare']==1 ){
header('Content-Type:text/plain; charset=utf-8');
$pids = "";
if(!empty($_GET['pid'])){
if(!empty($_COOKIE['compareids'])){
$pids .= $_COOKIE['compareids'].",".intval($_GET['pid']);
}else{
$pids = intval($_GET['pid']);
}
setMyCookie("compareids",$pids,time() +COOKIETIME);
$carcounts = count(explode(",",$pids));
}
else{
$carcounts = 0;
}
echo $carcounts;
exit;
}
if (!empty($_GET['ajax']) &&isset($_GET['comparecounts']))
{header('Content-Type:text/plain; charset=utf-8');
if(empty($_COOKIE['compareids'])){
$carcounts = 0;
}
else{
$carcounts = count(explode(",",$_COOKIE['compareids']));
}
echo $carcounts;
exit;
}
if (!empty($_GET['ajax']) &&isset($_GET['comparers']))
{header('Content-Type:text/plain; charset=utf-8');
$str = "<ul id='ulcompara' class='tc14-tplb clearfix'>";
if(!empty($_COOKIE['compareids'])){
$list = $db->row_select('cars',"p_id in(".$_COOKIE['compareids'].")");
foreach($list as $value){
$value['carname'] = _substr($value['p_allname'],0,28);
$str .= "<li class='clearfix'>
						<a target='_blank' href=''><img src=''></a>
						<h5><a target='_blank' href=''>".$value['carname']."</a></h5>
						<p>".$value['p_year']."年上牌 ".$value['p_kilometre']."万公里</p>
						<p><em> ".$value['p_price']."万</em></p>
						<div class='tc14-cysc'>
							<span class='button_gray h20-p8'>
								<a href='javascript:;'>取消对比</a>
							</span>
						</div>
					</li>";
}
}
$str .= "</ul>";
echo $str;
exit;
}
?>