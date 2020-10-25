<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/ 
session_start();
include 'Root_Hackdone_Action.php';
$page_file = '';
$page_path = '';
$page_state = '';
$page_title = '';
$page_content = '';
$page_date = '';
$page_time = '';
$page_can_comment = '';
$succeed = false;
date_default_timezone_set('PRC');
if (isset($_POST['Post_VI_Action'])) {
    if ($_POST['Post_VI_Action'] == $_SESSION['Post_Code']) {    
    $page_file = $_POST['file'];
    $page_state = 'publish';
    $page_title = trim($_POST['title']);
    $page_content = get_magic_quotes_gpc() ? stripslashes(trim($_POST['content'])) : trim($_POST['content']);;
    $page_date = date("Y-m-d");
    $page_time = date("H:i");
    $page_can_comment = $_POST['can_comment'];
    $index_show = $_POST['index_show'];
    if ($_POST['state'] == 'draft') {
        unset($page_state);
        $page_state = 'draft';
    }
    if ($_POST['path'] == ''){
        $page_path = RandCode(6, 0);
    }else {
        $page_path = $_POST['path'];
    }
    if ($_POST['year'] != '') $page_date = substr_replace($page_date, $_POST['year'], 0, 4);
    if ($_POST['month'] != '') $page_date = substr_replace($page_date, $_POST['month'], 5, 2);
    if ($_POST['day'] != '') $page_date = substr_replace($page_date, $_POST['day'], 8, 2);
    if ($_POST['hourse'] != '') $page_time = substr_replace($page_time, $_POST['hourse'], 0, 2);
    if ($_POST['minute'] != '') $page_time = substr_replace($page_time, $_POST['minute'], 3, 2);
   $page_path_part = explode('/', $page_path);
    $page_path_count = count($page_path_part);
    for ($i = 0; $i < $page_path_count; $i++) {
        $trim = trim($page_path_part[$i]);
        if ($trim == '') {
            unset($page_path_part[$i]);
        } else {
            $page_path_part[$i] = $trim;
        }
    }
    reset($page_path_part);
    $page_path = implode('/', $page_path_part);
    if ($page_title == '') {
        $error_msg = 'Oh,Shit !!! 标题空了';
    } else {
        if ($page_file == '') {
            $file_names = ShortUrl($page_title);
            foreach ($file_names as $file_name) {
                $file_path = FileLink.'/Index/Point/Data/Page/Data/' . $file_name . '.Mark';
                if (!is_file($file_path)) {
                    $page_file = $file_name;
                    break;
                }
            }
        } else {
            $file_path = FileLink. '/Index/Point/Data/Page/Data/' . $page_file . '.Mark';
            $data = unserialize(file_get_contents($file_path));
            $page_old_path = $data['path'];
            $page_old_state = $data['state'];
            if ($page_old_state != $page_state || $page_old_path != $page_path) {
                $index_file = FileLink.'/Index/Point/Data/Page/Index/' . $page_old_state . '.php';
                include $index_file;
                unset($Mark_Pages_Action[$page_old_path]);
                file_put_contents($index_file, "<?php\n\$Mark_Pages_Action=" . var_export($Mark_Pages_Action, true) . "\n?>");
            }
        } 
        $data = array(
            'file' => $page_file,
            'path' => $page_path,
            'state' => $page_state,
            'title' => $page_title,
            'date' => $page_date,
            'time' => $page_time,
            'index_show' => $index_show,
            'can_comment' => $page_can_comment,
        );
        $index_file = FileLink.'/Index/Point/Data/Page/Index/' . $page_state . '.php';
        include $index_file;
        $Mark_Pages_Action[$page_path] = $data;
        ksort($Mark_Pages_Action);
        file_put_contents($index_file, "<?php\n\$Mark_Pages_Action=" . var_export($Mark_Pages_Action, true) . "\n?>");
        $data['content'] = $page_content;
        file_put_contents($file_path, serialize($data));
        $succeed = true;
            }
           unset($_SESSION["Post_Code"]);  
    }else{
       echo "<script language=javascript>alert('请不要重复刷新页面！(Please,don\'t！)');window.location='".$Mark_Config_Action['site_link'].$Mark_Config_Action['level']."/".$Mark_Config_Action['root_link']."/Page.php'</script>";
    }
} else if (isset($_GET['file'])) {
    $file_path = FileLink.'/Index/Point/Data/Page/Data/' . $_GET['file'] . '.Mark';
    $data = unserialize(file_get_contents($file_path));
    $page_file = $data['file'];
    $page_path = $data['path'];
    $page_state = $data['state'];
    $page_title = $data['title'];
    $page_content = $data['content'];
    $page_date = $data['date'];
    $page_time = $data['time'];
    $page_show = $data['index_show'];
    $page_can_comment = isset($data['can_comment']) ? $data['can_comment'] : '1';
}
?>
