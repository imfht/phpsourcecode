<?php
include '../library/inc.php';

if ($act == 'edit') {
  $data['c_code'] = $_POST['c_code'];
  $data['c_name'] = $_POST['c_name'];
  $data['c_content'] = $_POST['c_content'];
  $data['c_safe'] = $_POST['c_safe'];
  null_back($data['c_name'], '请填写碎片名称！');

  $sql = "UPDATE chip SET " . arr_update($data) . " WHERE id = " . $id;
  $dataops->ops($sql, '碎片编辑[' . $_GET['id'] . ']', 1);
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
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}">编辑碎片<span class="am-icon-chevron-down am-fr"></span></header>
          <?php
          $res = $db->getRow("SELECT * FROM chip WHERE id = " . $id);
          if ($row = $res) {
          ?>
          <form action="" method="post" class="am-form">
            <main class="am-panel-bd am-panel-bordered am-collapse am-in" id="collapse-panel-2">
              <div class="am-form-group">
                <label for="c_name">碎片名称</label>
                 <input id="c_name" type="text" name="c_name" value="<?php echo $row['c_name']?>">
              </div>
              <div class="am-form-group">
                <label for="c_code">调用代码</label>
                 <input id="c_code" type="text" name="c_code" value="<?php echo $row['c_code']?>">
              </div>
              <div class="am-form-group">
                <label for="c_content">碎片内容</label>
                 <textarea id="c_content" name="c_content" class="editor"><?php echo htmlspecialchars(stripslashes($row['c_content']))?></textarea>
              </div>
              <div class="am-form-group">
                <label for="">安全保护</label>
                <div>
                  <label class="am-btn am-btn-default <?php echo $row['c_safe']?'am-active':'';?>">
                    <input type="radio" name="c_safe" value="1" <?php echo $row['c_safe']?'checked="checked"':'';?>/> 是
                  </label>
                  <label class="am-btn am-btn-default">
                    <input type="radio" name="c_safe" value="0" <?php echo $row['c_safe'] == 0?'checked="checked"':'';?>/> 否
                  </label>
                </div>
              </div>
              <center>
                <button type="submit" name="submit" id="save" class="am-btn am-btn-default">提交保存</button>
                <input type="hidden" name="act" value="edit">
              </center>
            </main>
          </form>
          <?php } ?>
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
