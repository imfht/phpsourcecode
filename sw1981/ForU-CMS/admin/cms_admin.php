<?php
$privilege = 'all';
include '../library/inc.php';

if (isset($_GET['del'])) {
  if ($_GET['del'] == 1) {
    alert_back('默认账户不能删除！');
  } else {
    $_GET['del'] = intval($_GET['del']);
    if (isset($_GET['del'])) {
      $sql = "DELETE FROM user WHERE id = " . $_GET['del'];
      $dataops->ops($sql, '管理员删除[' . getUserToken('id') . ']', 1);
    }
  }
}
if ($act == 'add') {
  $a_role = $_POST['a_role'];
  $a_name = $_POST['a_name'];
  $res = $db->getRow("SELECT * FROM user WHERE u_name = '" . $a_name . "'");
  if (is_array($res)) {
    alert_back('登录帐号重名');
  }
  $a_tname = !empty($_POST['a_tname']) ? $_POST['a_tname'] : '';
  $a_password = $_POST['a_password'];
  $a_cpassword = $_POST['a_cassword'];

  null_back($a_name, '请填写登录帐号');
  null_back($a_password, '请填写登录密码');
  if ($a_password == $a_cassword) {
    alert_back('请核对密码和重复密码');
  }

  $sql = "INSERT INTO user (u_rid,u_enable,u_name,u_tname,u_psw,u_isadmin) VALUES ('" . $a_role . "',1,'" . $a_name . "','" . $a_tname . "','" . psw_hash($a_password) . "',1)";
  $dataops->ops($sql, '管理员新增[' . getUserToken('id') . ']', 1);
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
      <div class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">现有管理员<span class="am-icon-chevron-down am-fr"></span></div>
      <div class="am-panel-bd am-collapse am-in am-scrollable-horizonetal" id="collapse-panel-1">
        <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table am-text-nowrap">
          <thead>
          <tr>
          <th>ID</th><th>帐号</th><th>管理</th>
          </tr>
          </thead>
          <tbody>
            <?php
            $res = $db->getAll("SELECT * FROM user WHERE u_isadmin = 1");
            if (!empty($res)) {
              foreach($res as $row){
                echo '<tr><td>' . $row['id'] . '</td><td>' . $row['u_name'] . '</td><td><a href="cms_admin_edit.php?id=' . $row['id'] . '" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-pencil"></span></a> <a href="cms_admin.php?del=' . $row['id'] . '" onclick="return confirm(\'确认要删除吗？\')" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-times"></span></a></td></tr>';
              }
            }
            ?>
          </tbody>
        </table>
        </div>
      </div>

    <section class="am-panel am-panel-default">
      <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-2'}">新增管理员<span class="am-icon-chevron-down am-fr"></span></header>
      <form action="" method="post" class="am-form">
        <main class="am-panel-bd am-panel-bordered am-collapse am-in" id="collapse-panel-2">
          <div class="am-form-group">
          <label for="a_name">登录帐号</label>
          <input id="a_name" type="text" name="a_name">
          </div>
          <div class="am-form-group">
          <label for="a_password">登录密码</label>
          <input name="a_password" type="password" id="a_password" placeholder="密码要求8-16位字符(大小写及数字)" maxlength="16">
          </div>
          <div class="am-form-group">
          <label for="a_cassword">重复密码</label>
          <input name="a_cassword" type="password" id="a_cassword"  maxlength="16">
          </div>
          <div class="am-form-group">
          <label for="a_tname">昵称</label>
          <input name="a_tname" type="text" id="a_tname">
          </div>
          <div class="am-form-group">
          <label for="a_role">权限角色</label>
          <select name="a_role" id="a_role">
            <?php
            $res = $db->getAll("SELECT * FROM role");
            foreach($res as $row) {
              echo '<option value="' . $row['id'] . '">' . $row['r_name'] . '</option>';
            }
            ?>
          </select>
          </div>
            <center>
	      <button type="submit" name="submit" class="am-btn am-btn-primary">提交保存</button>
              <input type="hidden" name="act" value="add">
              <button type="reset" class="am-btn am-btn-primary">放弃保存</button>
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
