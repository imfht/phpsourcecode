<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
session_start();
include 'Root_Hackdone_Action.php';
if (!is_dir(FileLink. '/Index/Point/Data/Post/Data/')) mkdir(FileLink.'/Index/Point/Data/Post/Data/', 0777);
function load_posts(){ 
    global $state, $index_file, $Mark_Posts_Action;
    if (isset($_GET['state'])) {
        if ($_GET['state'] == 'draft') {
            $state = 'draft';
            $index_file = FileLink. '/Index/Point/Data/Post/Index/draft.php';
        } else
            if ($_GET['state'] == 'delete') {
                $state = 'delete';
                $index_file = FileLink. '/Index/Point/Data/Post/Index/delete.php';
            } else {
                $state = 'publish';
                $index_file = FileLink. '/Index/Point/Data/Post/Index/publish.php';
            }
    } else {
        $state = 'publish';
        $index_file = FileLink.'/Index/Point/Data/Post/Index/publish.php';
    }
    include $index_file;
}
function delete_post($id){
    global $state, $index_file, $Mark_Posts_Action;
    $post = $Mark_Posts_Action[$id];
    $post['prev_state'] = $state;
    unset($Mark_Posts_Action[$id]);
    file_put_contents($index_file, "<?php\n\$Mark_Posts_Action=" . var_export($Mark_Posts_Action, true) .
        "\n?>");
    if ($state != 'delete') {
        $index_file2 = FileLink.'/Index/Point/Data/Post/Index/delete.php';
        include $index_file2;
        $Mark_Posts_Action[$id] = $post;
        file_put_contents($index_file2, "<?php\n\$Mark_Posts_Action=" . var_export($Mark_Posts_Action, true) .
            "\n?>");
    } else {
        unlink(FileLink.'/Index/Point/Data/Post/Data/' . $id . '.Mark');
    }
}
function revert_post($id){
    global $state, $index_file, $Mark_Posts_Action;
    $post = $Mark_Posts_Action[$id];
    $prev_state = $post['prev_state'];
    unset($post['prev_state']);
    unset($Mark_Posts_Action[$id]);
    file_put_contents($index_file, "<?php\n\$Mark_Posts_Action=" . var_export($Mark_Posts_Action, true) ."\n?>");
    $index_file2 = FileLink.'/Index/Point/Data/Post/Index/' . $prev_state .'.php';
    include $index_file2;
    $Mark_Posts_Action[$id] = $post;
    uasort($Mark_Posts_Action, "post_sort");
    file_put_contents($index_file2, "<?php\n\$Mark_Posts_Action=" . var_export($Mark_Posts_Action, true) ."\n?>");
}
load_posts();
if (isset($_GET['delete']) || (isset($_GET['apply']) && $_GET['apply'] =='delete')) {
    if (isset($_GET['apply']) && $_GET['apply'] == 'delete') {
        $ids = explode(',', $_GET['ids']);
        foreach ($ids as $id) {
            if (trim($id) == '')
                continue;
            delete_post($id);
            load_posts();
        }
    } else {
        delete_post($_GET['delete']);
    }
    Header('Location:Post.php?done=true&state=' . $state);
    exit();
}
if (isset($_GET['revert']) || (isset($_GET['apply']) && $_GET['apply'] =='revert')) {
    if (isset($_GET['apply']) && $_GET['apply'] == 'revert') {
        $ids = explode(',', $_GET['ids']);
        foreach ($ids as $id) {
            if (trim($id) == '')
                continue;
            revert_post($id);
            load_posts();
        }
    } else {
        revert_post($_GET['revert']);
    }
    Header('Location:Post.php?done=true&state=' . $state);
    exit();
}
if (isset($_GET['done'])) {
    $message = "<script language=javascript>alert('操作成功！');window.location='".$Mark_Config_Action['site_link'].$Mark_Config_Action['level']."/".$Mark_Config_Action['root_link']."/Post.php'</script>";
}
$post_ids = array_keys($Mark_Posts_Action);
$post_count = count($post_ids);
$date_array = array();
$tags_array = array();
for ($i = 0; $i < $post_count; $i++) {
    $post_id = $post_ids[$i];
    $post = $Mark_Posts_Action[$post_id];
    $date_array[] = substr($post['date'], 0, 7);
    $tags_array = array_merge($tags_array, (array)$post['tags']);
}
$date_array = array_unique($date_array);
$tags_array = array_unique($tags_array);
if (isset($_GET['tag']))
    $filter_tag = $_GET['tag'];
else
    $filter_tag = '';
if (isset($_GET['date']))
    $filter_date = $_GET['date'];
else
    $filter_date = '';
$Mark_Pos_Action = array();
for ($i = 0; $i < $post_count; $i++) {
    $post_id = $post_ids[$i];
    $post = $Mark_Posts_Action[$post_id];
    if ($filter_tag != '' && !in_array($filter_tag, $post['tags']))
        continue;
    if ($filter_date != '' && strpos($post['date'], $filter_date) !== 0)
        continue;
    $Mark_Pos_Action[$post_id] = $post;
}
$Mark_Posts_Action = $Mark_Pos_Action;
$post_ids = array_keys($Mark_Posts_Action);
$post_count = count($post_ids);
$last_page = ceil($post_count / 10);
if (isset($_GET['page']))
    $page_num = $_GET['page'];
else
    $page_num = 1;
if ($page_num > 1)
    $prev_page = $page_num - 1;
else
    $prev_page = 1;
if ($page_num < $last_page)
    $next_page = $page_num + 1;
else
    $next_page = $last_page;
if ($page_num < 0)
    $page_num = 1;
else
    if ($page_num > $last_page)
        $page_num = $last_page;
?>