<?php
$privilege = 'link';
include '../library/inc.php';
include_once LIB_PATH . 'cls.page.php';

if (isset($_GET['del'])) {
  $_GET['del'] = intval($_GET['del']);

  $sql = "DELETE FROM link WHERE id = " . $_GET['del'];
  $dataops->ops($sql, '链接删除[' . $_GET['del'] . ']');
}
if ($act == 'add') {
  $data['l_name'] = $_POST['l_name'];
  $data['l_picture'] = $_POST['l_picture'];
  $data['l_link'] = $_POST['l_link'];
  $data['l_order'] = $_POST['l_order'];

  $sql = "INSERT INTO link " . arr_insert($data);
  $dataops->ops($sql, '链接新增', 1);
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
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">链接管理<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table am-text-nowrap">
              <thead>
              <tr>
                <th>排序</th><th>链接图片</th><th>链接名称</th><th>链接地址</th><th>操作</th>
              </tr>
              </thead>
              <tbody>
                <?php
                $pager = new Page(20);
                $pager->handle($db->getOne("SELECT COUNT(*) FROM link"));
                $res = $db->getAll("SELECT * FROM link ORDER BY id DESC LIMIT " . $pager->page_start . "," . $pager->page_size);
                if (!empty($res)) {
                  foreach ($res as $row) {
                    $picture = strpos($row['l_picture'], '/')==0 || strpos($row['l_picture'], 'http://')!==false ? $row['l_picture'] : '../'.$row['l_picture'];
                    echo '<tr><td>' . $row['l_order'] . '</td><td><a href="' . $picture . '" target="_blank"><img src="' . $picture . '" width="100" height="30" /></a></td><td>' . $row['l_name'] . '</td><td>' . $row['l_link'] . '</td><td><a href="cms_link_edit.php?id=' . $row['id'] . '" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-pencil"></span></a> <a href="cms_link.php?del=' . $row['id'] . '" onclick="return confirm(\'确认要删除吗？\')" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-times"></span></a></td></tr>';
                  }
                }
                ?>
              </tbody>
            </table>
            <div data-am-page="{pages:<?php echo $pager->page_sum;?>,curr:<?php echo $pager->page_current;?>,jump:'?page=%page%'}"></div>
          </main>
        </section>

        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}">新增链接<span class="am-icon-chevron-down am-fr"></span></header>
          <form action="" method="post" class="am-form">
            <main class="am-panel-bd am-panel-bordered am-collapse am-in" id="collapse-panel-2">
              <div class="am-form-group">
                <label for="l_name">链接名称</label>
                 <input id="l_name" type="text" name="l_name">
              </div>
              <div class="am-form-group">
                <label for="l_picture">链接图片</label>
                <div class="am-input-group">
                  <input name="l_picture" type="text" id="l_picture" class="am-form-field">
                  <span class="am-input-group-btn">
                    <button type="button" class="am-btn am-btn-default" id="l_picture_upload">选择图片</button>
                  </span>
                </div>
              </div>
              <div class="am-form-group">
                <label for="l_link">链接地址</label>
                 <input id="l_link" type="text" name="l_link" value="http://">
              </div>
              <div class="am-form-group">
                <label for="l_order">排序</label>
                 <input id="l_order" type="text" name="l_order" value="100">
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
    if ($('#l_name').val() == ''){
      alert('请填写链接名称');
      $('#l_name').focus();
      return false;
    }
    if (isNaN($('#l_order').val()) || $('#l_order').val() == '') {
      alert('排序必须是数字');
      $('#l_order').focus();
      return false;
    }
  });
});
</script>
</body>
</html>
