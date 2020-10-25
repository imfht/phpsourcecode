<?php

//DEBUG 请求获取一页列表数据例子
$page = max(1, intval($_GET['page']));
$perpage = $limit = 6;
$start=(($page-1) * $perpage);
$cate_id = isset($_GET['id']) ? $_GET['id'] : '';

$wheresql = '';
if($cate_id){
    $wheresql = " AND info_cateid='".$cate_id." ";
    //DEBUG 获取TITLE名称
    $cate_title = get_title_by_info_cateid($cate_id);
}

$sql_info = "SELECT * FROM ".DB::table('content')." WHERE isdelete=0 ".$wheresql." ORDER BY info_id DESC ".DB::limit($start, $limit);
$sql_info_result = DB::fetch_all($sql_info);
//$sql_total_rows = "SELECT count(*) FROM ".DB::table('content')." WHERE isdelete=0 ".$wheresql."";
//$sql_total_rows_result = DB::result_first($sql_total_rows);
//$multipage = multi($sql_total_rows_result, $perpage, $page, "index.php?mod=index&action=cate&id=".$cate_id);
foreach($sql_info_result AS $key => $value){
    $sql_info_result[$key]['content_desc'] = cutstr(strip_tags($value['content']), 24, '...');
    if($value['create_dateline']){
        $sql_info_result[$key]['create_dateline_format'] = date('Y-m-d',$value['create_dateline']);
    }else{
        $sql_info_result[$key]['create_dateline_format'] = '';
    }
}

//DEBUG 如果存在焦点设置信息,则取出前6个焦点信息
$sql_info_fouse = "SELECT * FROM ".DB::table('content')." WHERE isdelete = 0 AND isfrontpage = 1 ".$wheresql." ORDER BY frontpage_order ASC ".DB::limit($start, $limit);
$sql_info_fouse_result = DB::fetch_all($sql_info_fouse);
if($sql_info_fouse_result){
	
}
include template('index/index');
?>