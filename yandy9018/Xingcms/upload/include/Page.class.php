<?php

class Page {
var $total_num;
var $page_size = 10;
var $total_page;
var $page = 1;
var $data;
function Page($tbname,$where = '1=1',$field = '*',$page_size = 20,$order_by = '',$group_by = '') {
!mysql_ping() &&exit('mysql can not connect!');
if (!empty($page_size)) $this ->page_size = $page_size;
$sql = "SELECT count(*) as row_num FROM $tbname WHERE $where";
$row_num = mysql_fetch_array(mysql_query($sql));
$this ->total_num = $row_num['row_num'];
$page = isset($_GET['page']) &&intval($_GET['page']) >0 ?intval($_GET['page']) : 1;
$this ->page = ($page <$this ->total_page ||$this ->total_page == 0) ?$page : $this ->total_page;
$start = ($this ->page -1) * $page_size;
if($page_size==0){
$sql = "SELECT $field FROM $tbname WHERE $where".($group_by ?' GROUP BY '.$group_by : '').($order_by ?' ORDER BY '.$order_by : '') ;
}else{
$this ->total_page = ceil($this ->total_num / $page_size);
$sql = "SELECT $field FROM $tbname WHERE $where".($group_by ?' GROUP BY '.$group_by : '').($order_by ?' ORDER BY '.$order_by : '') ." LIMIT $start,$this->page_size";
}
$result = mysql_query($sql);
$data = array();
while ($row = mysql_fetch_assoc($result)) {
$data[] = $row;
}
$this ->data = $data;
}
function get_data() {
return $this ->data;
}
function get_url() {
$arr_url = parse_url($_SERVER['REQUEST_URI']);
if (!isset($arr_url['query'])) $arr_url['query'] = '';
parse_str($arr_url['query'],$arr_get);
if (isset($arr_get['page'])) unset($arr_get['page']);
$str_url = '';
foreach ($arr_get as $k =>$v) {
$str_url .= $k .'='.$v .'&';
}
return $arr_url['path'] .'?'.substr($str_url,0,-1) .'&page=';
}
function button_select() {
$str = "<select onchange=\"location.href='".$this ->get_url() ."'+this.value\">";
for ($i = 1;$i <= $this ->total_page;$i++) {
$selected = ($this ->page == $i) ?'selected': '';
$str .= "<option value=$i $selected>$i</option>";
}
return $str .= '</select>';
}
function button_basic($total_num = 1,$current_page = 1,$first_and_last = 1) {
$url = $this ->get_url();
$str = '';
$str .= ($total_num ?'<span>共'.$this ->total_num .'条</span>': '');
$str .= "<span>第".($current_page ?$this ->page .'/'.$this ->total_page .'页</span>': '');
$str .= ($first_and_last ?($this ->total_page >1 ?"<a href='{$url}1'>首页</a>": '') : '');
$str .= ($this ->page >1 ?"<a href='$url".($this ->page-1) ."'>上一页</a>": '');
$str .= ($this ->page +1 <= $this ->total_page ?"<a href='$url".($this ->page +1) ."'>下一页</a>": '');
$str .= ($first_and_last ?($total_num >1 ?"<a href='{$url}{$this->total_page}'>尾页</a>": "<a href='{$url}{$this->total_page}'>尾页</a>") ."": '');
return $str;
}
function page_replace($page,$url) {
return str_replace("{page}",$page,$url);
}
function button_basic_html($url,$total_num = 1,$current_page = 1,$first_and_last = 1) {
$str = "";
$str .= "<span>".($total_num ?'共'.$this ->total_num .'条': '') ."</span>";
$str .= "<span>".($current_page ?$this ->page .'/'.$this ->total_page .'页': '') ."</span>";
$str .= ($first_and_last ?($this ->total_page >1 ?"<a href='".$this ->page_replace(1,$url) ."'>首页</a>": '<a>首页</a>') ."": '');
for ($i = 1;$i <= $this ->total_page;$i++) {
$selected = ($this ->page == $i) ?'selected': '';
$str .= "<a href='".$this ->page_replace($i,$url) ."'\" class=$selected>$i</a>";
}
$str .= ($first_and_last ?($total_num >1 ?"<a href='".$this ->page_replace($this ->total_page,$url) ."'>尾页</a>": "<a href='".$this ->page_replace($this ->total_page,$url) ."'>尾页</a>") ."": '');
return $str;
}
function button_basic_num($total_num=1,$current_page=1,$first_and_last=1)
{
$url = $this->get_url();
$str = "";
$str .= "<span>".($total_num ?'共'.$this->total_num.'条': '')."</span>";
$str .= ($first_and_last ?($this->total_page >1 ?"<a href='".$url."1'>首页</a>": '<a>首页</a>')."": '');
if($this->total_page -$this->page >= 10){
$allpage = $this->page+10;
}
else{
$allpage = $this->total_page;
}
for ($i = ($this->page>5?($this->page-5):1);$i <= (($this->page+5)>=$allpage?$allpage:($this->page+5));$i++)
{
$selected = ($this->page == $i) ?'selected': '';
$str .= "<a href='".$url.$i."' class=$selected>$i</a>";
}
$str .= ($first_and_last ?($total_num >1 ?"<a href='".$url."$this->total_page'>尾页</a>": "<a href='".$url.$this->total_page."'>尾页</a>")."": '');
return $str;
}
function api_page() {
$listpage = array('currentPage'=>$this ->page,'TotalCount'=>$this ->total_num,'pageSize'=>$this ->page_size,);
return $listpage;
}
function mobilephone_button_basic($total_num = 1,$current_page = 1,$first_and_last = 1) {
$url = $this ->get_url();
$str = "";
$str .= ($this ->page >1 ?"<a href='$url".($this ->page-1) ."'>上一页</a>": "<a href='javascript:void(0);'>上一页</a>");
$str .= "<span>".($current_page ?$this ->page .'/'.$this ->total_page : '');
$str .= " <select onchange=\"location.href='".$this ->get_url() ."'+this.value\" style='border:none'>";
for ($i = 1;$i <= $this ->total_page;$i++) {
$selected = ($this ->page == $i) ?'selected': '';
$str .= "<option value=$i $selected>$i</option>";
}
$str .= '</select></span>';
$str .= ($this ->page +1 <= $this ->total_page ?"<a href='$url".($this ->page +1) ."'>下一页</a>": "<a href='javascript:void(0);'>下一页</a>");
return $str;
}
}

?>