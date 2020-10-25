<?php
$privilege = 'priv';
include '../library/inc.php';

if (isset($_GET['del'])) {
  $_GET['del'] = intval($_GET['del']);

  if ($_GET['del'] == 1) {
    alert_back('默认角色不能删除！');
  } else {
    if (isset($_GET['del'])) {
      $sql = "DELETE FROM role WHERE id = " . $_GET['del'];
      $dataops->ops($sql, '角色删除[' . $_GET['del'] . ']');
    }
  }
}
if ($act == 'add') {
  $r_name = $_POST['r_name'];
  $res = $db->getRow("SELECT * FROM role WHERE r_name = '" . $r_name . "'");
  if (!empty($res)) {
    alert_back('角色重名');
  }
  null_back($r_name, '请填写角色名');
  $sql = "INSERT INTO role (r_name) VALUES ('" . $r_name . "')";
  $dataops->ops($sql, '角色新增', 1);
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
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">角色管理<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table am-text-nowrap">
              <thead>
              <tr>
                <th>角色名称</th><th>操作</th>
              </tr>
              </thead>
              <tbody>
                <?php
                $res = $db->getAll("SELECT * FROM role ORDER BY id ASC");
                if (!empty($res)) {
                  foreach ($res as $row) {
                    echo '<tr><td>' . $row['r_name'] . '</td><td>' . ($row['id']!=1 ? '<a href="cms_priv.php?id=' . $row['id'] . '" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-pencil"></span></a> <a href="cms_role.php?del=' . $row['id'] . '" onclick="return confirm(\'确认要删除吗？\')" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-times"></a>' : '') . '</td></tr>';
                  }
                }
                ?>
              </tbody>
            </table>
          </main>
        </section>

        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}">新增角色<span class="am-icon-chevron-down am-fr"></span></header>
          <form action="" method="post" class="am-form">
            <main class="am-panel-bd am-collapse am-in" id="collapse-panel-2">
              <div class="am-form-group">
                <label for="r_name">角色名称</label>
                 <input id="r_name" type="text" name="r_name">
              </div>
              <center>
                <button type="submit" name="submit" id="save" class="am-btn am-btn-default">提交保存</button>
                <input type="hidden" name="act" value="add">
                <button type="reset" class="am-btn am-btn-default">放弃保存</button>
              </center>
            </main>
          </form>
        </section>

      </div>
    </div>
  </div>
  <!-- content end -->
</div>

<?php include 'inc/inc_footer.php';?>

<!-- js -->
<script type="text/javascript">
$(function(){
  $('#save').click(function(){
    if ($('#r_name').val() == ''){
      alert('请填写登录帐号');
      $('#r_name').focus();
      return false;
    }
  });
});
</script>
</body>
</html>
