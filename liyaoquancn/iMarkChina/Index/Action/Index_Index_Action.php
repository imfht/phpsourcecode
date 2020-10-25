<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/ 
include_once 'Index_Code_Action.php';
$Mark_PageNum_Action = $Mark_Config_Action['site_mumber'];
$Mark_Url_Action = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
if (preg_match('|^post/([a-z0-5]{6})$|', $Mark_Url_Action, $Mark_Matche_Action)) {
    $Mark_Get_Type_Action = 'post';
    $Mark_Get_Name_Action = $Mark_Matche_Action[1];
} elseif (preg_match('|^tag/([^/]+)/(\?page=([0-9]+)){0,1}$|', $Mark_Url_Action, $Mark_Matche_Action)) {
    $Mark_Get_Type_Action = 'tag';
    $Mark_Get_Name_Action = urldecode($Mark_Matche_Action[1]);
    $Mark_Page_Num_Action = isset($Mark_Matche_Action[2]) ? $Mark_Matche_Action[3] : 1;
} elseif (preg_match('|^date/([0-9]{4}-[0-9]{2})/(\?page=([0-9]+)){0,1}$|', $Mark_Url_Action, $Mark_Matche_Action)) {
    $Mark_Get_Type_Action = 'date';
    $Mark_Get_Name_Action = urldecode($Mark_Matche_Action[1]);
    $Mark_Page_Num_Action = isset($Mark_Matche_Action[2]) ? $Mark_Matche_Action[3] : 1;
} elseif (preg_match('|^archive/$|', $Mark_Url_Action, $Mark_Matche_Action)) {
    $Mark_Get_Type_Action = 'archive';
}elseif (preg_match('|^search/$|', $Mark_Url_Action, $Mark_Matche_Action)){
    $Mark_Get_Type_Action = 'search';
}elseif ($Mark_Url_Action == 'rss/') {
    $Mark_Get_Type_Action = 'rss';
    $Mark_Get_Name_Action = '';
    $Mark_Page_Num_Action = isset($_GET['page']) ? $_GET['page'] : 1;
} elseif (preg_match('|^(([-a-zA-Z0-9]+/)+)$|', $Mark_Url_Action, $Mark_Matche_Action)) {
    $Mark_Get_Type_Action = 'page';
    $Mark_Get_Name_Action = substr($Mark_Matche_Action[1], 0, -1);
} else {
    $Mark_Get_Type_Action = 'index';
    $Mark_Get_Name_Action = '';
    $Mark_Page_Num_Action = isset($_GET['page']) ? $_GET['page'] : 1;
}
if ($Mark_Get_Type_Action == 'post') {
    include __Index__.'/Index/Point/Data/Post/Index/publish.php';
    if (array_key_exists($Mark_Get_Name_Action, $Mark_Posts_Action)) {
        $Mark_Post_Id_Action = $Mark_Get_Name_Action;
        $Mark_Post_Action = $Mark_Posts_Action[$Mark_Post_Id_Action];
        $Mark_Data_Action = unserialize(file_get_contents(__Index__.'/Index/Point/Data/Post/Data/' . $Mark_Post_Id_Action . '.Mark'));
    } else {
        Mark_404();
    }
} elseif ($Mark_Get_Type_Action == 'tag') {
    include __Index__.'/Index/Point/Data/Post/Index/publish.php';
    $Mark_Post_Ids_Action = array_keys($Mark_Posts_Action);
    $Mark_Post_Coun_Action = count($Mark_Post_Ids_Action);
    $Mark_Tag_Post_Action = array();
    for ($i = 0; $i < $Mark_Post_Coun_Action; $i++) {
        $id = $Mark_Post_Ids_Action[$i];
        $post = $Mark_Posts_Action[$id];
        if (in_array($Mark_Get_Name_Action, $post['tags'])) {
            $Mark_Tag_Post_Action[$id] = $post;
        }
    }
    $Mark_Posts_Action = $Mark_Tag_Post_Action;
    $Mark_Post_Ids_Action = array_keys($Mark_Posts_Action);
    $Mark_Post_Coun_Action = count($Mark_Post_Ids_Action);
} elseif ($Mark_Get_Type_Action == 'date') {
    include __Index__.'/Index/Point/Data/Post/Index/publish.php';
    $Mark_Post_Ids_Action = array_keys($Mark_Posts_Action);
    $Mark_Post_Coun_Action = count($Mark_Post_Ids_Action);
    $Mark_Data_Post = array();
    for ($M = 0; $M < $Mark_Post_Coun_Action; $M++) {
        $id = $Mark_Post_Ids_Action[$M];
        $post = $Mark_Posts_Action[$id];
        if (strpos($post['date'], $Mark_Get_Name_Action) === 0) {
            $Mark_Data_Post[$id] = $post;
        }
    }
    $Mark_Posts_Action = $Mark_Data_Post;
    $Mark_Post_Ids_Action = array_keys($Mark_Posts_Action);
    $Mark_Post_Coun_Action = count($Mark_Post_Ids_Action);
} elseif ($Mark_Get_Type_Action == 'archive') {
    include __Index__.'/Index/Point/Data/Post/Index/publish.php';
    $Mark_Post_Ids_Action = array_keys($Mark_Posts_Action);
    $Mark_Post_Coun_Action = count($Mark_Post_Ids_Action);
    $tags_array = array();
    $date_array = array();
    for ($i = 0; $i < $Mark_Post_Coun_Action; $i++) {
        $post_id = $Mark_Post_Ids_Action[$i];
        $post = $Mark_Posts_Action[$post_id];
        $date_array[] = substr($post['date'], 0, 7);
        $tags_array = array_merge($tags_array, (array)$post['tags']);
    }
    $Mark_Tag_Action = array_values(array_unique($tags_array));
    $Mark_Dates_Action = array_values(array_unique($date_array));
} elseif ($Mark_Get_Type_Action == 'page') {
    include __Index__.'/Index/Point/Data/Page/Index/publish.php';
    if (array_key_exists($Mark_Get_Name_Action, $Mark_Pages_Action)) {
        $Mark_Post_Id_Action = $Mark_Get_Name_Action;
        $Mark_Post_Action = $Mark_Pages_Action[$Mark_Post_Id_Action];
        $Mark_Data_Action = unserialize(file_get_contents(__Index__.'/Index/Point/Data/Page/Data/' . $Mark_Post_Action['file'] . '.Mark'));
    }else {
        Mark_404();
    }
} else {
    @include __Index__.'/Index/Point/Data/Post/Index/publish.php';
    @$Mark_Post_Ids_Action = array_keys($Mark_Posts_Action);
    $Mark_Post_Coun_Action = count($Mark_Post_Ids_Action);
}
if ($Mark_Get_Type_Action != 'rss'){ 
    global  $Mark_Config_Action;
    $Mark_style = $Mark_Config_Action['style'];
    @include __Index__.'/Index/Theme/'.$Mark_style.'/index.php';
}else {
    include __Index__.'/Index/Action/Index_Rss_Action.php';
}
?>