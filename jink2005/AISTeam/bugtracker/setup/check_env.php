<?php
function PrintCheckEnvMesg()
{
?>
		<p>Before we start, please make sure you have edited the <font color="green"><b>
		include/config.php</b></font> and setup the
		following information:</p>
		<ul>
		<li>IP Address of database server</li>
		<li>User name to access the database</li>
		<li>Database password</li>
		<li>Database name for bug tracker</li>
		<li>Real path of bug tracker project</li>
		<li>HTML URL path of Bug Tracker</li>
		</ul>

		<p>The script will now make sure your server is capable for running Bug Tracker:</p>
<?php
}

function SetupCheckEnv()
{
	include("../include/config.php");
	$error_project_path = 0;
	$error_url_root = 0;
	$error_php_version = 0;
	$error_database = 0;

	// Path of Bug Tracker
	$my_path = $GLOBALS['SYS_PROJECT_PATH']."/setup/index.php";
	if (!stat($my_path)) {
		$error_project_path = 1;
	}

	// URL ROOT
	$url_root = $GLOBALS["SYS_URL_ROOT"];
	if (substr($url_root, -1, 1) == "/") {
		$url_root = substr($url_root, 0, -1);
	}
	if (strncmp($url_root."/", $_SERVER['PHP_SELF'], strlen($url_root)+1) != 0) {
		$error_url_root = 1;
	}

	// PHP version
	$phpversion = phpversion();
	if ($phpversion < '5.0.0') {
		$error_php_version = 1;
	}

	// Database type
	if ( !(($GLOBALS['BR_dbtype'] == "postgres") && function_exists('pg_connect')) &&
		 !(strstr($GLOBALS['BR_dbtype'], "mysql") && function_exists('mysql_pconnect'))) {
		$error_database = 1;
	}

	// Print outputs
	$STRING_PASS = '<font color="green"><b>PASSED!</b></font>';
	$STRING_FAIL = '<font color="red"><b>FAILED</b></font></li>';

	$hint = "";
	echo "<ul>";
	echo "<li>Real path of bug tracker: ".($error_project_path?$STRING_FAIL:$STRING_PASS)."</li>";
	echo "<li>Path of URL root: ".($error_url_root?$STRING_FAIL:$STRING_PASS)."</li>";
	echo "<li>PHP version ($phpversion): ".($error_php_version?$STRING_FAIL:$STRING_PASS)."</li>";
	echo "<li>Database support: ".($error_database?$STRING_FAIL:$STRING_PASS)."</li>";
	echo "</ul>";

	if ($error_project_path || $error_url_root || $error_php_version || $error_database) {
		echo "Failed to pass system tests. Please contact your host and request that they:";
		echo "<ul>";
		if ($error_project_path) {
			echo "<li>Please edit the \$GLOBALS[\"SYS_PROJECT_PATH\"] in include/config.php.</li>\n";
		}
		if ($error_url_root) {
			echo "<li>Please edit the \$GLOBALS[\"SYS_URL_ROOT\"] in include/config.php.</li>\n";
		}
		if ($error_php_version) {
			echo "<li>Upgrade PHP to at least 5.0.0.</li>\n";
		}
		if ($error_database) {
			echo "<li>Make sure the \$GLOBALS['BR_dbtype'] is set to pgsql or mysqlt and PHP supports PostgreSQL or MySQL</li>";
		}
		return -1;
	}
	return 0;
}

PrintCheckEnvMesg();
$error = SetupCheckEnv();
?>

