<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/ 
session_start();
include 'Root_Hackdone_Action.php';
$links_file = '';
$links_path = '';
$links_state = '';
$links_title = '';
$links_content = '';
$links_date = '';
$links_time = '';
$links_can_comment = '';
$succeed = false;
date_default_timezone_set('PRC');
if (isset($_POST['Post_VI_Action'])) {
    if ($_POST['Post_VI_Action'] == $_SESSION['Post_Code']) {    
    $links_file = $_POST['file'];
    $links_state = 'publish';
    $links_title = trim($_POST['title']);
    $links_content = get_magic_quotes_gpc() ? stripslashes(trim($_POST['content'])) : trim($_POST['content']);;
    $links_date = date("Y-m-d");
    $links_time = date("H:i");
    $links_url = $links_content;
    if ($_POST['state'] == 'draft') {
        unset($links_state);
        $links_state = 'draft';
    }
    if ($_POST['path'] == ''){
        $links_path = RandCode(6, 0);
    }else {
        $links_path = $_POST['path'];
    }
    if ($_POST['year'] != '') $links_date = substr_replace($links_date, $_POST['year'], 0, 4);
    if ($_POST['month'] != '') $links_date = substr_replace($links_date, $_POST['month'], 5, 2);
    if ($_POST['day'] != '') $links_date = substr_replace($links_date, $_POST['day'], 8, 2);
    if ($_POST['hourse'] != '') $links_time = substr_replace($links_time, $_POST['hourse'], 0, 2);
    if ($_POST['minute'] != '') $links_time = substr_replace($links_time, $_POST['minute'], 3, 2);
   $links_path_part = explode('/', $links_path);
    $links_path_count = count($links_path_part);
    for ($i = 0; $i < $links_path_count; $i++) {
        $trim = trim($links_path_part[$i]);
        if ($trim == '') {
            unset($links_path_part[$i]);
        } else {
            $links_path_part[$i] = $trim;
        }
    }
    reset($links_path_part);
    $links_path = implode('/', $links_path_part);
    if ($links_title == '') {
        $error_msg = 'Oh,Shit !!! 标题空了';
    } elseif ($links_content == '') {
        $error_msg = 'Oh,Shit !!! 链接空了';        
    }else {
        if ($links_file == '') {
            $file_names = ShortUrl($links_title);
            foreach ($file_names as $file_name) {
                $file_path = FileLink.'/Index/Point/Data/Links/Data/' . $file_name . '.Mark';
                if (!is_file($file_path)) {
                    $links_file = $file_name;
                    break;
                }
            }
        } else {
            $file_path = FileLink.'/Index/Point/Data/Links/Data/' . $links_file . '.Mark';
            $data = unserialize(file_get_contents($file_path));
            $links_old_path = $data['path'];
            $links_old_state = $data['state'];
            if ($links_old_state != $links_state || $links_old_path != $links_path) {
                $index_file = FileLink.'/Index/Point/Data/Links/Index/' . $links_old_state . '.php';
                include $index_file;
                unset($Mark_Links_Action[$links_old_path]);
                file_put_contents($index_file, "<?php\n\$Mark_Links_Action=" . var_export($Mark_Links_Action, true) . "\n?>");
            }
        } 
        $data = array(
            'file' => $links_file,
            'path' => $links_path,
            'state' => $links_state,
            'title' => $links_title,
            'date' => $links_date,
            'time' => $links_time,
            'url'    => $links_url,
        );
        $index_file = FileLink.'/Index/Point/Data/Links/Index/' . $links_state . '.php';
        include $index_file;
        $Mark_Links_Action[$links_path] = $data;
        ksort($Mark_Links_Action);
        file_put_contents($index_file, "<?php\n\$Mark_Links_Action=" . var_export($Mark_Links_Action, true) . "\n?>");
        $data['content'] = $links_content;
        file_put_contents($file_path, serialize($data));
        $succeed = true;
            }
           unset($_SESSION["Post_Code"]);  
    }else{
        echo "<script language=javascript>alert('请不要重复刷新页面！(Please,don\'t！)');window.location='".$Mark_Config_Action['site_link'].$Mark_Config_Action['level']."/".$Mark_Config_Action['root_link']."/Links.php'</script>";
    }
} else if (isset($_GET['file'])) {
    $file_path = FileLink.'/Index/Point/Data/Links/Data/' . $_GET['file'] . '.Mark';
    $data = unserialize(file_get_contents($file_path));
    $links_file = $data['file'];
    $links_path = $data['path'];
    $links_state = $data['state'];
    $links_title = $data['title'];
    $links_content = $data['content'];
    $links_date = $data['date'];
    $links_time = $data['time'];
}
?>
