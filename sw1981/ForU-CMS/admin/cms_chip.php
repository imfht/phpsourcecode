<?php
$privilege = 'chip';
include '../library/inc.php';
include_once LIB_PATH . 'cls.page.php';

if (isset($_GET['del'])) {
  $_GET['del'] = intval($_GET['del']);

  $res = $db->getRow("SELECT * FROM chip WHERE id = " . $_GET['del']);
  if ($res['c_safe']) {
    alert_back('已受保护,无法删除！');
  }
  $sql = "DELETE FROM chip WHERE id = " . $_GET['del'];
  $dataops->ops($sql, '碎片删除[' . $_GET['del'] . ']');

}
if ($act == 'add') {
  $data['c_name'] = $_POST['c_name'];
  $data['c_code'] = $_POST['c_code'];
  $data['c_content'] = $_POST['c_content'];
  $data['c_safe'] = $_POST['c_safe'];
  null_back($data['c_name'],'请填写碎片名称！');

  $sql = "INSERT INTO chip " . arr_insert($data);
  $dataops->ops($sql, '碎片新增', 1);
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
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">碎片管理<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table am-text-nowrap">
              <thead>
              <tr>
                <th>碎片名称</th><th>调用代码</th><th>操作</th>
              </tr>
              </thead>
              <tbody>
                <?php
                $pager = new Page(20);
                $pager->handle($db->getOne("SELECT COUNT(*) FROM chip"));
                $res = $db->getAll("SELECT * FROM chip ORDER BY id DESC LIMIT " . $pager->page_start . "," . $pager->page_size);
                if (!empty($res)) {
                  foreach ($res as $row) {
                    echo '<tr><td>' . $row['c_name'] . '</td><td>' . $row['c_code'] . '</td><td><a href="cms_chip_edit.php?id=' . $row['id'] . '" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-pencil"></span></a> <a href="cms_chip.php?del=' . $row['id'] . '" onclick="return confirm(\'确认要删除吗？\')" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-times"></a></td></tr>';
                  }
                }
                ?>
              </tbody>
            </table>
            <div data-am-page="{pages:<?php echo $pager->page_sum;?>,curr:<?php echo $pager->page_current;?>,jump:'?page=%page%'}"></div>
          </main>
        </section>

        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}">新增碎片<span class="am-icon-chevron-down am-fr"></span></header>
          <form action="" method="post" class="am-form">
            <main class="am-panel-bd am-collapse am-in" id="collapse-panel-2">
              <div class="am-form-group">
                <label for="c_name">碎片名称</label>
                 <input id="c_name" type="text" name="c_name">
              </div>
              <div class="am-form-group">
                <label for="c_code">调用代码</label>
                 <input id="c_code" type="text" name="c_code">
              </div>
              <div class="am-form-group">
                <label for="c_content">碎片内容</label>
                 <textarea id="c_content" name="c_content" class="editor"></textarea>
              </div>
              <div class="am-form-group">
                <label for="">安全保护</label>
                <div>
                  <label class="am-btn am-btn-default">
                    <input type="radio" name="c_safe" value="1"/> 是
                  </label>
                  <label class="am-btn am-btn-default am-active">
                    <input type="radio" name="c_safe" value="0" checked="checked" /> 否
                  </label>
                </div>
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
      if ($('#c_name').val() == '') {
        alert('请填写碎片名称');
        $('#c_name').focus();
        return false;
      }
    });
  });
</script>
</body>
</html>
