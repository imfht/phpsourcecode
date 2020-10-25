<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8" />
<title>开发代号: iMarkChina</title>
<style>
* {padding:0;margin:0;font-family:"Microsoft YaHei",Segoe UI,Tahoma,Arial,Verdana,sans-serif;}
html,body { height:100%; }
body { background:#f5f5f5; color:#2d2d2d; font-size:14px; }
#main { position:absolute; left:35%;  }
#mainbox { background-color: #fff; border: 1px solid #e5e5e5; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; -webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, .05); -moz-box-shadow: 0 1px 2px rgba(0, 0, 0, .05); box-shadow: 0 1px 2px rgba(0, 0, 0, .05); padding:20px; margin-bottom:20px; }
.btn-primary { color: #fff;text-shadow: 0 -1px 0 rgba(0,0,0,0.25);background-color: #0aaaf1;}
label { font-weight:bold; color:#333; font-size:12px; }
.textbox input { border:none; padding:0; font-size:18px; width:312px; color:#333; outline:0; }
.textbox { border:1px solid #e0e0e0; padding:6px; margin:6px 0 20px; border-radius:3px 3px 3px 3px; }
</style>
</head>
<?php
date_default_timezone_set('PRC');
   include_once './Index/Point/Index_Config_Action.php';
if(@!include_once("./Public/Uploadfile/Done.lock")){
    function new_is_writeable($file) {
              if (is_dir($file)){
             $dir = $file;
            if ($fp = @fopen("$dir/Mark.txt", 'w')) {
             @fclose($fp);
             @unlink("$dir/Mark.txt");
              $writeable = 1;
             } else {
            $writeable = 0;
              }
            } else {
              if ($fp = @fopen($file, 'a+')) {
             @fclose($fp);
             $writeable = 1;
              } else {
              $writeable = 0;
             }
              }
             return $writeable;
}
$uploadfie = new_is_writeable('./Public/Uploadfile/');
$action = new_is_writeable('./Index/Point/Index_Config_Action.php');
$post = new_is_writeable('./Index/Point/Data/Post');
$page = new_is_writeable('./Index/Point/Data/Page');
$link = new_is_writeable('./Index/Point/Data/Links');
  $url=dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);  
if ($uploadfie != '1') {
  echo '<div style="font-size:32px;font-weight:bold;text-align:center;padding-top:40px;">./Public/Uploadfile/ &nbsp&nbsp目录不可写!<br>如果你不懂设置请把全部目录与文件设置为777<br>
设置好权限后请刷新重试!</div>';
}elseif ($action != '1') {
  echo '<div style="font-size:32px;font-weight:bold;text-align:center;padding-top:40px;">./Index/Action/Index_Config_Action.php &nbsp&nbspIndex_Config_Action.php不可写!<br>如果你不懂设置请把全部目录与文件设置为777<br>
设置好权限后请刷新重试!</div>';
}elseif ($post != '1') {
  echo  '<div style="font-size:32px;font-weight:bold;text-align:center;padding-top:40px;">./Index/Point/Data/Post&nbsp&nbsp目录下的全部目录与文件不可写!<br>如果你不懂设置请把全部目录与文件设置为777<br>
设置好权限后请刷新重试!</div>';
}elseif($page != '1'){
  echo  '<div style="font-size:32px;font-weight:bold;text-align:center;padding-top:40px;">./Index/Point/Data/Page&nbsp&nbsp目录下的全部目录与文件不可写!<br>如果你不懂设置请把全部目录与文件设置为777<br>
设置好权限后请刷新重试!</div>';
}elseif($link != '1'){
  echo  '<div style="font-size:32px;font-weight:bold;text-align:center;padding-top:40px;">./Index/Point/Data/Links&nbsp&nbsp目录下的全部目录与文件不可写!<br>如果你不懂设置请把全部目录与文件设置为777<br>
设置好权限后请刷新重试!</div>';
}else{
    if (isset($_POST['action'])) {
     $Mark_Config_Action['site_name'] = str_replace('\\','',$Mark_Config_Action['site_name']);
     $Mark_Config_Action['copy_right'] = str_replace('\\','',$Mark_Config_Action['copy_right']);
    $Mark_Config_Action['site_name'] = $Mark_Config_Action['site_name'];
    $Mark_Config_Action['site_link'] = $_POST['site_link'];
    $Mark_Config_Action['nametwo'] = $Mark_Config_Action['nametwo'];
    $Mark_Config_Action['site_desc'] = $Mark_Config_Action['site_desc'];
    $Mark_Config_Action['site_key'] = $Mark_Config_Action['site_key'];
    $Mark_Config_Action['site_mumber'] = $Mark_Config_Action['site_mumber'];
    $Mark_Config_Action['user_nick'] = $_POST['user_nick'];
    $Mark_Config_Action['user_pass'] = MD5($_POST['user_pass']);
    $Mark_Config_Action['root_link'] = $Mark_Config_Action['root_link'];
    $Mark_Config_Action['style'] = $Mark_Config_Action['style'];
    $Mark_Config_Action['fdlinks'] = $Mark_Config_Action['fdlinks'];
    $Mark_Config_Action['write'] = $Mark_Config_Action['write'];
    if ($_POST['level'] != '') {
      $Mark_Config_Action['level'] = '/'.$_POST['level'];
    } else {
      $Mark_Config_Action['level'] = '';
    }
    $Mark_Config_Action['comment_code'] = $Mark_Config_Action['comment_code'];
        if($_POST['runyear'] == '') {$Mark_Config_Action['runyear'] = date("Y");}else{$Mark_Config_Action['runyear'] = $_POST['runyear'];}
    if($_POST['runmon'] == '') {$Mark_Config_Action['runmon'] = date("m");}else{$Mark_Config_Action['runmon'] = $_POST['runmon'];}
    if($_POST['runday'] == '') {$Mark_Config_Action['runday'] = date("d");}else{$Mark_Config_Action['runday'] = $_POST['runday'];}
    if($_POST['runhour'] == '') {$Mark_Config_Action['runhour'] = date("H");}else{$Mark_Config_Action['runhour'] = $_POST['runhour'];}
    if($_POST['runmin'] == '') {$Mark_Config_Action['runmin'] = date("i");}else{$Mark_Config_Action['runmin'] = $_POST['runmin'];}
    if($_POST['runsec'] == '') {$Mark_Config_Action['runsec'] = date("s");}else{$Mark_Config_Action['runsec'] = $_POST['runsec'];}
    $code = "<?php\n\$Mark_Config_Action = " . var_export($Mark_Config_Action, true) . "\n?>";
    file_put_contents('./Index/Point/Index_Config_Action.php', $code);
    fopen("./Public/Uploadfile/Done.lock","a");
    @rename("Mark.php","Mark.Done");
    echo ' <div style="font-size:32px;font-weight:bold;text-align:center;padding-top:40px;">设置保存成功！</div>';
    echo '<div style="font-size:32px;font-weight:bold;text-align:center;padding-top:40px;"><a href="'.$url.'">←前台</a></div>';
}else{
?>
<body style="background:#f2f2f2;">
  <div id="main">
    <div style="font-size:32px;font-weight:bold;text-align:center;padding-top:40px;">开发代号: iMarkChina</div>
    <div id="mainbox">
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <label>网站标题</label>
      <div class="textbox"><input type="text" name="site_name" value="<?php echo $Mark_Config_Action['site_name']; ?>"/></div>
      <label>网站地址（网站域名如 http://www.imarkchina.cn ）</label>
      <div class="textbox"><input type="text" name="site_link" id="site_link"  value="" placeholder="以 http:// 开头结尾不带 / "/></div>
      <script type="text/javascript">
      document.getElementById('site_link').value = 'http://'+window.location.host;
      </script>
       <label>二级目录（二级目录安装务必填写，非二级目录必定不写 <br>如：iMarkChina 文件夹名前后不带  / ）</label>
      <div class="textbox"><input type="text" name="level" value=""/></div>
      <label>站长昵称</label>
      <div class="textbox"><input type="text" name="user_nick" value="<?php echo $Mark_Config_Action['user_nick']; ?>"/></div>
      <label>后台账号</label>
      <div class="textbox"><input type="text" name="user_name" value="<?php echo $Mark_Config_Action['user_name']; ?>"/></div>
      <label>后台密码（默认密码: root）</label>
      <div class="textbox"><input type="password" name="user_pass" value="root"/></div>
      <div style="text-align:center;"><input type="submit" name="action" value="Action" class="btn btn-primary"/></div>
      <input type="hidden" name="runyear" value="<?php echo $Mark_Config_Action['runyear']; ?>"/>
      <input type="hidden" name="runmon" value="<?php echo $Mark_Config_Action['runmon']; ?>"/>
       <input type="hidden" name="runday" value="<?php echo $Mark_Config_Action['runday']; ?>"/>
       <input type="hidden" name="runhour" value="<?php echo $Mark_Config_Action['runhour']; ?>"/>
      <input type="hidden" name="runmin" value="<?php echo $Mark_Config_Action['runmin']; ?>"/>
        <input type="hidden" name="runsec" value="<?php echo $Mark_Config_Action['runsec']; ?>"/>
    </form>
       </div>
  </div>
  <?php } }  }else{
    echo '<div style="font-size:32px;font-weight:bold;text-align:center;padding-top:40px;">请删除（./Public/Uploadfile/）目录下的Done.lock文件后刷新重试！</div>';
} ?>
</body>
</html>
