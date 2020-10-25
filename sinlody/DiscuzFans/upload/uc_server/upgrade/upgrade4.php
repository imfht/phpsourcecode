<?php

/*
	[UCenter] (C)2001-2099 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: upgrade4.php 33333 2013-05-28 09:10:48Z pmonkey_w $
*/

define("IN_UC", TRUE);
define('UC_ROOT', realpath('..').'/');

$version_old = 'UCenter 1.6.0';
$version_new = 'UCenter 1.7.0';
$lock_file = UC_ROOT.'./data/upgrade.lock';

require UC_ROOT.'./data/config.inc.php';
if(function_exists("mysql_connect")) {
	require UC_ROOT.'./lib/db.class.php';
} else {
	require UC_ROOT.'./lib/dbi.class.php';
}
error_reporting(0);
if(PHP_VERSION < '5.3.0') {
    @set_magic_quotes_runtime(0);
}
$PHP_SELF = htmlspecialchars($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);

$action = getgpc('action');
$forward = getgpc('forward');

$sql = <<<EOT
ALTER TABLE uc_members ADD COLUMN sms char(15) NOT NULL DEFAULT '' AFTER email;
ALTER TABLE uc_members ADD KEY sms(sms);
REPLACE INTO uc_settings(k, v) VALUES ('mdoublee','0');
REPLACE INTO uc_settings(k, v) VALUES ('accesssms','');
REPLACE INTO uc_settings(k, v) VALUES ('censorsms','');
REPLACE INTO uc_settings(k, v) VALUES ('smstype', '1');
REPLACE INTO uc_settings(k, v) VALUES ('smsauth_username', '');
REPLACE INTO uc_settings(k, v) VALUES ('smsauth_passwd', '');
REPLACE INTO uc_settings(k, v) VALUES ('smssilent', '1');
REPLACE INTO uc_settings(k, v) VALUES ('smstemplate', '');
REPLACE INTO uc_settings (k, v) VALUES ('version','1.7.0');

DROP TABLE IF EXISTS uc_smsqueue;
CREATE TABLE uc_smsqueue (
  smsid int(10) unsigned NOT NULL auto_increment,
  touid mediumint(8) unsigned NOT NULL default '0',
  tosms varchar(32) NOT NULL default '',
  message text NOT NULL,
  charset varchar(15) NOT NULL default '',
  level tinyint(1) NOT NULL default '1',
  dateline int(10) unsigned NOT NULL default '0',
  failures tinyint(3) unsigned NOT NULL default '0',
  appid smallint(6) unsigned NOT NULL default '0',
  temptype varchar(30) NOT NULL default '',
  tempsign varchar(50) NOT NULL default '',
  template varchar(500) NOT NULL default '',
  PRIMARY KEY  (smsid),
  KEY appid (appid),
  KEY level (level,failures)
) TYPE=MyISAM;
EOT;

if(file_exists($lock_file)) {
	showheader();
	showerror('升级被锁定，应该是已经升级过了，如果已经恢复数据请手动删除<br />'.str_replace(UC_ROOT, '', $lock_file).'<br />之后再来刷新页面');
	showfooter();
}

if(!$action) {

	showheader();

?>

	<p>本程序用于升级 UCenter 1.6.0 到 UCenter 1.7.0</p>
	<p>运行本升级程序之前，请确认已经上传 UCenter 1.7.0 的全部文件和目录</p>
	<p>强烈建议您升级之前备份数据库资料</p>
	<p><a href="<?php echo $PHP_SELF;?>?action=db">如果您已确认完成上面的步骤,请点这里升级</a></p>

<?php
	showfooter();

} elseif($action == 'db') {


	@touch(UC_ROOT.'./data/install.lock');
	@unlink(UC_ROOT.'./install/index.php');

	$db = new ucserver_db();
	$db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, UC_DBCHARSET);

	runquery($sql);
	dir_clear(UC_ROOT.'./data/view');
	dir_clear(UC_ROOT.'./data/cache');
	if(is_dir(UC_ROOT.'./plugin/setting')) {
		dir_clear(UC_ROOT.'./plugin/setting');
		@unlink(UC_ROOT.'./plugin/setting/index.htm');
		@rmdir(UC_ROOT.'./plugin/setting');
	}

	header("Location: upgrade4.php?action=ok");

} elseif($action == 'ok') {

	showheader();

	echo "<h4>升级完成</h4>";

	@touch($lock_file);
	echo "升级完成。";

	showfooter();
}

