<?php /* Smarty version Smarty-3.1.21-dev, created on 2014-12-30 16:01:44
         compiled from "D:\wwwroot\blog\templates\login.html" */ ?>
<?php /*%%SmartyHeaderCode:700754a2b0184c9e22-77569594%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3dd4d3da9cab0b492d11f31136465979946cd033' => 
    array (
      0 => 'D:\\wwwroot\\blog\\templates\\login.html',
      1 => 1419951683,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '700754a2b0184c9e22-77569594',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54a2b018508638_93771865',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a2b018508638_93771865')) {function content_54a2b018508638_93771865($_smarty_tpl) {?><html>
<!DOCTYPE html>
<html lang="en" class="no-js">

    <head>

        <meta charset="utf-8">
        <title>登录(Login)</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- CSS -->
        <link rel="stylesheet" href="../templates/assets/css/reset.css">
        <link rel="stylesheet" href="../templates/assets/css/supersized.css">
        <link rel="stylesheet" href="../templates/assets/css/style.css">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <?php echo '<script'; ?>
 src="assets/js/html5.js"><?php echo '</script'; ?>
>
        <![endif]-->

    </head>

    <body>

        <div class="page-container">
            <h1>登录(Login)</h1>
            <form action="./admin.php" method="post" name = "myform">
                <input type="text" name="username" class="username" placeholder="请输入您的用户名！">
                <input type="password" name="password" class="password" placeholder="请输入您的用户密码！">
                <input type="Captcha" class="Captcha" name="Captcha" placeholder="请输入验证码！">
				<div style = "width:70px;height:20px;float:left;margin-top:36px;margin-left:8px;"><img src = "../admin/code_char.php" alt = '看不清？' name = "yzm" onClick="this.src=this.src+'?'+Math.random()" /></div>
                <input type="submit" class="submit_button" name = "sub" value = "登 录" id = "button">
                <div class="error"><span>+</span></div>
            </form>
        </div>
		
        <!-- Javascript -->
        <?php echo '<script'; ?>
 src="../templates/assets/js/jquery-1.8.2.min.js" ><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="../templates/assets/js/supersized.3.2.7.min.js" ><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="../templates/assets/js/supersized-init.js?te" ><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="../templates/assets/js/scripts.js" ><?php echo '</script'; ?>
>

    </body>
<div style="text-align:center;">
</div>
</html>

<?php }} ?>
