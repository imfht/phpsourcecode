<?php

//DEBUG 请求获取一页列表数据例子
$page = max(1, intval($_GET['page']));
$perpage = $limit = 10;
$start=(($page-1) * $perpage);

$cate_id = isset($_GET['id']) ? $_GET['id'] : '';
if($cate_id){
    $sql_info = "SELECT * FROM ".DB::table('content')." WHERE info_cateid='".$cate_id."' AND isdelete=0 ORDER BY info_id DESC ".DB::limit($start, $limit);
    $sql_info_result = DB::fetch_all($sql_info);
    $sql_total_rows = "SELECT count(*) FROM ".DB::table('content')." WHERE info_cateid='".$cate_id."' AND isdelete=0 ";
    $sql_total_rows_result = DB::result_first($sql_total_rows);
    $multipage = multi($sql_total_rows_result, $perpage, $page, "index.php?mod=index&action=cate&id=".$cate_id);

    //DEBUG 获取TITLE名称
    $cate_title = get_title_by_info_cateid($cate_id);
}

include template('index/index_cate');