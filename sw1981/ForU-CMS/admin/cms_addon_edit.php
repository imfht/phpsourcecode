<?php
$privilege = 'addon';
include '../library/inc.php';

if ($act == 'edit') {
  $data['a_name'] = str_safe($_POST['a_name']);
  $data['a_func'] = str_safe($_POST['a_func']);
  $data['a_desc'] = str_safe($_POST['a_desc']);
  $data['a_order'] = intval($_POST['a_order']);
  $data['a_enable'] = intval($_POST['a_enable']);
  $data['a_safe'] = intval($_POST['a_safe']);
  null_back($data['a_name'], '请填写插件名称！');

  $sql = "UPDATE addon SET " . arr_update($data) . " WHERE id = " . $id;
  $dataops->ops($sql, '插件编辑[' . $id . ']', 1);
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
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}">编辑插件<span class="am-icon-chevron-down am-fr"></span></header>
          <?php
          $res = $db->getRow("SELECT * FROM addon WHERE id = " . $id);
          if ($row = $res) {
          ?>
          <form action="" method="post" class="am-form">
            <main class="am-panel-bd am-panel-bordered am-collapse am-in" id="collapse-panel-2">
              <div class="am-form-group">
                <label for="a_name">插件名称</label>
                 <input id="a_name" type="text" name="a_name" value="<?php echo $row['a_name']?>">
              </div>
              <div class="am-form-group">
                <label for="a_func">调用函数</label>
                 <input id="a_func" type="text" name="a_func" value="<?php echo $row['a_func']?>">
              </div>
              <div class="am-form-group">
                <label for="a_desc">插件描述</label>
                 <textarea id="a_desc" name="a_desc"><?php echo htmlspecialchars(stripslashes($row['a_desc']))?></textarea>
              </div>
              <div class="am-form-group">
                <label for="a_order">排序</label>
                 <input id="a_order" type="text" name="a_order" value="<?php echo $row['a_order']?>">
              </div>
              <div class="am-form-group">
                <label for="">生效</label>
                <div>
                  <label class="am-btn am-btn-default <?php echo $row['a_safe']?'am-active':'';?>">
                    <input type="radio" name="a_enable" value="1" <?php echo $row['a_enable']?'checked="checked"':'';?>/> 是
                  </label>
                  <label class="am-btn am-btn-default">
                    <input type="radio" name="a_enable" value="0" <?php echo $row['a_enable'] == 0?'checked="checked"':'';?>/> 否
                  </label>
                </div>
              </div>
              <div class="am-form-group">
                <label for="">安全保护</label>
                <div>
                  <label class="am-btn am-btn-default <?php echo $row['a_safe']?'am-active':'';?>">
                    <input type="radio" name="a_safe" value="1" <?php echo $row['a_safe']?'checked="checked"':'';?>/> 是
                  </label>
                  <label class="am-btn am-btn-default">
                    <input type="radio" name="a_safe" value="0" <?php echo $row['a_safe'] == 0?'checked="checked"':'';?>/> 否
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
      if ($('#a_name').val() == ''){
        alert('请填写插件名称');
        $('#a_name').focus();
        return false;
      }
    });
  });
</script>
</body>
</html>