function dir_clear($dir) {
	$directory = dir($dir);
	while($entry = $directory->read()) {
		$filename = $dir.'/'.$entry;
		if(is_file($filename)) {
			@unlink($filename);
		}
	}
	@touch($dir.'/index.htm');
	$directory->close();
}

function createtable($sql, $dbcharset) {
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
	(mysql_get_server_info() > '4.1' ? " ENGINE=$type default CHARSET=".UC_DBCHARSET : " TYPE=$type");
}

function runquery($query) {
	global $db;

	$query = str_replace("\r", "\n", str_replace(' uc_', ' '.UC_DBTABLEPRE, $query));
	$expquery = explode(";\n", $query);

	foreach($expquery as $sql) {
		$sql = trim($sql);
		if($sql == '' || $sql[0] == '#') continue;

		if(strtoupper(substr($sql, 0, 12)) == 'CREATE TABLE') {
			$db->query(createtable($sql, UC_DBCHARSET));
		} elseif (strtoupper(substr($sql, 0, 11)) == 'ALTER TABLE') {
			runquery_altertable($sql);
		} else {
			$db->query($sql);
		}
	}
}

function getgpc($k, $var='R') {
	switch($var) {
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		case 'R': $var = &$_REQUEST; break;
	}
	return isset($var[$k]) ? $var[$k] : NULL;
}

