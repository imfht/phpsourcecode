<?php
include '../library/inc.php';

if ($act == 'adminLogin') {
  times_limit('login_admin_count', 'time_admin'); // 次数限制

  if (strtolower($_POST['vercode']) != $_SESSION["verifycode_admin"]) {
    unset($_SESSION['verifycode_admin']);
    alert_href('验证码错误', 'cms_login.php');
  }
  $a_name = str_safe($_POST['a_name']);
  $a_password = str_safe($_POST['a_password']);
  unset($_SESSION['verifycode_admin']);

  if ($res = $db->getRow("SELECT id,u_name,u_psw FROM user WHERE u_name='$a_name' AND u_isadmin=1")) {

    if (psw_verify($a_password, $res['u_psw'])) {

      limitUserLogin($res['id']); // 用户多终端登陆限制

      setUserToken($res['id'], $res['u_name']); // 设置用户标示
      admin_log('管理员登陆');
      href('index.php?act=welcome');

    } else {
      alert_href('用户名或密码错误', 'cms_login.php');
    }

  } else {
    alert_href('用户名或密码错误', 'cms_login.php');
  }
}

// 处理管理密码
// if ($act == 'initPsw') {
//   $db->query(Sql::parse("UPDATE user SET u_psw='" . psw_hash($_POST['admin_password']) . "' WHERE id = 1"));
// }
?>
<!DOCTYPE html>
<html class="no-js">
<head>
<?php include 'inc/inc_head.php';?>
<style>
  .header {text-align: center;}
  .header h1 {font-size: 200%;color: #333;margin-top: 30px;}
  .header p {font-size: 14px;}
  .hand {cursor: pointer;}
</style>
</head>

<body>
<!--[if lte IE 9 ]><div class="am-alert am-alert-danger ie-warning" data-am-alert><button type="button" class="am-close">&times;</button><div class="am-container">您正在使用<strong>过时</strong>的浏览器，大部分功能暂不支持。 请 <a href="http://browsehappy.com/" target="_blank">升级浏览器</a> 以获得更好的体验！</div></div><![endif]-->

<div class="header">
  <div class="am-g">
    <h1>MCMS系统管理后台</h1>
  </div>
  <hr>
</div>
<div class="am-g">
  <div class=" am-u-sm-centered am-u-md-8 am-u-lg-6">
    <form method="post" class="am-form">
      <div class="am-form-group">
        <label for="a_name">管理员/USER NAME:</label>
        <input type="text" name="a_name" id="a_name" value="">
      </div>
      <div class="am-form-group">
        <label for="a_password">密码/PASSWORD:</label>
        <input type="password" name="a_password" id="a_password" value="">
      </div>
      <div class="am-form-group">
        <div class="am-input-group">
          <input id="vercode" type="text" name="vercode" class="am-form-field" value="">
          <span class="am-input-group-btn">
            <img src="verifycode.php?v=<?php echo rand();?>" onclick="javascript:this.src='verifycode.php?v='+Math.random();" class="hand" title="点击刷新验证码" height="38">
          </span>
        </div>
      </div>
      <div class="am-form-group">
        <input type="submit" name="submit" id="login_submit" value="登 录" class="am-btn am-btn-default am-btn-block"><input type="hidden" name="act" value="adminLogin">
      </div>
    </form>
    <!-- <hr>
    <p>© 2014 石榴木. Licensed under MIT license.</p> -->
  </div>
</div>

<!-- JS -->
<script type="text/javascript">
$(function(){
  $('#login_submit').click(function(){
    if ($('#a_name').val() == '') {
      alert('请填写用户名！');
      $('#a_name').focus();
      return false;
    }
    if ($('#a_password').val() == '') {
      alert('请填写密码！');
      $('#a_password').focus();
      return false;
    }
    if ($('#vercode').val() == '') {
      alert('请填写验证码！');
      $('#vercode').focus();
      return false;
    }
  });
});
</script>
</body>
</html>
