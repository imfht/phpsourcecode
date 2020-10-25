<?php
$privilege = 'theme';
include '../library/inc.php';

if (isset($_GET['path'])) {
  $_GET['path'] = str_safe($_GET['path']);

  $sql = "UPDATE system SET s_lang = '" . $_GET['path'] . "'";
  $dataops->ops($sql, '语言变更[' . $_GET['path'] . ']', 1);
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
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">语言管理<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table am-text-nowrap">
              <thead>
              <tr>
                <th>路径</th><th>操作</th>
              </tr>
              </thead>
              <tbody>
                <?php
                $res = scandir(ROOT_PATH . LANG_DIR);
                if (!empty($res)) {
                  foreach($res as $val){
                    if ($val!='.' && $val!='..') {
                      if ($cms['s_lang'] == $val) {
                        $temp_str = '<span class="color_red">当前语言</span>';
                      } else {
                        $temp_str = '<a href="cms_lang.php?path=' . $val . '" class="am-btn am-btn-default am-btn-xs">应用</a>';
                      }
                      echo '<tr><td>' . $val . '</td><td>' . $temp_str . '</td></tr>';
                    }
                  }
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
