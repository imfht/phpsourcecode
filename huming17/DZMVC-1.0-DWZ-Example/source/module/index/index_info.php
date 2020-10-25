<?php
$id = isset($_GET['id']) ? $_GET['id'] : '';
if($id){
    $sql_info = "SELECT * FROM ".DB::table('content')." WHERE info_id='".$id."' LIMIT 1";
    $sql_info_result = DB::fetch_first($sql_info);
}
include template('index/index_info');
?>