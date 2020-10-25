<?php
$privilege = 'base';
include '../library/inc.php';
include_once LIB_PATH . 'cls.page.php';

if (ADMIN_LOG === false) {
  alert_back('日志功能并未开启, 如需开启请在config.php文件内将 ADMIN_LOG 项设置为 true');
}

// 清理一年前的数据
if ($act == 'clear') {
  $lmt = 365 * 24 * 3600;
  $time_diff = date('Y-m-d H:i:s', time() - $lmt);
  if ($db->query("DELETE FROM admin_log WHERE log_time < '" . $time_diff . "'")) {
    alert_href('操作完成', 'cms_log.php');
  } else {
    alert_href('操作失败', 'cms_log.php');
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
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">日志管理<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table am-text-nowrap">
              <thead>
              <tr>
                <th>ID</th><th>管理员</th><th>操作</th><th>时间</th><th>IP</th>
              </tr>
              </thead>
              <tbody>
                <?php
                $pager = new Page(100);
                $pager->handle($db->getOne("SELECT COUNT(*) FROM admin_log"));
                $res = $db->getAll("SELECT * FROM admin_log ORDER BY id DESC LIMIT " . $pager->page_start . "," . $pager->page_size);
                if ($row = $res) {
                  foreach ($res as $val) {
                    echo '<tr><td>' . $val['id'] . '</td><td>' . $val['admin_name'] . '</td><td>' . $val['log_code'] . '</td><td>' . $val['log_time'] . '</td><td>' . $val['log_ip'] . '</td></tr>';
                  }
                }
                ?>
              </tbody>
            </table>
            <div data-am-page="{pages:<?php echo $pager->page_sum;?>,curr:<?php echo $pager->page_current;?>,jump:'?page=%page%'}"></div>
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
