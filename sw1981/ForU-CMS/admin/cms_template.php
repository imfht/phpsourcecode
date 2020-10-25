<?php
$privilege = 'theme';
include '../library/inc.php';

if (isset($_GET['del'])) {
  $_GET['del'] = intval($_GET['del']);

  $sql = "DELETE FROM template WHERE id = " . $_GET['del'];
  $dataops->ops($sql, '模板删除[' . $_GET['del'] . ']');
}
if ($act == 'add') {
  $t_name = $_POST['t_name'];
  $t_path = $_POST['t_path'];
  if (empty($t_name) || empty($t_path)) {
    alert_back('名称或路径不能为空!');
  }
  $sql = "INSERT INTO template (t_name,t_path) VALUES ('" . $t_name . "','" . $t_path . "')";
  $dataops->ops($sql, '模板新增', 1);
}
if (isset($_GET['path'])) {
  $sql = "UPDATE system SET s_template = '" . $_GET['path'] . "'";
  $dataops->ops($sql, '模板变更[' . $_GET['path'] . ']', 1);
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
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">模版管理<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table am-text-nowrap">
              <thead>
              <tr>
                <th>名称</th><th>路径</th><th>操作</th>
              </tr>
              </thead>
              <tbody>
                <?php
                $res = $db->getAll("SELECT * FROM template ORDER BY id DESC");
                if (!empty($res)) {
                  foreach($res as $row){
                    if ($cms['s_template'] == $row['t_path']) {
                      $temp_str = '<span class="color_red">当前模板</span>';
                    } else {
                      $temp_str = '<a href="cms_template.php?path=' . $row['t_path'] . '" class="am-btn am-btn-default am-btn-xs">应用</a> <a href="cms_template.php?del=' . $row['id'] . '" onclick="return confirm(\'确定要删除吗？\')" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-times"></span></a>';
                    }
                    echo '<tr><td>' . $row['t_name'] . '</td><td>' . $row['t_path'] . '</td><td>' . $temp_str . '</td></tr>';
                  }
                }
                ?>
              </tbody>
            </table>
          </main>
        </section>

        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}">新增模版<span class="am-icon-chevron-down am-fr"></span></header>
          <form action="" method="post" class="am-form">
            <main class="am-panel-bd am-panel-bordered am-collapse am-in" id="collapse-panel-2">
              <div class="am-form-group">
                <label for="t_name">名称</label>
                <input id="t_name" type="text" name="t_name">
              </div>
              <div class="am-form-group">
                <label for="t_path">路径</label>
                <input name="t_path" type="text" id="t_path" placeholder="<?php echo TPL_DIR;?>目录下的文件夹名称, 只能是英文和数字">
              </div>
              <center>
                <button type="submit" id="save" class="am-btn am-btn-primary submit">提交保存</button>
                <button type="reset" class="am-btn am-btn-primary">放弃保存</button>
                <input type="hidden" name="act" value="add">
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
    if ($('#t_name').val() == '') {
      alert('请填写模板名称');
      $('#t_name').focus();
      return false;
    }
    if ($('#t_path').val() == '') {
      alert('请填写模板路径');
      $('#t_path').focus();
      return false;
    }
  });
});
</script>
</body>
</html>
