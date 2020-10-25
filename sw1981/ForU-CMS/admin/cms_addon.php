<?php
$privilege = 'addon';
include '../library/inc.php';
include_once LIB_PATH . 'cls.page.php';

if (isset($_GET['del'])) {
  $_GET['del'] = intval($_GET['del']);
  $res = $db->getRow("SELECT * FROM addon WHERE id = " . $_GET['del']);
  if ($res['a_safe']) {
    alert_back('已受保护,无法删除！');
  }
  $sql = "DELETE FROM addon WHERE id = " . $_GET['del'];
  $dataops->ops($sql, '插件删除[' . $_GET['del'] . ']');
}

if ($act == 'add') {
  $data['a_name'] = str_safe($_POST['a_name']);
  $data['a_func'] = str_safe($_POST['a_func']);
  $data['a_desc'] = str_safe($_POST['a_desc']);
  $data['a_order'] = intval($_POST['a_order']);
  $data['a_enable'] = intval($_POST['a_enable']);
  $data['a_safe'] = intval($_POST['a_safe']);
  null_back($data['a_name'], '请填写插件名称！');
  null_back($data['a_func'], '请填写调用函数！');

  $sql = "INSERT INTO addon " . arr_insert($data);
  $dataops->ops($sql, '插件新增', 1);
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
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">插件管理<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table am-text-nowrap">
              <thead>
                <tr>
                  <th>插件名称</th><th>调用函数</th><th>排序</th><th>操作</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $pager = new Page(20);
                $pager->handle($db->getOne("SELECT COUNT(*) FROM addon"));
                $res = $db->getAll("SELECT * FROM addon ORDER BY a_order ASC,id DESC LIMIT " . $pager->page_start . "," . $pager->page_size);
                if (!empty($res)) {
                  foreach ($res as $row) {
                    echo '<tr><td>' . $row['a_name'] . '</td><td>' . $row['a_func'] . '</td><td>' . $row['a_order'] . '</td><td><a href="cms_addon_edit.php?id=' . $row['id'] . '" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-pencil"></span></a> <a href="cms_addon.php?del=' . $row['id'] . '" onclick="return confirm(\'确认要删除吗？\')" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-times"></a></td></tr>';
                  }
                }
                ?>
              </tbody>
            </table>
            <div data-am-page="{pages:<?php echo $pager->page_sum;?>,curr:<?php echo $pager->page_current;?>,jump:'?page=%page%'}"></div>
          </main>
        </section>

        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}">新增插件<span class="am-icon-chevron-down am-fr"></span></header>
          <form action="" method="post" class="am-form">
            <main class="am-panel-bd am-collapse am-in" id="collapse-panel-2">
              <div class="am-form-group">
                <label for="a_name">插件名称</label>
                 <input id="a_name" type="text" name="a_name">
              </div>
              <div class="am-form-group">
                <label for="a_func">调用函数</label>
                 <input id="a_func" type="text" name="a_func">
              </div>
              <div class="am-form-group">
                <label for="a_desc">插件描述</label>
                 <textarea id="a_desc" name="a_desc"></textarea>
              </div>
              <div class="am-form-group">
                <label for="a_order">排序</label>
                 <input id="a_order" type="text" name="a_order" value="100">
              </div>
              <div class="am-form-group">
                <label for="">生效</label>
                <div>
                  <label class="am-btn am-btn-default">
                    <input type="radio" name="a_enable" value="1" checked="checked"/> 是
                  </label>
                  <label class="am-btn am-btn-default am-active">
                    <input type="radio" name="a_enable" value="0" /> 否
                  </label>
                </div>
              </div>
              <div class="am-form-group">
                <label for="">安全保护</label>
                <div>
                  <label class="am-btn am-btn-default">
                    <input type="radio" name="a_safe" value="1"/> 是
                  </label>
                  <label class="am-btn am-btn-default am-active">
                    <input type="radio" name="a_safe" value="0" checked="checked" /> 否
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
      if ($('#a_name').val() == '') {
        alert('请填写插件名称');
        $('#a_name').focus();
        return false;
      }
      if ($('#a_func').val() == '') {
        alert('请填写插件名称');
        $('#a_func').focus();
        return false;
      }
    });
  });
</script>
</body>
</html>
