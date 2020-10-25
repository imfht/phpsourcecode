<?php
header('Content-type: text/html;charset=UTF-8');
if (isset($_POST['save'])) {
  $db_name = $_POST['db_name'];
  $file_name = 'data.sql';

  $str = '';
  $str .= '<?php';
  $str .= "\n";
  $str .= '//Mysql数据库信息';
  $str .= "\n";
  $str .= 'define(\'DATA_HOST\', \'' . $_POST['db_host'] . '\');';
  $str .= "\n";
  $str .= 'define(\'DATA_USERNAME\', \'' . $_POST['db_username'] . '\');';
  $str .= "\n";
  $str .= 'define(\'DATA_PASSWORD\', \'' . $_POST['db_password'] . '\');';
  $str .= "\n";
  $str .= 'define(\'DATA_NAME\', \'' . $db_name . '\');';
  $str .= "\n";
  $str .= 'define(\'DATA_PREFIX\', \'' . $_POST['db_prefix'] . '\');';
  $str .= "\n";
  $str .= '?>';
  $files = '../config/data.php';
  $ff = fopen($files, 'w+');
  fwrite($ff, $str);

  include '../config/config.php';
  include '../config/data.php';
  include_once '../library/library.php';
  include_once '../library/cls.sql.php';
  include_once '../library/cls.pdo.php';

  set_time_limit(0);
  $db = new DataObject();
  $fp = @fopen($file_name, "r");

  if ($fp === false) {
    die("不能打开SQL文件");
  }

  if ($db->query('use ' . DATA_NAME) === NULL || mysql_errno()) {
    die('无法连接数据库,请检查信息后重试!');
  }

  $tbl = $db->query("SHOW TABLES");

  if ($tbl) {
      echo "正在清空数据库,请稍等....<br>";
      while ($row = $tbl->fetch(PDO::FETCH_NUM)) {
        $db->query("DROP TABLE {$row[0]}");
      }
      echo "数据库清理成功<br>";
  }
  unset($tbl);

  echo "正在执行导入数据库操作<br>";
  // 导入数据库的MySQL命令
  $sql=fread($fp, filesize($file_name));
  fclose($fp);
  $sql = str_replace("\r", "\n", $sql);
  $sql=explode(";\n", $sql);

  foreach($sql as $val){
    if ($val!=="\n") $db->query($val);
  }
  unset($sql);

  // 处理管理密码
  $db->query("UPDATE user SET u_psw='" . psw_hash($_POST['admin_password']) . "' WHERE id = 1");

  echo "数据库文件导入完成!";

  alert_href('安装成功,为了确保安全请尽快删除install目录','../index.php');
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>安装向导</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="body_main">
  <h1>MYSQL信息</h1>
  <form method="post">
    <table class="common_table">
      <tr>
        <td align="center">主机名</td>
        <td>
          <input id="db_host" class="form_text" type="text" name="db_host" value="127.0.0.1">
          <span class="color_red">*</span>
          <span class="color_gray">一般为 localhost:3306 或者 127.0.0.1:3306</span>
        </td>
      </tr>
      <tr>
        <td align="center">数据库名</td>
        <td>
          <input id="db_name" class="form_text" type="text" name="db_name" value="">
          <span class="color_red">*</span>
          <span class="color_gray">请提供的数据库名。</span>
        </td>
      </tr>
      <tr>
        <td align="center">用户</td>
        <td>
          <input id="db_username" class="form_text" type="text" name="db_username">
          <span class="color_red">*</span>
          <span class="color_gray">请填写MYSQL链接用户名</span>
        </td>
      </tr>
      <tr>
        <td align="center">密码</td>
        <td>
          <input id="db_password" class="form_text" type="text" name="db_password">
          <span class="color_red">*</span>
          <span class="color_gray">请填写MYSQL链接密码</span>
        </td>
      </tr>
      <tr>
        <td align="center">管理ID</td>
        <td>
          <input id="admin_id" class="form_text" type="text" name="admin_id" value="admin" style="background-color:#EEE;" readonly/>
        </td>
      </tr>
      <tr>
        <td align="center">管理密码</td>
        <td>
          <input id="admin_password" class="form_text" type="text" name="admin_password">
          <span class="color_red">*</span>
          <span class="color_gray">请填写管理密码</span>
        </td>
      </tr>
      <tr>
        <td align="center">表前缀</td>
        <td>
          <input id="db_prefix" class="form_text" type="text" name="db_prefix" value="cms_">
          <span class="color_red">*</span>
          <span class="color_gray">前缀后增加下划线"_"</span>
        </td>
      </tr>
      <tr>
        <td></td>
        <td><input id="save" class="form_submit" type="submit" name="save" value="安装"></td>
      </tr>
    </table>
  </form>
</div>

<script src="../js/jquery.min.js"></script>
<script type="text/javascript">
$(function(){
  $('#save').click(function(){
    if($('#db_host').val() == ''){
      alert('请填写主机名');
      $('#db_host').focus();
      return false;
    }
    if($('#db_name').val() == ''){
      alert('请填写数据库名');
      $('#db_name').focus();
      return false;
    }
    if($('#db_username').val() == ''){
      alert('请填写用户名');
      $('#db_username').focus();
      return false;
    }
    if($('#db_password').val() == ''){
      alert('请填写密码');
      $('#db_password').focus();
      return false;
    }
    if($('#admin_id').val() == '' && $('#admin_id').val()!='admin'){
      alert('请填写管理ID, 默认为admin');
      $('#admin_id').focus();
      return false;
    }
    if($('#admin_password').val() == ''){
      alert('请填写管理密码');
      $('#admin_password').focus();
      return false;
    }
    if($('#db_prefix').val() == ''){
      alert('请填写表前缀');
      $('#db_prefix').focus();
      return false;
    }
  });
});
</script>
</body>
</html>
