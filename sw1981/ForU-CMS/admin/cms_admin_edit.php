<?php
$privilege = 'all';
include '../library/inc.php';

if ($act == 'edit') {
  $a_role = intval($_POST['a_role']);
  $a_name = str_safe($_POST['a_name']);
  $a_tname = str_safe($_POST['a_tname']);
  $a_password = str_safe($_POST['a_password']);
  $a_cpassword = str_safe($_POST['a_cassword']);
  $a_npassword = $db->getOne("SELECT u_psw FROM user WHERE id = " . $id);
  if ($a_password == '') {
    $password = $a_npassword;
  } else {
    $password = psw_hash($a_password);
  }
  null_back($a_name, '请填写登录帐号');
  if ($a_password != $a_cpassword) {
    alert_back('请核对密码和重复密码');
  }

  $sql = "UPDATE user SET u_rid=" . $a_role . ",u_name='" . $a_name . "',u_tname='" . $a_tname . "',u_psw='" . $password . "' WHERE id = " . $id;
  $dataops->ops($sql, '管理员编辑['.getUserToken('id').']', 1);
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
        <?php
        $res = $db->
        getRow("SELECT * FROM user WHERE id = " . $id);
        if ($row = $res) {
        ?>
        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}">管理员<span class="am-icon-chevron-down am-fr"></span></header>
          <form action="" method="post" class="am-form">
            <main class="am-panel-bd am-panel-bordered am-collapse am-in" id="collapse-panel-2">
            <div class="am-form-group">
              <label for="a_name">登录帐号</label>
              <input id="a_name" type="text" name="a_name" value="<?php echo $row['u_name']; ?>" readonly>
            </div>
            <div class="am-form-group">
              <label for="a_password">登录密码</label>
              <input name="a_password" type="password" id="a_password" value="" placeholder="密码要求8-16位字符(大小写及数字)" maxlength="16">
              <p class="am-form-help">
                如不需修改请留空
              </p>
            </div>
            <div class="am-form-group">
              <label for="a_cassword">重复密码</label>
              <input name="a_cassword" type="password" id="a_cassword" maxlength="16">
              <p class="am-form-help">
                如不需修改请留空
              </p>
            </div>
            <div class="am-form-group">
              <label for="a_tname">昵称</label>
              <input name="a_tname" type="text" id="a_tname" value="<?php echo $row['u_tname'];?>">
            </div>
            <div class="am-form-group">
              <label for="a_role">权限角色</label>
              <select name="a_role" id="a_role">
                <?php
                if ($row['id']==1) {
                  echo '<option value="1" selected="selected">超级管理员</option>';
                } else {
                  $res = $db->getAll("SELECT * FROM role");
                  foreach($res as $val) {
                    echo '<option value="' . $val['id'] . '" ' . ($val['id']==$row['u_rid'] ? 'selected="selected"' : '') . '>' . $val['r_name'] . '</option>';
                  }
                }
                ?>
              </select>
            </div>
            <center>
              <button type="submit" name="submit" class="am-btn am-btn-default">提交保存</button>
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
  //密码为8位及以上并且字母数字三项都包括
  var strongRegex = new RegExp("^(?![0-9a-z]+$)(?![0-9A-Z]+$)(?![0-9\W]+$)(?![a-z\W]+$)(?![a-zA-Z]+$)(?![A-Z\W]+$)[a-zA-Z0-9\W]{8,16}$", "g");
  $('.am-form').submit(function(){
    if ($('#a_name').val() == '') {
      alert('请填写登录帐号');
      $('#a_name').focus();
      return false;
    }
    if (false == strongRegex.test($('#a_password').val())) {
      $('#a_password').val('');
      $('#a_cassword').val('');
      alert('密码要求8-16位字符(大小写及数字)');
      return false;
    }
    if ($('#a_password').val() != $('#a_cassword').val()) {
      alert('两次密码不一致');
      return false;
    }
  });
});
</script>
</body>
</html>
