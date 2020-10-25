<?php
$privilege = 'database';
include '../library/inc.php';
include_once '../library/cls.database.php';
$dbc = new Database();
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
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">数据表 <?php echo str_safe($_GET['id']);?><span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <table class="am-table am-table-compact admin-content-table">
              <thead>
              <tr>
                <th>表名</th>
              </tr>
              </thead>
              <tbody>
                <tr>
                <?php
                $res = $dbc->tables();
                if (!empty($res)) {
                  $count = count($res);
                  for ($i=0; $i<$count; $i++) {
                    echo '<td class="am-u-sm-12 am-u-md-4"><a href="cms_database_table.php?id=' . $res[$i] . '" title="点击查看表结构">' . $res[$i] . '</a></td>';
                  }
                  for ($i=0; $i < 3-$count%3; $i++) {
                    echo strpos($res[$i], DATA_PREFIX)===FALSE ? '<td class="am-hide-sm-down am-u-md-4"></td>' : '';
                  }
                }
                ?>
                </tr>
              </tbody>
            </table>
            <hr>
            <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table">
              <thead>
                <tr>
                  <th>数据列名</th>
                  <th>数据类型</th>
                  <th class="am-hide-sm-down">主键</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($_GET['id']) {
                  $dbc = new Database();
                  $res = $dbc->cols(str_safe($_GET['id']));
                  foreach ($res as $val) {
                    echo '<tr><td>' . $val['Field'] . '</td><td>' . $val['Type'] . '</td><td class="am-hide-sm-down">' . $val['Key'] . '</td></tr>';
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
