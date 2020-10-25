<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: password_send.php,v 1.5 2008/11/28 10:36:09 alex Exp $
 *
 */
include("include/header.php");

?>
<script language="JavaScript">
<!--
function check1()
{
	var f=document.form1;
	var y='';

	if (!f.email.value) {
		y = '\n<?php echo addslashes(str_replace("@key@", $STRING['email'], $STRING['no_empty']))?>';
		alert(y);
		return false;
	}
	
	return OnSubmit(f);
}

function Redirect(url)
{
	parent.location=url;
}
-->
</script>

<form method="POST" action="password_dosend.php" name="form1" onSubmit="return check1();">
<div id="report_mesg_main">
	<div class="report_mesg_sub">
		<div class="mesg_left">
			<p align="center"><img border="0" src="images/key.png" width="46" height="64"></p>
			<p><?php echo $STRING['forget_password_hint']?></p>
		</div>
		<div class="mesg_right mesg_right1">
            <tt class="outline">
<?php
			if ($_GET['register'] == "y") {
				echo '<input type="hidden" name="register" value="y">';
				echo $STRING['register_account'];
			} else {
				echo $STRING['forget_password_title'];
			}
?>
			</tt>
		</div>
		<div class="mesg_right mesg_right2">
			<table width="100%" height="100%">
			<tr>
				<td>
					<p align="left"><?php echo $STRING['email'].$STRING['colon']?><br>
					<input class="input-form-text-field" type="text" name="email" size="20"></p>
				</td>
			</tr>
			</table>
		</div>
		<div class="mesg_right mesg_right3">
			<input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button" >
			<input type="button" value="<?php echo $STRING['button_cancel']?>" name="B1" class="button" onClick="return Redirect('index.php');">
		</div>
	</div>
</div>
			
</form>
<SCRIPT LANGUAGE="JavaScript">
	document.form1.email.focus();
</script>
<?php
include("include/tail.php");
?>
