<?php
function LoginBoxPrint()
{
	global $STRING;
	global $SYSTEM;

?>
<script language="javascript" type="text/javascript">
function AuthSend(form)
{
	ALEXWANG.Misc.DocumentMask(true, '<?php echo $STRING['loading']?>');
	
	document.getElementById("login_error_message").innerHTML = '';

	ALEXWANG.Ajax.request({
		url: '<?php echo $GLOBALS["SYS_URL_ROOT"]?>/login.php',
		method: "POST",
		param: {
			username: form.username.value,
			password: form.password.value
		},
		callback: function (options, success, response) {
			 if (success) {
				 if (response == 1) {
					 document.ReloadForm.submit();
				 } else {
					 document.getElementById("login_error_message").innerHTML = response;
					 ALEXWANG.Misc.DocumentUnMask();
				 }
			 } else {
				 ALEXWANG.Misc.DocumentUnMask();
			 }
		},
		scope: this
	});

	return false;
}
</script>

<form method="POST" action="login.php" name="LoginForm" onsubmit="return AuthSend(this);">
<div id="login_box_main">
	<div class="login_box_title"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/encrypted.gif"></div>
	<div class="login_box_sub">
		<div class="login_left">
			<font class="login_title"><?php echo $STRING['msg_title_login']?></font></p>
			<font size="2" color="#666666">
				<p><?php echo str_replace("@key@", $SYSTEM['program_name'], $STRING['login_hint'])?></p>
			</font>
			<br>
			<div id="login_error_message"></div>
		</div>
		<div class="login_right">
			<table width="100%" height="100%">
			<tr>
				<td>
					<p><?php echo $STRING['username'].$STRING['colon']?><br>
					<input class="input-form-text-field" type="text" name="username" size="20"></p>
					<p><?php echo $STRING['password'].$STRING['colon']?><br>
					<input class="input-form-text-field" type="password" name="password" size="20"></p>
					<input type="submit" value="<?php echo $STRING['login']?>" name="B1" class="button">
				</td>
			</tr>
			</table>
		</div>
	</div>
</div>
</form>
<script language="JavaScript" type="text/javascript">
	document.LoginForm.username.focus();
</script>
<?php
}

function AuthCheckAndLogin()
{
	$LoginFailed = FALSE;

	if (isset($_SESSION[SESSION_PREFIX.'uid']) && isset($_SESSION[SESSION_PREFIX.'gid']) &&
		isset($_SESSION[SESSION_PREFIX.'username'])) {
		return;
	}
	if ($_POST) {
		$arglist = $_POST;
		$method = "POST";
	} else {
		$arglist = $_GET;
		$method = "GET";
	}
	include_once("header.php");

	echo '<form method="'.$method.'" action="'.$_SERVER['PHP_SELF'].'" name="ReloadForm">';
	$args = array_keys($arglist);
	for ($i = 0; $i < sizeof($args); $i++) {
		$name = $args[$i];
		$value = $arglist[$args[$i]];
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		//$value = str_replace('"', '&quot;', $value);
		$value = htmlspecialchars($value);
		echo '
			<input type="hidden" name="'.$name.'" value="'.$value.'">';
	}
	echo '</form>';

	LoginBoxPrint();

	include("tail.php");
	exit();
}
?>
