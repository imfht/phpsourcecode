<?php

if (!defined('APP_IN')) exit('Access Denied');
if (!empty($_GET['ajax']) &&isset($_GET['bid'])) {
header('Content-Type:text/plain; charset=utf-8');
$brandlist = "<option value='' selected>请选择子品牌</option>";
$list = $db ->row_select('brand',"b_parent='".$_GET['bid'] ."'");
if ($list) {
foreach($list as $key =>$value) {
$brandlist .= "<option value=".$value['b_id'] .">".$value['b_name'] ."</option>";
}
}
echo $brandlist;
exit;
}
if (!empty($_GET['ajax']) &&isset($_GET['p_id'])) {
$str = $_GET['p_pic'];
$arr_picid = explode("/",$str);
$arr_length = count($arr_picid);
$picstr = explode(".",$arr_picid[$arr_length-1]);
if (!empty($_GET['p_id'])) {
$picpath = substr($str,1);
if (file_exists($picpath)) unlink($picpath);
$delstr = $str;
$arr = $db ->row_select_one('cars',"p_id=".intval($_GET['p_id']));
if (!empty($arr['p_pics'])) {
$pic_list = array_flip(explode("|",$arr['p_pics']));
unset($pic_list[$delstr]);
$post['p_pics'] = implode("|",array_flip($pic_list));
$rs = $db ->row_update('cars',$post,"p_id=".intval($_GET['p_id']));
}
}
echo $picstr[0];
exit;
}
$m_name = '车源管理';
$ac_arr = array('list'=>'车源列表','add'=>'添加车源','edit'=>'编辑车源','del'=>'删除车源','sell'=>'改变买卖状态','show'=>'显示车源','refresh'=>'刷新车源','html'=>'生成静态','bulkhtml'=>'批量生成静态','bulkdel'=>'批量删除','bulkrefresh'=>'批量刷新','bulksell'=>'批量改变买卖状态');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl ->assign('mod_name',$m_name);
$tpl ->assign('ac_arr',$ac_arr);
$tpl ->assign('ac',$ac);
$page_g = isset($_REQUEST['page']) ?intval($_REQUEST['page']) : 1;
$tpl ->assign('page_g',$page_g);
if ($ac == 'list') {
$pagecount = $db ->row_count('cars');
$tpl ->assign('pagecount',$pagecount);
if($settings['version']==2 or $settings['version']==3){
$dealercount = $db ->row_count('cars'," uid in (select id from ".$db ->tb_prefix ."member where ischeck=1 and isdealer=2)");
$tpl ->assign('dealercount',$dealercount);
$personcount = $db ->row_count('cars'," uid in (select id from ".$db ->tb_prefix ."member where ischeck=1 and isdealer=1)");
$tpl ->assign('personcount',$personcount);
}
$visitorcount = $db ->row_count('cars'," uid=0 ");
$tpl ->assign('visitorcount',$visitorcount);
$unauditedcount = $db ->row_count('cars',"isshow=0");
$tpl ->assign('unauditedcount',$unauditedcount);
$issellcount = $db ->row_count('cars',"issell=1");
$tpl ->assign('issellcount',$issellcount);
$nosellcount = $db ->row_count('cars',"issell=0");
$tpl ->assign('nosellcount',$nosellcount);
$select_brand = select_make('',$commoncache['markbrandlist'],'请选择品牌');
$tpl ->assign('select_brand',$select_brand);
if($settings['version']==3){
$province_search = arr_province();
$select_province_search = select_make('',$province_search,'请选择省份');
$tpl ->assign('selectprovincesearch',$select_province_search);
}
$where = '1=1';
if (isset($_GET['clear']) and $_GET['clear'] == 1) {
setMyCookie("province",'',time() -COOKIETIME);
setMyCookie("city",'',time() -COOKIETIME);
setMyCookie("keywords",'',time() -COOKIETIME);
setMyCookie("brand",'',time() -COOKIETIME);
setMyCookie("subbrand",'',time() -COOKIETIME);
setMyCookie("subsubbrand",'',time() -COOKIETIME);
setMyCookie("price",'',time() -COOKIETIME);
setMyCookie("age",'',time() -COOKIETIME);
setMyCookie("model",'',time() -COOKIETIME);
setMyCookie("gas",'',time() -COOKIETIME);
setMyCookie("show",'',time() -COOKIETIME);
setMyCookie("order",'',time() -COOKIETIME);
setMyCookie("usertype",'',time() -COOKIETIME);
setMyCookie("status",'',time() -COOKIETIME);
setMyCookie("recom",'',time() -COOKIETIME);
}
$arr_p = array('1'=>'3万以下','2'=>'3-5万','3'=>'5-8万','4'=>'8-12万','5'=>'12-18万','6'=>'18-24万','7'=>'24-35万','8'=>'35-50万','9'=>'50-100万','10'=>'100万以上');
$tpl ->assign('arr_price',$arr_p);
$arr_a = array('1'=>'1年以内','2'=>'2年以内','3'=>'3年以内','4'=>'4年以内','5'=>'5年以内','6'=>'6年以内','7'=>'6年以上');
$tpl ->assign('arr_age',$arr_a);
$arr_g = array('1'=>'1.0L','2'=>'2.0L','3'=>'3.0L','4'=>'4.0L','5'=>'5.0L及以上');
$tpl ->assign('arr_gas',$arr_g);
$arr_b = arr_brand_recom();
$tpl ->assign('arr_brand',$arr_b);
$arr_t = array('1'=>'特荐','2'=>'推荐','3'=>'热门');
$tpl ->assign('arr_recom',$arr_t);
$arr_s = array('1'=>'未卖','2'=>'已卖');
$tpl ->assign('arr_status',$arr_s);
$arr_u = array('1'=>'商家','2'=>'个人');
$tpl ->assign('arr_usertype',$arr_u);
if (isset($_GET['b'])) {
if (!empty($_GET['b'])) {
setMyCookie("brand",intval($_GET['b']),time() +COOKIETIME);
}
if (!empty($_GET['sb'])) {
setMyCookie("subbrand",intval($_GET['sb']),time() +COOKIETIME);
}
if (!empty($_GET['sbsb'])) {
setMyCookie("subsubbrand",intval($_GET['sbsb']),time() +COOKIETIME);
}
if (isset($_COOKIE['subsubbrand']) and $_COOKIE['subsubbrand'] == 0) {
setMyCookie("brand",'',time() -COOKIETIME);
setMyCookie("subbrand",'',time() -COOKIETIME);
setMyCookie("subsubbrand",'',time() -COOKIETIME);
}
}
if (isset($_GET['c'])) {
$arr_c = explode("_",trim($_GET['c']));
if ($arr_c['0'] == "p") {
if (isset($arr_c[1])) {
setMyCookie("price",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['price']) and $_COOKIE['price'] == 0) {
setMyCookie("price",'',time() -COOKIETIME);
}
}
elseif ($arr_c['0'] == "a") {
if (isset($arr_c[1])) {
setMyCookie("age",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['age']) and $_COOKIE['age'] == 0) {
setMyCookie("age",'',time() -COOKIETIME);
}
}
elseif ($arr_c['0'] == "m") {
if (isset($arr_c[1])) {
setMyCookie("model",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['model']) and $_COOKIE['model'] == 0) {
setMyCookie("model",'',time() -COOKIETIME);
}
}
elseif ($arr_c['0'] == "g") {
if (isset($arr_c[1])) {
setMyCookie("gas",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['gas']) and $_COOKIE['gas'] == 0) {
setMyCookie("gas",'',time() -COOKIETIME);
}
}
elseif($arr_c['0'] == "t"){
if (isset($arr_c[1])) {
setMyCookie("recom",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['recom']) and $_COOKIE['recom'] == 0) {
setMyCookie("recom",'',time() -COOKIETIME);
}
}
elseif($arr_c['0'] == "u"){
if (isset($arr_c[1])) {
setMyCookie("usertype",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['usertype']) and $_COOKIE['usertype'] == 0) {
setMyCookie("usertype",'',time() -COOKIETIME);
}
}
elseif($arr_c['0'] == "s"){
if (isset($arr_c[1])) {
setMyCookie("status",intval($arr_c[1]),time() +COOKIETIME);
}
if (isset($_COOKIE['status']) and $_COOKIE['status'] == 0) {
setMyCookie("status",'',time() -COOKIETIME);
}
}
if($settings['version']==3){
if (!empty($_GET['c'])) {
setMyCookie("province",intval($_GET['c']),time() +COOKIETIME);
}
if (!empty($_GET['cy'])) {
setMyCookie("city",intval($_GET['cy']),time() +COOKIETIME);
}
if (isset($_COOKIE['city']) and $_COOKIE['city'] == 0) {
setMyCookie("province",'',time() -COOKIETIME);
setMyCookie("city",'',time() -COOKIETIME);
}
}
}
if (isset($_COOKIE['subbrand']) and isset($_GET['sb']) and  $_GET['sb'] == 0) {
setMyCookie("subbrand",'',time() -COOKIETIME);
}
if (isset($_COOKIE['subsubbrand']) and isset($_GET['sbsb']) and  $_GET['sbsb'] == 0) {
setMyCookie("subsubbrand",'',time() -COOKIETIME);
}
if (isset($_COOKIE['city']) and isset($_GET['cy']) and  $_GET['cy'] == 0) {
setMyCookie("city",'',time() -COOKIETIME);
}
if (isset($_COOKIE['subbrand']) and $_COOKIE['subbrand'] <>0) {
$subbrand = $db ->row_select_one('brand','b_id='.$_COOKIE['subbrand'],'b_name');
$tpl ->assign('subrandname',$subbrand['b_name']);
$where .= " and p_subbrand = ".$_COOKIE['subbrand'];
}
if (isset($_COOKIE['subsubbrand']) and $_COOKIE['subsubbrand'] <>0) {
$subsubbrand = $db ->row_select_one('brand','b_id='.$_COOKIE['subsubbrand'],'b_name');
$tpl ->assign('subsubrandname',$subsubbrand['b_name']);
$where .= " and p_subsubbrand = ".$_COOKIE['subsubbrand'];
}
if (isset($_COOKIE['brand']) and $_COOKIE['brand'] <>0) {
$where .= " and p_brand = ".$_COOKIE['brand'];
}
if (isset($_COOKIE['subbrand']) and $_COOKIE['subbrand'] <>0) {
$where .= " and p_subbrand = ".$_COOKIE['subbrand'];
}
if (isset($_COOKIE['subsubbrand']) and $_COOKIE['subsubbrand'] <>0) {
$where .= " and p_subsubbrand = ".$_COOKIE['subsubbrand'];
}
if($settings['version']==3){
if (isset($_COOKIE['province']) and $_COOKIE['province'] <>0) {
$where .= " and aid = ".$_COOKIE['province'];
}
if (isset($_COOKIE['city']) and $_COOKIE['city'] <>0) {
$where .= " and cid = ".$_COOKIE['city'];
}
}
if (isset($_COOKIE['price']) and $_COOKIE['price'] <>0) {
switch ($_COOKIE['price']) {
case 1:
$where .= " and p_price > 0 and p_price <= 3";
break;
case 2:
$where .= " and p_price > 3 and p_price <= 5";
break;
case 3:
$where .= " and p_price > 5 and p_price <= 8";
break;
case 4:
$where .= " and p_price > 8 and p_price <= 12";
break;
case 5:
$where .= " and p_price > 12 and p_price <= 18";
break;
case 6:
$where .= " and p_price > 18 and p_price <= 24";
break;
case 7:
$where .= " and p_price > 24 and p_price <= 35";
break;
case 8:
$where .= " and p_price > 35 and p_price <= 50";
break;
case 9:
$where .= " and p_price > 50 and p_price <= 100";
break;
case 10:
$where .= " and p_price > 100";
break;
default:
$where .= "";
}
}
if (isset($_COOKIE['age']) and $_COOKIE['age'] <>0) {
$compareyear = date("Y") -$_COOKIE['age'];
switch ($_COOKIE['age']) {
case 7:
$where .= " and p_year < ".$compareyear;
break;
default:
$where .= " and p_year >= ".$compareyear;
}
}
if (isset($_COOKIE['model']) and $_COOKIE['model'] <>0) {
$where .= " and p_model = ".$_COOKIE['model'];
}
if (isset($_COOKIE['gas']) and $_COOKIE['gas'] <>0) {
switch ($_COOKIE['gas']) {
case 1:
$where .= " and p_gas = 1.0";
break;
case 2:
$where .= " and p_gas = 2.0";
break;
case 3:
$where .= " and p_gas = 3.0";
break;
case 4:
$where .= " and p_gas = 4.0";
break;
case 5:
$where .= " and p_gas >= 5.0";
break;
default:
$where .= "";
}
}
if (isset($_COOKIE['usertype']) and $_COOKIE['usertype'] <>0) {
switch($_COOKIE['usertype']) {
case 1:
$where .= " and uid in (select id from ".$db ->tb_prefix ."member where ischeck=1 and isdealer=2)";
break;
case 2:
$where .= " and uid in (select id from ".$db ->tb_prefix ."member where isdealer=1)";
break;
case 3:
$where .= " and uid=0";
break;
}
}
if (isset($_COOKIE['recom']) and $_COOKIE['recom'] <>0) {
switch ($_COOKIE['recom']) {
case 1:
$where .= " and issprecom = 1 ";
break;
case 2:
$where .= " and isrecom = 1 ";
break;
case 3:
$where .= " and ishot = 1 ";
break;
}
}
if (isset($_COOKIE['status']) and $_COOKIE['status'] <>0) {
switch ($_COOKIE['status']) {
case 1:
$where .= " and issell = 0 ";
break;
case 2:
$where .= " and issell = 1 ";
break;
}
}
if (isset($_GET['keywords']) and $_GET['keywords'] != ""and $_GET['keywords'] != "请输入要搜索的关键词,如:宝马") {
setMyCookie("keywords",$_GET['keywords'],time() +COOKIETIME);
}elseif (isset($_GET['keywords']) and $_GET['keywords'] == "") {
setMyCookie("keywords",'',time() -COOKIETIME);
}
if (!empty($_COOKIE['keywords'])) {
$where .= " AND (`p_allname` like '%".$_COOKIE['keywords'] ."%' or `p_keyword` like '%".$_COOKIE['keywords'] ."%' or `p_no` like '%".$_COOKIE['keywords'] ."%')";
}
if (isset($_GET['order'])) {
setMyCookie("order",$_GET['order'],time() +COOKIETIME);
}else {
setMyCookie("order",1,time() +COOKIETIME);
}
$orderby = "";
if (!empty($_COOKIE['order'])) {
switch ($_COOKIE['order']) {
case 1:
$orderby = "listtime desc";
break;
case 2:
$orderby = "listtime asc";
break;
case 3:
$orderby = "p_price asc";
break;
case 4:
$orderby = "p_price desc";
break;
case 5:
$orderby = "p_kilometre asc";
break;
case 6:
$orderby = "p_kilometre desc";
break;
case 7:
$orderby = "p_year desc,p_month desc";
break;
case 8:
$orderby = "p_year asc,p_month asc";
break;
default:
$orderby = "listtime desc";
}
}
include(INC_DIR .'Page.class.php');
$Page = new Page($db ->tb_prefix .'cars',$where,'*','50','listtime desc');
$list = $Page ->get_data();
$page = $Page ->page;
foreach($list as $key =>$value) {
$list[$key]['listtime'] = date('Y-m-d H:i:s',$value['listtime']);
$list[$key]['p_addtime'] = date('Y-m-d H:i:s',$value['p_addtime']);
if (!empty($value['p_model'])) $list[$key]['p_modelname'] = $commoncache['modellist'][$value['p_model']];
$list[$key]['p_url'] = HTML_DIR ."buycars/".date('Y/m/d',$value['p_addtime']) ."/".$value['p_id'] .".html";
if($settings['version']==2 or $settings['version']==3){
if (!empty($value['uid'])) {
$user = $db ->row_select_one('member','id='.$value['uid'],'username');
$list[$key]['username'] = $user['username'];
if($settings['version']==3){
if($value['aid']!=0){
$list[$key]['province']=$commoncache['provincelist'][$value['aid']];
}
if($value['cid']!=0){
$list[$key]['city']=$commoncache['citylist'][$value['cid']];
}
}
}else {
$list[$key]['username'] = "游客";
}
}
}
$button_basic = $Page ->button_basic();
$button_select = $Page ->button_select();
$tpl ->assign('button_basic',$button_basic);
$tpl ->assign('button_select',$button_select);
$tpl ->assign('carslist',$list);
$tpl ->assign('currpage',$page);
$tpl ->display('admin/cars_list.html');
exit;
}
elseif ($ac == 'del') {
$p_id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$car = $db ->row_select_one('cars',"p_id=$p_id");
if (!empty($car['p_pics'])) {
$pic_list = explode('|',$car['p_pics']);
foreach($pic_list as $key =>$value) {
$smallpic = str_replace(".","_small.",$value);
if(file_exists($value)){
unlink($value);
}
if(file_exists($smallpic)){
unlink($smallpic);
}
}
}
$rs = $db ->row_delete('cars',"p_id=$p_id");
$rs = $db ->row_delete('selfdefine_value',"p_id=$p_id");
}
elseif ($ac == 'bulkdel') {
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach($_POST['bulkid'] as $key =>$value) {
$car = $db ->row_select_one('cars',"p_id=".$value);
if (!empty($car['p_pics'])) {
$pic_list = explode('|',$car['p_pics']);
foreach($pic_list as $key =>$value) {
$smallpic = str_replace(".","_small.",$value);
if(file_exists($value)){
unlink($value);
}
if(file_exists($smallpic)){
unlink($smallpic);
}
}
}
}
$str_id = return_str_id($_POST['bulkid']);
$rs = $db ->row_delete('cars',"p_id in($str_id)");
$rs = $db ->row_delete('selfdefine_value',"p_id in($str_id)");
}
elseif ($ac == 'bulkrefresh') {
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$listtime = time();
$rs = $db ->row_update('cars',array('listtime'=>$listtime),"p_id in($str_id)");
}
elseif ($ac == 'bulksell') {
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
$str_id = return_str_id($_POST['bulkid']);
$rs = $db ->row_update('cars',array('issell'=>'1'),"p_id in($str_id)");
foreach($_POST['bulkid'] as $key =>$value) {
html_cars($value);
}
}
elseif ($ac == 'sell') {
$issell = intval($_GET['issell']);
$rs = $db ->row_update('cars',array('issell'=>$issell),"p_id=".intval($_GET['id']));
html_cars(intval($_GET['id']));
}
elseif ($ac == 'show') {
$ptate = intval($_GET['p_state']);
$rs = $db ->row_update('cars',array('isshow'=>$ptate),"p_id=".intval($_GET['id']));
html_cars(intval($_GET['id']));
}
elseif ($ac == 'refresh') {
$listtime = time();
$rs = $db ->row_update('cars',array('listtime'=>$listtime),"p_id=".intval($_GET['id']));
}
elseif ($ac == 'add'||$ac == 'edit') {
if (submitcheck('a')) {
$arr_not_empty = array('p_model'=>'车型不可为空');
can_not_be_empty($arr_not_empty,$_POST);
$post = post('p_brand','p_subbrand','p_subsubbrand','p_name','p_allname','p_price','p_color','p_country','p_transmission','p_year','p_month','p_details','p_model','p_hits','p_gas','p_kilometre','p_addtime','listtime','issell','isshow','isrecom','issprecom','ishot','p_username','p_tel','p_emission');
if ($settings['version'] == 3) {
$post['aid'] = intval($_POST['aid']);
$post['cid'] = intval($_POST['cid']);
}else {
$post['aid'] = 0;
$post['cid'] = 0;
}
$post['p_brand'] = intval($post['p_brand']);
$post['p_subbrand'] = intval($post['p_subbrand']);
$post['p_model'] = intval($post['p_model']);
$post['p_allname'] = "";
if(!empty($post['p_subbrand'])){
$bname = $commoncache['brandlist'][$post['p_brand']];
$subbname = arr_brandname($post['p_subbrand']);
$compareword = strstr($subbname,$bname);
if(!empty($compareword)){
$post['p_allname'] .= arr_brandname($post['p_subbrand']);
}
else{
$post['p_allname'] .= $bname ." ".arr_brandname($post['p_subbrand']);
}
}
if(!empty($post['p_subsubbrand'])){
$post['p_allname'] .= " ".arr_brandname($post['p_subsubbrand']);
}
if (empty($post['p_kilometre'])) {
$post['p_kilometre'] = 0;
}
if (empty($_POST['p_year'])) $post['p_year'] = 0;
if (empty($_POST['p_month'])) $post['p_month'] = 0;
if (empty($post['isrecom'])) $post['isrecom'] = 0;
if (empty($post['issprecom'])) $post['issprecom'] = 0;
if (empty($post['ishot'])) $post['ishot'] = 0;
if (!empty($post['p_username'])) $post['p_username'] = trim($_POST['p_username']);
if (!empty($post['p_tel'])) $post['p_tel'] = trim($_POST['p_tel']);
if (isset($_POST['p_pics'])) {
$post['p_pics'] = implode("|",$_POST['p_pics']);
if (isset($_POST['p_mainpic'])) {
$post['p_mainpic'] = $_POST['p_mainpic'];
}else {
$post['p_mainpic'] = $_POST['p_pics'][0];
}
}else {
$post['p_pics'] = "";
}
$paralist = $db ->row_select('selfdefine',"isshow=1",' id,type_name,type_value,c_name');
if ($ac == 'add') {
$post['p_hits'] = 0;
$post['p_addtime'] = time();
$post['listtime'] = time();
$post['isshow'] = 1;
$post['issell'] = 0;
$post['uid'] = -1;
$rs = $db ->row_insert('cars',$post);
$post=post('c_id','p_id','c_value');
$insertid = $db ->insert_id();
foreach($paralist as $key =>$value){
$post['c_id']=$paralist[$key]['id'];
$post['p_id']=$insertid;
$c_value='para'.$key;
if($paralist[$key]['type_name']=='checkbox'){
$checkpara = implode("|",$_POST[$c_value]);
$post['c_value'] = $checkpara;
}
else
{
$post['c_value'] = $_POST[$c_value];
}
$r = $db ->row_insert('selfdefine_value',$post);
}
html_cars($insertid);
}else {
$rs = $db ->row_update('cars',$post,"p_id=".intval($_POST['id']));
$post = post('p_id','c_value');
foreach($paralist as $key =>$value){
$post['p_id']=intval($_POST['id']);
$c_value='para'.$key;
if($paralist[$key]['type_name']=='checkbox'){
$checkpara = implode("|",$_POST[$c_value]);
$post['c_value'] = $checkpara;
}
else
{
$post['c_value'] = $_POST[$c_value];
}
$selfvalue= $db ->row_select_one('selfdefine_value',"p_id=".intval($_POST['id']).' and c_id='.$paralist[$key]['id'],'c_id,p_id');
if(empty($selfvalue['c_id'])){
$post['c_id']=$paralist[$key]['id'];
$r = $db ->row_insert('selfdefine_value',$post);
}else{
$rs = $db ->row_update('selfdefine_value',$post,"p_id=".intval($_POST['id']).' and c_id='.$paralist[$key]['id']);
}
}
html_cars(intval($_POST['id']));
}
}
else {
$configure_list = array();
if (empty($_GET['id'])) {
$data = array('p_brand'=>'','p_subbrand'=>'','p_subsubbrand'=>'','p_name'=>'','p_allname'=>'','p_keyword'=>'','p_price'=>'','p_pics'=>'','p_color'=>'','p_country'=>'','p_transmission'=>'','p_year'=>'','p_month'=>'','p_details'=>'','p_model'=>'','p_hits'=>'','p_state'=>1,'p_gas'=>'','p_kilometre'=>'','p_addtime'=>'','listtime'=>'','issell'=>'','isshow'=>'','aid'=>'','cid'=>'','p_emission'=>'');
}else {
$data = $db ->row_select_one('cars',"p_id=".intval($_GET['id']));
if (!empty($data['p_pics'])) {
$pic_list = explode('|',$data['p_pics']);
$piclist = array();
foreach($pic_list as $key =>$value) {
$piclist[$key]['pic'] = $value;
$piclist[$key]['showpic'] = WEB_PATH .$value;
$arr_picid = explode("/",$value);
$arr_length = count($arr_picid);
$arr_picids = explode(".",$arr_picid[$arr_length-1]);
$piclist[$key]['picid'] = $arr_picids[0];
}
$tpl ->assign('pic_list',$piclist);
}
}
$paralist = $db ->row_select('selfdefine',"isshow=1",' id,type_name,type_value,c_name');
foreach($paralist as $key =>$value){
if(!empty($data['p_id'])){
$para_value = $db ->row_select_one('selfdefine_value',"p_id=".$data['p_id'].' and c_id='.$value['id']);
if($value['type_name']=='select'){
$arr_para = arr_selfdefine($value['type_value']);
$para = select_make($para_value['c_value'],$arr_para,'请选择');
$paralist[$key]['select'] = $para;
}elseif($value['type_name']=='checkbox'){
$check_para = explode("|",$value['type_value']);
$checkvalue = explode("|",$para_value['c_value']);
$checkbox_str = "";
foreach($check_para as $k =>$v){
if(in_array($v,$checkvalue)){
$check = "checked";
}
else{
$check = "";
}
$checkbox_str.= "<input type='checkbox' name='para".$key."[]' value='".$v."' ".$check."> ".$v."&nbsp;&nbsp;";
}
$tpl->assign('checkbox_str',$checkbox_str);
}
else{
$paralist[$key]['c_value']=$para_value['c_value'];
}
}
else{
if($value['type_name']=='select'){
$arr_para = arr_selfdefine($value['type_value']);
$para = select_make(-1,$arr_para,'请选择');
$paralist[$key]['select'] = $para;
}elseif($value['type_name']=='checkbox'){
$check_para = explode("|",$value['type_value']);
foreach($check_para as $k =>$v){
$list[$check_para[$k]]=0;
}
$tpl->assign('list',$list);
}
}
}
$tpl->assign('paralist',$paralist);
$select_province = select_make($data['aid'],$commoncache['provincelist'],"请选择省份");
$select_city = select_make($data['cid'],$commoncache['citylist'],"请选择城市");
$tpl ->assign('selectprovince',$select_province);
$tpl ->assign('selectcity',$select_city);
$pstate_get = isset($_GET['pstate']) ?$_GET['pstate'] : "";
$page_get = isset($_GET['page']) ?$_GET['page'] : "";
$select_brand = select_make($data['p_brand'],$commoncache['markbrandlist'],'请选择品牌');
$select_subbrand = select_subbrand(intval($data['p_subbrand']));
$select_subsubbrand = select_subbrand(intval($data['p_subsubbrand']));
$select_model = select_make($data['p_model'],$commoncache['modellist'],'');
$select_year = select_make($data['p_year'],$commoncache['yearlist'],'请选择年份');
$select_month = select_make($data['p_month'],array('01'=>'01月','02'=>'02月','03'=>'03月','04'=>'04月','05'=>'05月','06'=>'06月','07'=>'07月','08'=>'08月','09'=>'09月','10'=>'10月','11'=>'11月','12'=>'12月'),'请选择月份');
$select_color = select_make($data['p_color'],$commoncache['colorlist'],'请选择颜色');
$select_gas = select_make($data['p_gas'],$commoncache['gaslist'],'请选择排量');
$select_transmission = select_make($data['p_transmission'],$commoncache['transmissionlist'],'请选择变速箱');
$select_country = select_make($data['p_country'],array('国产'=>'国产','进口'=>'进口'),'请选择');
$tpl ->assign('cars',$data);
$tpl ->assign('select_brand',$select_brand);
$tpl ->assign('select_subbrand',$select_subbrand);
$tpl ->assign('select_subsubbrand',$select_subsubbrand);
$tpl ->assign('select_model',$select_model);
$tpl ->assign('select_color',$select_color);
$tpl ->assign('select_year',$select_year);
$tpl ->assign('select_month',$select_month);
$tpl ->assign('select_gas',$select_gas);
$tpl ->assign('select_transmission',$select_transmission);
$tpl ->assign('select_country',$select_country);
$tpl ->assign('sessionid',session_id());
$tpl ->display('admin/add_cars.html');
exit;
}
}
elseif ($ac == 'html') {
$p_id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('缺少ID',-1);
$rs = html_cars($p_id);
}
elseif ($ac == 'bulkhtml') {
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach($_POST['bulkid'] as $value) {
$rs = html_cars($value);
}
}
else {
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac] .($rs ?'成功': '失败'),ADMIN_PAGE ."?m=$m&a=list&page=".$page_g);

?>