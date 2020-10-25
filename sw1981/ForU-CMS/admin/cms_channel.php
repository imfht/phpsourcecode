<?php
$privilege = 'channel';
include '../library/inc.php';

if (isset($_GET['del'])) {
  $_GET['del'] = intval($_GET['del']);

  $sql = "SELECT * FROM channel WHERE id = " . $_GET['del'];
  $res = $db->getRow($sql);
  if ($res['c_ifsub'] == 0 && $res['c_safe'] == 0) {
    // 频道相关清理
    $db->query("DELETE FROM channel WHERE id = " . $_GET['del']);
    $db->query("DELETE FROM detail WHERE d_parent = " . $_GET['del']);
    update_channel();
    admin_log('频道删除');
  } else {
    alert_back('此频道存在下级或已受保护，无法删除！');
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
        <div class="am-panel am-panel-default">
          <div class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">频道管理<span class="am-icon-chevron-down am-fr"></span></div>
          <div class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table am-text-nowrap">
              <thead>
              <tr>
              <th>ID</th><th>排序</th><th>频道名称</th><th>频道模型</th><th>内容模型</th><th>属性</th><th>频道操作</th>
              </tr>
              </thead>
              <tbody>
                <?php echo channel_list(0,0);?>
              </tbody>
            </table>
          </div>
        </div>
        </div>
    </div>
    </div>
    <!-- content end -->
</div>

<?php include 'inc/inc_footer.php';?>
</body>
</html>