function showheader() {
	global $version_old, $version_new;
	ob_start();
	$charset = UC_CHARSET;
	@header("Content-Type:text/html;charset=".$charset);
	print <<< EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=$charset" />
<title>UCenter 升级程序( $version_old &gt;&gt; $version_new)</title>
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<meta http-equiv="MSThemeCompatible" content="Yes">
<style>
a:visited	{color: #FF0000; text-decoration: none}
a:link		{color: #FF0000; text-decoration: none}
a:hover		{color: #FF0000; text-decoration: underline}
body,table,td	{color: #3a4273; font-family: Tahoma, verdana, arial; font-size: 12px; line-height: 20px; scrollbar-base-color: #e3e3ea; scrollbar-arrow-color: #5c5c8d}
input		{color: #085878; font-family: Tahoma, verdana, arial; font-size: 12px; background-color: #3a4273; color: #ffffff; scrollbar-base-color: #e3e3ea; scrollbar-arrow-color: #5c5c8d}
.install	{font-family: Arial, Verdana; font-size: 14px; font-weight: bold; color: #000000}
.header		{font: 12px Tahoma, Verdana; font-weight: bold; background-color: #3a4273 }
.header	td	{color: #ffffff}
.red		{color: red; font-weight: bold}
.bg1		{background-color: #e3e3ea}
.bg2		{background-color: #eeeef6}
</style>
</head>

<body bgcolor="#3A4273" text="#000000">
<script type="text/javascript">
	function redirect(url) {
		window.location=url;
	}
</script>
<table width="95%" height="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
<tr>
<td>
<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td class="install" height="30" valign="bottom"><font color="#FF0000">&gt;&gt;</font>
UCenter  升级程序( $version_old &gt;&gt; $version_new)</td>
</tr>
<tr>
<td>
<hr noshade align="center" width="100%" size="1">
</td>
</tr>
<tr>
<td align="center">
<b>本升级程序只能从 $version_old 升级到 $version_new ，运行之前，请确认已经上传所有文件，并做好数据备份<br />
升级当中有任何问题请访问技术支持站点 <a href="http://www.discuzf.com" target="_blank">http://www.discuzf.com</a></b>
</td>
</tr>
<tr>
<td>
<hr noshade align="center" width="100%" size="1">
</td>
</tr>
<tr><td>
EOT;
}

function showfooter() {
	echo <<< EOT
</td></tr></table></td></tr>
<tr><td height="100%">&nbsp;</td></tr>
</table>
</body>
</html>
EOT;
	ob_flush();
	exit();
}

function showerror($message, $break = 1) {
	echo '<br /><br />'.$message.'<br /><br />';
	if($break) showfooter();
}

function redirect($url) {

	$url = $url.(strstr($url, '&') ? '&' : '?').'t='.time();

	echo <<< EOT
<hr size=1>
<script language="JavaScript">
	function redirect() {
		window.location.replace('$url');
	}
	setTimeout('redirect();', 1000);
</script>
<br /><br />
&gt;&gt;<a href="$url">浏览器会自动跳转页面，无需人工干预。除非当您的浏览器长时间没有自动跳转时，请点击这里</a>
<br /><br />
EOT;
	showfooter();
}

function get_table_columns($table) {
	global $db;
	$tablecolumns = array();
	if($db->version() > '4.1') {
		$query = $db->query("SHOW FULL COLUMNS FROM $table", 'SILENT');
	} else {
		$query = $db->query("SHOW COLUMNS FROM $table", 'SILENT');
	}
	while($field = @$db->fetch_array($query)) {
		$tablecolumns[$field['Field']] = $field;
	}
	return $tablecolumns;
}

function parse_alter_table_sql($s) {
	$arr = array();
	preg_match("/ALTER TABLE (\w+)/i", $s, $m);
	$tablename = substr($m[1], strlen(UC_DBTABLEPRE));
	preg_match_all("/add column (\w+) ([^\n;]+)/is", $s, $add);
	preg_match_all("/drop column (\w+)([^\n;]*)/is", $s, $drop);
	preg_match_all("/change (\w+) ([^\n;]+)/is", $s, $change);
	preg_match_all("/add key ([^\n;]+)/is", $s, $keys);
	preg_match_all("/add unique ([^\n;]+)/is", $s, $uniques);
	foreach($add[1] as $k => $colname) {
		$attr = preg_replace("/(.+),$/", "\\1", trim($add[2][$k]));
		$arr[] = array($tablename, 'ADD', $colname, $attr);
	}
	foreach($drop[1] as $k => $colname) {
		$attr = preg_replace("/(.+),$/", "\\1", trim($drop[2][$k]));
		$arr[] = array($tablename, 'DROP', $colname, $attr);
	}
	foreach($change[1] as $k => $colname) {
		$attr = preg_replace("/(.+),$/", "\\1", trim($change[2][$k]));
		$arr[] = array($tablename, 'CHANGE', $colname, $attr);
	}
	foreach($keys[1] as $k => $colname) {
		$attr = preg_replace("/(.+),$/", "\\1", trim($keys[0][$k]));
		$arr[] = array($tablename, 'INDEX', '', $attr);
	}
	foreach($uniques[1] as $k => $colname) {
		$attr = preg_replace("/(.+),$/", "\\1", trim($uniques[0][$k]));
		$arr[] = array($tablename, 'INDEX', '', $attr);
	}
	return $arr;
}

function runquery_altertable($sql) {
	global $db;
	$tablepre = UC_DBTABLEPRE;
	$dbcharset = UC_DBCHARSET;

	$updatesqls = parse_alter_table_sql($sql);

	foreach($updatesqls as $updatesql) {
		$successed = TRUE;

		if(is_array($updatesql) && !empty($updatesql[0])) {

			list($table, $action, $field, $sql) = $updatesql;

			if(empty($field) && !empty($sql)) {

				$query = "ALTER TABLE {$tablepre}{$table} ";
				if($action == 'INDEX') {
					$successed = $db->query("$query $sql", "SILENT");
				} elseif ($action == 'UPDATE') {
					$successed = $db->query("UPDATE {$tablepre}{$table} SET $sql", 'SILENT');
				}

			} elseif($tableinfo = get_table_columns($tablepre.$table)) {

				$fieldexist = isset($tableinfo[$field]) ? 1 : 0;

				$query = "ALTER TABLE {$tablepre}{$table} ";

				if($action == 'MODIFY') {

					$query .= $fieldexist ? "MODIFY $field $sql" : "ADD $field $sql";
					$successed = $db->query($query, 'SILENT');

				} elseif($action == 'CHANGE') {

					$field2 = trim(substr($sql, 0, strpos($sql, ' ')));
					$field2exist = isset($tableinfo[$field2]);

					if($fieldexist && ($field == $field2 || !$field2exist)) {
						$query .= "CHANGE $field $sql";
					} elseif($fieldexist && $field2exist) {
						$db->query("ALTER TABLE {$tablepre}{$table} DROP $field2", 'SILENT');
						$query .= "CHANGE $field $sql";
					} elseif(!$fieldexist && $fieldexist2) {
						$db->query("ALTER TABLE {$tablepre}{$table} DROP $field2", 'SILENT');
						$query .= "ADD $sql";
					} elseif(!$fieldexist && !$field2exist) {
						$query .= "ADD $sql";
					}
					$successed = $db->query($query);

				} elseif($action == 'ADD') {

					$query .= $fieldexist ? "CHANGE $field $field $sql" :  "ADD $field $sql";
					$successed = $db->query($query);

				} elseif($action == 'DROP') {
					if($fieldexist) {
						$successed = $db->query("$query DROP $field", "SILENT");
					}
					$successed = TRUE;
				}

			} else {

				$successed = 'TABLE NOT EXISTS';

			}
		}
	}
	return $successed;
}

?>