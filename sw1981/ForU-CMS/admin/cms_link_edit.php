<?php
$privilege = 'link';
include '../library/inc.php';

if ($act == 'edit') {
  null_back($_POST['l_name'], '请填写链接名称');
  non_numeric_back($_POST['l_order'], '排序必须是数字!');
  $data['l_name'] = $_POST['l_name'];
  $data['l_picture'] = $_POST['l_picture'];
  $data['l_link'] = $_POST['l_link'];
  $data['l_order'] = $_POST['l_order'];
  $sql = "UPDATE link SET " . arr_update($data) . " WHERE id = " . $id;
  $dataops->ops($sql, '连接编辑[' . $id . ']', 1);
}
?>
<!DOCTYPE html>
<html>
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
        <?php
        $res = $db->getRow("SELECT * FROM link WHERE id = " . $id);
        if ($row = $res) {
        ?>
        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">编辑链接<span class="am-icon-chevron-down am-fr"></span></header>
          <form action="" method="post" class="am-form">
            <main class="am-panel-bd am-panel-bordered am-collapse am-in" id="collapse-panel-1">
              <div class="am-form-group">
                <label for="l_name">链接名称</label>
                 <input id="l_name" type="text" name="l_name" value="<?php echo $row['l_name']?>">
              </div>
              <div class="am-form-group">
                <label for="l_picture">链接图片</label>
                <div class="am-input-group">
                  <input name="l_picture" type="text" id="l_picture" class="am-form-field" value="<?php echo $row['l_picture']?>">
                  <span class="am-input-group-btn">
                    <button type="button" class="am-btn am-btn-default" id="l_picture_upload">选择图片</button>
                  </span>
                </div>
              </div>
              <div class="am-form-group">
                <label for="l_link">链接地址</label>
                 <input id="l_link" type="text" name="l_link" value="<?php echo $row['l_link']?>">
              </div>
              <div class="am-form-group">
                <label for="l_order">排序</label>
                 <input id="l_order" type="text" name="l_order" value="<?php echo $row['l_order']?>">
              </div>
              <center>
                <button type="submit" name="submit" id="save" class="am-btn am-btn-default">提交保存</button>
                <input type="hidden" name="act" value="edit">
                <button type="reset" class="am-btn am-btn-default">放弃保存</button>
              </center>
            </main>
          </form>
        </section>
        <?php } ?>
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
    if ($('#l_name').val() == '') {
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
