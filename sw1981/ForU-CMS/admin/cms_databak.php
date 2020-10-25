<?php
$privilege = 'database';
include '../library/inc.php';
include_once '../library/cls.database.php';

$dir = '../' . SQL_DIR . '/';

if ($act == 'del') {
  $f = $dir . get_file_name($_GET['f']) . '.sql';
  if (@unlink($f)) {
    href('cms_databak.php');
  } else {
    alert_href($GLOBALS['lang']['msg_failed'], 'cms_databak.php');
  }
}
?>
<!DOCTYPE html>
<html class="no-js fixed-layout">
<head>
<?php include 'inc/inc_head.php';?>
</head>

<body>
<?php include 'inc/inc_header.php';?>

<div class="am-cf admin-main">
  <!-- content start -->
  <div class="admin-content">
    <div class="am-g am-g-fixed">
      <div class="am-u-sm-12 am-padding-top">

        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">数据库备份管理<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-in" id="collapse-panel-1">
            <a href="cms_database.php?act=backup" class="am-btn am-btn-default">数据库备份</a>
            <a href="cms_database.php?act=optimize" class="am-btn am-btn-default">数据库优化</a>
            <a href="javascript:if(confirm('数据正常的情况下，请不要随意进行修复操作！\n您确定要进行该操作吗？')) window.location.href='cms_database.php?act=repair';" class="am-btn am-btn-default">数据库修复</a>
            <a href="cms_database.php" class="am-btn am-btn-default am-fr">返回</a>
            <hr>
            <table class="am-table am-table-compact am-table-striped admin-content-table">
              <thead>
              <tr>
                <th>序号</th>
                <th>名称</th>
                <th>操作</th>
              </tr>
              </thead>
              <tbody>
                <?php
                  if (is_dir($dir)) {
                    $file = scandir($dir);
                    if (isset($file[2])) {
                      foreach ($file as $val) {
                        if ($val!='.' && $val!='..') {
                          $arr[] = $val;
                        }
                      }
                      rsort($arr);
                      foreach ($arr as $key => $val) {
                        $file = $val;
                        echo '<tr><td>' . ($key+1) . '</td><td><a href="' . $file . '">' . $val . '</a></td><td><a href="?act=del&f=' . $file . '" onclick="return confirm(\'确认要删除吗？\')" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-times"></span></a></td></tr>';
                      }
                    } else {
                      echo '<tr><td colspan=3 align=center>暂时没有备份数据</td></tr>';
                    }
                  } else {
                    echo '<tr><td colspan=3 align=center>备份文件夹不存在</td></tr>';
                  }
                ?>
              </tbody>
            </table>
          </main>
        </section>

      </div>
    </div>
  </div>
  <!-- content end -->
</div>

<?php include 'inc/inc_footer.php';?>

</body>
</html>
