<?php

function search_cars($status='',$page = '',$brand='',$subbrand='',$price='',$age='',$kilometre='',$transmission='',$gas='',$color='',$source='',$class='',$picture='',$aid='',$cid='',$uid='',$keywords='')
{
global $db;
$orderby='';
$where = "isshow=1";
if (!empty($status)) {
switch($status) {
case 1:
$orderby .= "listtime desc";
break;
case 2:
$orderby = "  p_price asc";
break;
case 3:
$orderby = "  p_price desc";
break;
case 4:
$orderby = "  p_kilometre desc ";
break;
case 5:
$orderby= "  p_year desc, p_month desc";
break;
}
}
if(!empty($brand)) $where .= " and p_brand=".$brand;
if(!empty($keywords)) $where .= " and p_keywords= like '%".$keywords ."%' ";
if(!empty($subbrand)) $where .= " and p_subbrand=".$subbrand;
if(!empty($subbrand)) $where .= " and uid=".$uid;
if(!empty($subbrand)) $where .= " and cid=".$cid;
if(!empty($subbrand)) $where .= " and aid=".$aid;
if(!empty($class)) $where .= " and p_model=".$class;
if(!empty($age)){
$compareyear01 = date("Y");
switch($age) {
case 0:
$where .= "";
break;
case 1:
$where .= " and  (".$compareyear01."-p_year)<1 ";
break;
case 2:
$where .= " and (".$compareyear01."-p_year)>=1 and (".$compareyear01."-p_year)<3 ";
break;
case 3:
$where .= " and (".$compareyear01."-p_year)>=3 and (".$compareyear01."-p_year)<5 ";
break;
case 4:
$where .= " and  (".$compareyear01."-p_year)>=5 and (".$compareyear01."-p_year)<8 ";
break;
case 5:
$where .= " and  (".$compareyear01."-p_year)>=8 and (".$compareyear01."-p_year)<10 ";
break;
case 6:
$where .= " and  (".$compareyear01."-p_year)>=10 ";
break;
}
}
if(!empty($kilometre)){
switch($kilometre) {
case 0:
$where .= "";
break;
case 1:
$where .= " and p_kilometre<1 ";
break;
case 2:
$where .= " and  p_kilometre<3";
break;
case 3:
$where .= " and  p_kilometre<5 ";
break;
case 4:
$where .= " and  p_kilometre<10";
break;
case 5:
$where .= " and  p_year>10";
break;
}
}
if(!empty($price)){
switch($price) {
case 0:
$where .= "";
break;
case 1:
$where .= " and  p_price<3 ";
break;
case 2:
$where .= " and p_price>=3 and p_price<5";
break;
case 3:
$where .= " and  p_price>=5 and p_price<8 ";
break;
case 4:
$where .= " and p_price>=8 and p_price<12";
break;
case 5:
$where .= " and p_price>=12 and p_price<18";
break;
case 6:
$where .= " and p_price>=18 and p_price<24";
break;
case 7:
$where .= " and p_price>=24 and p_price<35";
break;
case 8:
$where .= " and p_price>=35 and p_price<60";
break;
case 9:
$where .= " and p_price>=60 and p_price<100";
break;
case 10:
$where .= " and p_price>100";
break;
}
}
if(!empty($transmission)){
switch($transmission) {
case 0:
$where .= "";
break;
case 1:
$where .= " and p_transmission='手动' ";
break;
case 2:
$where .= " and p_transmission='自动' ";
break;
case 3:
$where .= " and  p_transmission='手自一体'  ";
break;
case 4:
$where .= "  and p_transmission='无纺变速' ";
break;
case 5:
$where .= "  and p_transmission='双离合' ";
break;
}
}
if(!empty($gas)){
switch($gas) {
case 0:
$where .= "";
break;
case 1:
$where .= " and p_gas<1.0 ";
break;
case 2:
$where .= " and p_gas>=1.0 and p_gas<1.6 ";
break;
case 3:
$where .= "  and p_gas>=1.6 and p_gas<2.0  ";
break;
case 4:
$where .= " and p_gas>=2.0 and p_gas<3.0 ";
break;
case 5:
$where .= " and p_gas>3.0 ";
break;
}
}
if(!empty($color)){
switch($color) {
case 0:
$where .= "";
break;
case 1:
$where .= " and p_color='黑色' ";
break;
case 2:
$where .= " and p_color='银灰色' ";
break;
case 3:
$where .= " and p_color='白色'  ";
break;
case 4:
$where .= " and p_color='红色' ";
break;
case 5:
$where .= " and p_color='蓝色' ";
break;
case 6:
$where .= " and p_color='深灰色' ";
break;
case 7:
$where .= " and p_color='香槟色' ";
break;
case 8:
$where .= " and p_color='绿色' ";
break;
case 9:
$where .= " and p_color='黄色' ";
break;
case 10:
$where .= " and p_color='橙色' ";
break;
case 11:
$where .= "  and p_color='咖啡色' ";
break;
case 12:
$where .= " and p_color='紫色' ";
break;
case 13:
$where .= " and p_color='多彩色' ";
break;
}
}
if(!empty($source)){
switch($source) {
case 0:
$where .= "";
break;
case 1:
$puser = $db ->row_select_one('member','isshop=0','id');
$where .= " and  uid= ".$puser['id'];
break;
case 2:
$suser = $db ->row_select_one('member','isshop=1','id');
$where .= " and  uid= ".$suser['id'] ;
break;
}
}
if(!empty($picture)){
switch($picture) {
case 0:
$where .= "";
break;
case 1:
$where .= " and  p_pics is not null ";
break;
}
}
$page = !empty($page) ?$page : 1;
include(INC_DIR .'Page.class.php');
$Page = new Page($db ->tb_prefix .'cars',$where,'uid,p_id,p_allname,p_price,p_color,p_gas,p_kilometre,p_year,p_month,p_mainpic,listtime','30',$orderby);
$listnum = $Page ->total_num;
$list = $Page ->get_data();
foreach($list as $key =>$value) {
$list[$key]['id'] = $value['p_id'];
$list[$key]['carname'] = $value['p_allname'];
$list[$key]['price'] = $value['p_price'];
$list[$key]['color'] = $value['p_color'];
$list[$key]['gas'] = $value['p_gas'];
$list[$key]['kilometre'] = $value['p_kilometre'];
$list[$key]['regdate'] = $value['p_year']."年".$value['p_month'].'月';
$list[$key]['mainpic'] = $value['p_mainpic'];
$list[$key]['listtime'] = date('Y-m-d',$value['listtime']);
if(!empty($value['cid'])){
$area = $db ->row_select_one('area',"id=".$value['cid'],'name');
$list[$key]['city'] = $area['name'];
}
else{
$list[$key]['city'] = "";
}
if (!empty($value['uid'])) {
$user = $db ->row_select_one('member','id='.$value['uid'],'isshop');
if($user['isshop']==1){
$list[$key]['usertype'] = "商家";
}
else{
$list[$key]['usertype'] = "个人";
}
}
else{
$list[$key]['usertype'] = "个人";
}
unset ($list[$key]['p_id']);
unset ($list[$key]['p_allname']);
unset ($list[$key]['p_price']);
unset ($list[$key]['p_color']);
unset ($list[$key]['p_gas']);
unset ($list[$key]['p_kilometre']);
unset ($list[$key]['p_year']);
unset ($list[$key]['p_month']);
unset ($list[$key]['p_mainpic']);
unset ($list[$key]['cid']);
}
$page_arr = $Page ->api_page();
$carlist = array('page'=>$page_arr,'list'=>$list);
return json_encode($carlist);
}
function search_cars_detail($id)
{
global $db;
$fzz = new fzz_cache;
if( !($fzz->_isset( "common_cache")) ){
$fzz->set("common_cache",display_common_cache(),CACHETIME);
}
$commoncache = $fzz->get("common_cache");
$array_city = $commoncache['citylist'];
$array_model = $commoncache['modellist'];
$array_brand = $commoncache['brandlist'];
$array_subbrand = $commoncache['subbrandlist'];
$data = $db ->row_select_one('cars',"p_id=".$id,'p_id,cid,uid,p_model,p_allname,p_price,p_kilometre,p_color,p_country,p_transmission,p_gas,listtime,p_mainpic,p_pics,p_details,p_year,p_month,listtime,p_username,p_tel,p_exatime,p_securedate,p_tax,p_productiontime');
if($data){
$data['id'] = $data['p_id'];
$data['carname'] = $data['p_allname'];
$data['model'] = $array_model[$data['p_model']];
if(!empty($data['cid'])){
$data['city'] = $array_city[$data['cid']];
}
else{
$data['city'] = "";
}
$data['price'] = $data['p_price'];
$data['color'] = $data['p_color'];
$data['transmission'] = $data['p_transmission'];
$data['kilometre'] = $data['p_kilometre'];
$data['gas'] = $data['p_gas'];
$data['country'] = $data['p_country'];
$data['listtime'] = date('Y-m-d',$data['listtime']);
$data['details'] = $data['p_details'];
$data['regdate'] = $data['p_year']."年".$data['p_month'].'月';
$data['mainpic'] = $data['p_mainpic'];
$data['pics'] = $data['p_pics'];
$data['exatime'] = $data['p_exatime'];
$data['securedate'] = $data['p_securedate'];
$data['taxdate'] = $data['p_tax'];
$data['productiontime'] = $data['p_productiontime'];
if($data['uid']==0){
$data['username'] = $data['p_username'];
$data['tel'] = $data['p_tel'];
$data['usertype'] = "个人";
}
else{
$user = $db ->row_select_one('member',"id=".$data['uid'],'mobilephone,nicname,isshop');
$data['username'] = $user['nicname'];
$data['tel'] = $user['mobilephone'];
if($user['isshop']==1){
$data['usertype'] = "商家";
}
else{
$data['usertype'] = "个人";
}
}
unset($data['p_id']);
unset($data['cid']);
unset($data['uid']);
unset($data['p_allname']);
unset($data['p_model']);
unset($data['p_brand']);
unset($data['p_subbrand']);
unset($data['p_price']);
unset($data['p_transmission']);
unset($data['p_gas']);
unset($data['p_country']);
unset($data['p_mainpic']);
unset($data['p_pics']);
unset($data['p_kilometre']);
unset($data['p_details']);
unset($data['p_year']);
unset($data['p_month']);
unset($data['p_color']);
unset($data['p_username']);
unset($data['p_tel']);
unset($data['p_exatime']);
unset($data['p_securedate']);
unset($data['p_tax']);
unset($data['p_productiontime']);
}
return json_encode($data);
}
function select_brands(){
global $db;
$list = $db ->row_select('brand',"b_parent=-1",'b_id,b_name,mark,pic',0,'mark asc');
foreach($list as $key =>$value) {
$list[$key+1]['b_id'] = $value['b_id'];
$list[$key+1]['b_name'] = trim($value['b_name']);
$list[$key+1]['mark'] = $value['mark'];
$list[$key+1]['pic'] = $value['pic'];
if(!empty($value['pic'])){
$list[$key+1]['icon'] = "http://img2.cheyuan.com/common/".$value['pic'];
}
else{
$list[$key+1]['icon'] = "";
}
$sublist = $db ->row_select('brand',"b_parent=".$value['b_id'],'b_id,b_name',0,'orderid asc');
foreach($sublist as $k =>$v) {
$sublist[$k]['b_name'] = trim($v['b_name']);
}
$list[$key+1]['subbrand'] = $sublist;
}
$list[0]['b_name']="不限品牌";
$list[0]['b_id'] = 0;
$list[0]['mark'] = "B";
$list[0]['pic'] = "";
$list[0]['icon'] = "";
$list[0]['subbrand'] = array();
return json_encode($list);
}
function select_price(){
$price_arr = array('0'=>'不限','1'=>'3万以下','2'=>'3-5万','3'=>'5-8万','4'=>'8-12万','5'=>'12-18万','6'=>'18-24万','7'=>'24-35万','8'=>'35-60万','9'=>'60-100万','10'=>'100万以上');
return json_encode($price_arr);
}
function select_age(){
$age_arr = array('0'=>'不限','1'=>'1年以内','2'=>'1-3年','3'=>'3-5年','4'=>'5-8年','5'=>'8-10年','6'=>'10年以上');
return json_encode($age_arr);
}
function select_kilometre(){
$kilometre_arr = array('0'=>'不限','1'=>'1万公里以下','2'=>'3万公里以下','3'=>'5万公里以下','4'=>'10万公里以下','5'=>'10万公里以上');
return json_encode($kilometre_arr);
}
function select_transmission(){
$transmission_arr = array('0'=>'不限','1'=>'手动','2'=>'自动','3'=>'手自一体','4'=>'无极变速','5'=>'双离合');
return json_encode($transmission_arr);
}
function select_gas(){
$gas_arr = array('0'=>'不限','1'=>'1.0以下','2'=>'1.0-1.6','3'=>'1.6-2.0','4'=>'2.0-3.0','5'=>'3.0以上');
return json_encode($gas_arr);
}
function select_color(){
$color_arr = array('0'=>'黑色','1'=>'银灰色','2'=>'白色','3'=>'红色','4'=>'蓝色','5'=>'深灰色','6'=>'香槟色','7'=>'绿色','8'=>'黄色','9'=>'橙色','10'=>'咖啡色','11'=>'紫色','12'=>'多彩色');
return json_encode($color_arr);
}
function select_source(){
$source_arr = array('0'=>'不限','1'=>'个人','2'=>'商家');
return json_encode($source_arr);
}
function select_class(){
global $db;
$list = $db ->row_select('model',"1=1",'s_id,s_name',0,'orderid asc');
return json_encode($list);
}
function select_picture(){
$picture_arr = array('0'=>'不限','1'=>'只看有图');
return json_encode($picture_arr);
}
function select_area()
{
global $db;
$list = $db ->row_select('area',"c_parent=-1",'c_id,c_name',0,'orderid');
return json_encode($list);
}
function submit_assess($uid,$brand,$subbrand,$kilometre){
global $db;
$post['p_brand'] =$brand;
$post['p_subbrand'] = $subbrand;
$post['p_kilometre'] = $kilometre;
$rs = $db ->row_insert('assesscars',$post);
if(!$rs) $s = array('succ'=>'false','msg'=>'失败');
else $s = array('succ'=>'true','msg'=>'成功');
return json_encode($s);
}
function submit_cars($uid,$brand,$subbrand,$carname,$year,$month,$color,$details,$price,$transmission,$kilometre,$pics,$model,$tel){
global $db;
$fzz = new fzz_cache;
if( !($fzz->_isset( "common_cache")) ){
$fzz->set("common_cache",display_common_cache(),CACHETIME);
}
$commoncache = $fzz->get("common_cache");
$array_model = $commoncache['modellist'];
$array_brand = $commoncache['brandlist'];
$array_brand_keyword = $commoncache['brandkeyword'];
$array_subbrand = $commoncache['subbrandlist'];
$array_subbrand_keyword = $commoncache['subbrandkeyword'];
$post['uid'] = $uid;
$user = $db ->row_select_one('member','id='.$uid);
$post['p_tel']=$tel;
$post['p_brand'] = $brand;
$post['p_subbrand'] = $subbrand;
$post['p_name'] = $carname;
$post['p_allname'] = $array_brand[$brand] .$array_subbrand[$subbrand].$carname;
$post['p_keyword'] = $array_brand_keyword[$brand] .$array_subbrand_keyword[$subbrand].$carname;
$post['p_model']=  $array_model[$model];
$post['p_details'] = $details;
$post['p_price'] = $price;
$post['p_year'] = $year;
$post['p_month'] = $month;
$post['p_color'] = $color;
$post['p_transmission'] = $transmission;
$post['p_kilometre'] = $kilometre;
$post['p_pics'] = $pics;
$post['p_type'] = 0;
$post['p_addtime'] = $post['listtime'] = time();
$rs = $db->row_insert('cars',$post);
if(!$rs) $s = array('succ'=>'false','msg'=>'失败');
else $s = array('succ'=>'true','msg'=>'成功');
return json_encode($s);
}
function select_consult($uid){
global $db;
$list = $db ->row_select('assess'," uid=  ".$uid);
return json_encode($list);
}
function select_appraiser(){
global $db;
$orderby='l_orderid';
$where=' isshow=1 ';
$page = !empty($page) ?$page : 1;
include(INC_DIR .'Page.class.php');
$Page = new Page($db ->tb_prefix .'appraiser',$where,'l_id,on_name,word,telephone,isshow,l_orderid,pic_url','30',$orderby);
$listnum = $Page ->total_num;
$list = $Page ->get_data();
foreach($list as $key =>$value) {
$list[$key]['id'] = $value['l_id'];
$list[$key]['onname'] = $value['on_name'];
$list[$key]['word'] = $value['word'];
$list[$key]['telephone'] = $value['telephone'];
$list[$key]['isshow'] = $value['is_show'];
$list[$key]['orderid'] = $value['l_orderid'];
$list[$key]['pic'] = $value['pic_url'];
unset ($list[$key]['l_id']);
unset ($list[$key]['on_name']);
unset ($list[$key]['word']);
unset ($list[$key]['telephone']);
unset ($list[$key]['is_show']);
unset ($list[$key]['l_orderid']);
}
$page_arr = $Page ->api_page();
$appraiserlist = array('page'=>$page_arr,'list'=>$list);
return json_encode($appraiserlist);
}
function select_compcar($ids){
global $db;
$list = $db ->row_select('cars',"p_id in".'('.$ids.')');
return json_encode($list);
}
function submit_upload(){
global $db;
if(!empty($_FILES['upload']['name'])){
$newname = time();
$post['p_pic'] = upload_pic($newname,'',1);
}
}

?>