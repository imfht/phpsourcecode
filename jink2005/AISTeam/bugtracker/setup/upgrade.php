<?php
include("../include/header.php");
$setup_steps = array(
		"upgrade_welcome.php",
        "check_env.php",
        "upgrade_sql.php",
        "setup_string.php",
		"upgrade_version.php",
);

if ($_POST['step']) {
	$_GET['step'] = $_POST['step'];
}
?>
<div id="current_location">
	<b>Current Location:</b> /
	Upgrade Wizard
	<?php if (isset($_GET['step'])) echo " / Step ".$_GET['step']." of ".(sizeof($setup_steps)-1);?>
</div>
<div id="main_container">

<div id="sub_container" style="width: 98%;">
	<table width="100%" border="0">
		<tr>
			<td align="left" nowrap>
				<tt class="outline">Bug Tracker Upgrade Wizard</tt>
			</td>
		</tr>
	</table>
	<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
		<h3>&nbsp;</h3>

<?php
if (!isset($_GET['step'])) {
	$_GET['step'] = 0;
}
$error = 0;
if (isset($_POST['password'])) {
        $sql="select * from ".$GLOBALS['BR_user_table']." where username='admin' and password='".md5($_POST['password'])."'";
        $sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
        $line = $sql_result->RecordCount();
        if ($line != 1) {
                ErrorPrintOut("auth_failed");
        }
        $_SESSION['reg_upgrade_allowed'] = 1;
}
if ($_GET['step'] > 1 && !$_SESSION['reg_upgrade_allowed']) {
?>
<form action="<?php echo $_SERVER['PHP_SELF']?>" name="form1" method="POST">
	<input type="hidden" name="step" value="2">
	<p align="center">Please enter the admin password:
	<input type="password" name="password" size="20" class="input-form-text-field"></p>
	<br>
	<p align="center"><input type="submit" value="Next Step &gt; &gt;" name="B1" class="button">
	</form>
<?php
} else {
	include($setup_steps[$_GET['step']]);

	if ($error != 0) {
		echo '<form method="GET" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return OnSubmit(this);" name="form1">';
		echo '<input type=hidden name="step" value="'.($_GET['step']).'">';
		echo '<p align="center"><input type="submit" name="next" value="Retest" class="button"></p>';
		echo '</form>';
	} else {
		if ($_GET['step'] == (sizeof($setup_steps)-1)) {
			echo '<p>Congratulations!!</p><p>The upgrade is finished.</p>';
			echo "<p align=\"center\"><a href=\"".$GLOBALS["SYS_URL_ROOT"]."/index.php\">Login Bug Tracker</a></p>";
			echo '<h2 align=center><font color="red">Please remember to remove setup/*.php after installation</font><h1>';
			echo '<h2 align=center><font color="red">Please do not remove the whoe setup directory. Remove *.php only.</font><h1>';
		} else {
			echo '<br><form method="GET" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return OnSubmit(this);" name="form1">';
			echo '<input type=hidden name="step" value="'.($_GET['step']+1).'">';
			echo '<p align="center"><input type="submit" name="next" value="Next Step &gt; &gt;" class="button"></p>';
			echo '</form>';
		}
	}
}
?>

</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php
include("../include/tail.php");
?>
