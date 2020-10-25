<?php

$db = null;

function db_connect() {
	//配置变量
	global $config;
	$mysql_server = $config['mysql_server'];
	$mysql_user = $config['mysql_user'];
	$mysql_pwd = $config['mysql_pwd'];
	$mysql_db = $config['mysql_db'];

	$db = mysql_pconnect($mysql_server, $mysql_user, $mysql_pwd);
	if (!$db) die('数据库连接失败');

	$db_select = mysql_select_db($mysql_db, $db);
	if (!$db_select) die('数据库选择失败');

	return $db;
}
function dt_count($table, $cond) {
	$rs = dt_query("SELECT COUNT(*) FROM $table $cond");
	if (!$rs) return false;
	$c = mysql_fetch_array($rs);
	return $c[0];
}
function dt_sum($table, $col, $cond) {
	$rs = dt_query("SELECT SUM($col) FROM $table $cond");
	if (!$rs) return false;
	$s = mysql_fetch_array($rs);
	return $s[0];
}
function dt_query_one($sql) {
	$rs = dt_query($sql);
	if (!$rs) return false;
	$dt = mysql_fetch_array($rs);
	if (empty($dt)) return false;
	return $dt;
}
function dt_query_array($sql) {
	$rs = dt_query($sql);
	if (!$rs) return false;
	$a = array();
	while($row = mysql_fetch_array($rs)) $a[] = $row;
	return $a;
}
function dt_query($sql) {
	global $db;

	//sql安全检查
	if(do_query_safe($sql) < 1) {
		echo '没有通过sql安全检查';
		return false;
	}

	if (null == $db) { $db = db_connect(); }

	$rs = mysql_query($sql, $db);
	if (!$rs) {
		echo mysql_error();
		return false;
	}

	return $rs;
}
function do_query_safe($sql) {   // SQL安全检测，可自动预防SQL注入攻击
	$sql = str_replace(array('\\\\', '\\\'', '\\"', '\'\''), '', $sql);
	$mark = $clean = '';
	if(strpos($sql, '/') === false && strpos($sql, '#') === false && strpos($sql, '-- ') === false) {
		$clean = preg_replace("/'(.+?)'/s", '', $sql);
	} else {
		$len = strlen($sql);
		$mark = $clean = '';
		for ($i = 0; $i <$len; $i++) {
			$str = $sql[$i];
			switch ($str) {
			case '\'':
				if(!$mark) {
					$mark = '\'';
					$clean .= $str;
				} elseif ($mark == '\'') {
					$mark = '';
				}
				break;
			case '/':
				if(empty($mark) && $sql[$i+1] == '*') {
					$mark = '/*';
					$clean .= $mark;
					$i++;
				} elseif($mark == '/*' && $sql[$i -1] == '*') {
					$mark = '';
					$clean .= '*';
				}
				break;
			case '#':
				if(empty($mark)) {
					$mark = $str;
					$clean .= $str;
				}
				break;
			case "\n":
				if($mark == '#' || $mark == '--') {
					$mark = '';
				}
				break;
			case '-':
				if(empty($mark)&& substr($sql, $i, 3) == '-- ') {
					$mark = '-- ';
					$clean .= $mark;
				}
				break;

			default:

				break;
			}
			$clean .= $mark ? '' : $str;
		}
	}

	$clean = preg_replace("/[^a-z0-9_\-\(\)#\*\/\"]+/is", "", strtolower($clean));

	//配置变量
	$dfunction = array('load_file','hex','substring','if','ord','char');
	$daction = array('intooutfile','intodumpfile','unionselect', 'unionall', 'uniondistinct'); //array('intooutfile','intodumpfile','unionselect','(select', 'unionall', 'uniondistinct');
	$dnote = array('/*','*/','#','--','"');
	$dlikehex = 1;
	$afullnote = 0;

	if($afullnote) {
		$clean = str_replace('/**/','',$clean);
	}

	if(is_array($dfunction)) {
		foreach($dfunction as $fun) {
			if(strpos($clean, $fun.'(') !== false) return '-1';
		}
	}

	if(is_array($daction)) {
		foreach($daction as $action) {
			if(strpos($clean,$action) !== false) return '-3';
		}
	}

	if($dlikehex && strpos($clean, 'like0x')) {
		return '-2';
	}

	if(is_array($dnote)) {
		foreach($dnote as $note) {
			if(strpos($clean,$note) !== false) return '-4';
		}
	}

	return 1;
}

