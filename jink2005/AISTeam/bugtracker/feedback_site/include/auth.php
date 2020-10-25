<?php
function LoginBoxPrint()
{
	global $STRING;
	global $FEEDBACK_SYSTEM;
?>
<script language="javascript" type="text/javascript">
function AuthSend(form)
{
	ALEXWANG.Misc.DocumentMask(true, '<?php echo $STRING['loading']?>');
	
	document.getElementById("login_error_message").innerHTML = '';

	ALEXWANG.Ajax.request({
		url: 'login.php',
		method: "POST",
		param: {
			email: form.email.value,
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
	<div class="login_box_title"><img src="images/encrypted.gif"></div>
	<div class="login_box_sub">
		<div class="login_left">
			<font class="login_title"><?php echo $STRING['msg_title_login']?></font></p>
              <font size="2" color="#666666">
			  <?php
				echo "<p>".str_replace("@key@", $FEEDBACK_SYSTEM['feedback_system_name'], $STRING['feedback_login_hint'])."</p>";
				if (($FEEDBACK_SYSTEM['login_mode'] == "mode_anonymous") || ($FEEDBACK_SYSTEM['login_mode'] == "mode_both")){
					echo "<p>".$STRING['no_account_yet'];
					echo "<a href=\"password_send.php?register=y\">".$STRING['register_account']."</a>";
				}
				echo "</p>";
			  ?>
			  </font>
			  <div id="login_error_message"></div>
		</div>
		<div class="login_right">
			<table width="100%" height="100%">
			<tr>
				<td>
					<p><?php echo $STRING['email'].$STRING['colon']?><br>
						<input class="input-form-text-field" type="text" name="email" size="20">
					</p>
					<p>
						<?php echo $STRING['password'].$STRING['colon']?><br>
						<input class="input-form-text-field" type="password" name="password" size="20">
					</p>
						<input type="submit" value="<?php echo $STRING['login']?>" name="B1" class="button">
						<a href="password_send.php"><?php echo $STRING['forget_password']?></a>
					</p>
				</td>
			</tr>
			</table>
		</div>
	</div>
</div>
</form>
<script language="JavaScript" type="text/javascript">
<!--
	document.LoginForm.email.focus();
-->
</script>
<?php
}

function AuthCheckAndLogin()
{
	$LoginFailed = FALSE;

	if (isset($_SESSION[SESSION_PREFIX.'feedback_uid']) && isset($_SESSION[SESSION_PREFIX.'feedback_customer']) &&
		isset($_SESSION[SESSION_PREFIX.'feedback_email'])) {
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

