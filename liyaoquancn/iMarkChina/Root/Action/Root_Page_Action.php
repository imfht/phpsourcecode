<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/ 
session_start();
include 'Root_Hackdone_Action.php';
if (!is_dir(FileLink.'/Index/Point/Data/Page/Data/')) mkdir(FileLink.'/Index/Point/Data/Page/Data/',0777);
function load_pages() {
    global $state, $index_file, $Mark_Pages_Action;
    if (isset($_GET['state'])) {
        if ($_GET['state'] == 'draft') {
            $state = 'draft';
            $index_file = FileLink.'/Index/Point/Data/Page/Index/draft.php';
        } else if ($_GET['state'] == 'delete') {
            $state = 'delete';
            $index_file = FileLink.'/Index/Point/Data/Page/Index/delete.php';
        } else {
            $state = 'publish';
            $index_file = FileLink.'/Index/Point/Data/Page/Index/publish.php';
        }
    } else {
        $state = 'publish';
        $index_file = FileLink.'/Index/Point/Data/Page/Index/publish.php';
    }
    include $index_file;
}
function delete_page($id) {
    global $state, $index_file, $Mark_Pages_Action;
    $page = $Mark_Pages_Action[$id];
    $page['prev_state'] = $state;
    unset($Mark_Pages_Action[$id]);
    file_put_contents($index_file, "<?php\n\$Mark_Pages_Action=" . var_export($Mark_Pages_Action, true) . "\n?>");
    if ($state != 'delete') {
        $index_file2 = FileLink.'/Index/Point/Data/Page/Index/delete.php';
        include $index_file2;
        $Mark_Pages_Action[$id] = $page;
        file_put_contents($index_file2, "<?php\n\$Mark_Pages_Action=" . var_export($Mark_Pages_Action, true) . "\n?>");
    } else {
        unlink(FileLink.'/Index/Point/Data/Page/Data/' . $page['file'] . '.Mark');
    }
}
function revert_page($id) {
    global $state, $index_file, $Mark_Pages_Action;
    $page = $Mark_Pages_Action[$id];
    $prev_state = $page['prev_state'];
    unset($page['prev_state']);
    unset($Mark_Pages_Action[$id]);
    file_put_contents($index_file, "<?php\n\$Mark_Pages_Action=" . var_export($Mark_Pages_Action, true) . "\n?>");
    $index_file2 = FileLink.'/Index/Point/Data/Page/Index/' . $prev_state . '.php';
    include $index_file2;
    $Mark_Pages_Action[$id] = $page;
    ksort($Mark_Pages_Action);
    file_put_contents($index_file2, "<?php\n\$Mark_Pages_Action=" . var_export($Mark_Pages_Action, true) . "\n?>");
}
load_pages();
if (isset($_GET['delete']) || (isset($_GET['apply']) && $_GET['apply'] == 'delete')) {
    if (isset($_GET['apply']) && $_GET['apply'] == 'delete') {
        $ids = explode(',', $_GET['ids']);
        foreach ($ids as $id) {
            if (trim($id) == '') continue;
            delete_page($id);
            load_pages();
        }
    } else {
        delete_page($_GET['delete']);
    }
    Header('Location:Page.php?done=true&state=' . $state);
    exit();
}
if (isset($_GET['revert']) || (isset($_GET['apply']) && $_GET['apply'] == 'revert')) {
    if (isset($_GET['apply']) && $_GET['apply'] == 'revert') {
        $ids = explode(',', $_GET['ids']);
        foreach ($ids as $id) {
            if (trim($id) == '') continue;
            revert_page($id);
            load_pages();
        }
    } else {
        revert_page($_GET['revert']);
    }
    Header('Location:Page.php?done=true&state=' . $state);
    exit();
}
if (isset($_GET['done'])) {
   $message = "<script language=javascript>alert('操作成功！');window.location='".$Mark_Config_Action['site_link'].$Mark_Config_Action['level']."/".$Mark_Config_Action['root_link']."/Page.php'</script>";
}
$page_ids = array_keys($Mark_Pages_Action);
$page_count = count($page_ids);
$date_array = array();
for ($i = $page_count - 1; $i >= 0; $i--) {
    $page_id = $page_ids[$i];
    $page = $Mark_Pages_Action[$page_id];
    $date_array[] = substr($page['date'], 0, 7);
}
$date_array = array_unique($date_array);
if (isset($_GET['date'])) $filter_date = $_GET['date'];
else $filter_date = '';
$Mark_Pages_Action2 = array();
for ($i = 0; $i < $page_count; $i++) {
    $page_id = $page_ids[$i];
    $page = $Mark_Pages_Action[$page_id];
    if ($filter_date != '' && strpos($page['date'], $filter_date) !== 0) continue;
    $Mark_Pages_Action2[$page_id] = $page;
}
$Mark_Pages_Action = $Mark_Pages_Action2;
$page_ids = array_keys($Mark_Pages_Action);
$page_count = count($page_ids);
$last_page = ceil($page_count / 10);
if (isset($_GET['page'])) $page_num = $_GET['page'];
else $page_num = 1;
if ($page_num > 1) $prev_page = $page_num - 1;
else $prev_page = 1;
if ($page_num < $last_page) $next_page = $page_num + 1;
else $next_page = $last_page;
if ($page_num < $last_page) $next_page = $page_num + 1;
else $next_page = $last_page;
if ($page_num < 0) $page_num = 1;
else if ($page_num > $last_page) $page_num = $last_page;
?>