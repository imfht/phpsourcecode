<?php

/**
 * 测试目录写仅限
 * @param type $dir
 * @return boolean
 */
function testwrite($dir) {
	$tfile = "_test.txt";
	$fp = @fopen($dir . "/" . $tfile, "w");
	if (!$fp) {
		return false;
	}
	fclose($fp);
	$rs = @unlink($dir . "/" . $tfile);
	if ($rs) {
		return true;
	}
	return false;
}

/**
 * SQL执行函数
 * @param type $sql
 * @param type $tablepre
 * @return boolean
 */
function sql_execute($sql, $tablepre) {
	$sqls = sql_split($sql, $tablepre);
	if (is_array($sqls)) {
		foreach ($sqls as $sql) {
			if (trim($sql) != '') {
				mysql_query($sql);
			}
		}
	} else {
		mysql_query($sqls);
	}
	return true;
}

/**
 * SQL分割
 * @param type $sql SQL语句
 * @param type $tablepre
 * @return type
 */
function sql_split($sql, $tablepre) {
	if ($tablepre != "sp_") {
		$sql = str_replace("sp_", $tablepre, $sql);
	}
	$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
	if ($r_tablepre != $s_tablepre) {
		$sql = str_replace($s_tablepre, $r_tablepre, $sql);
	}
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach ($queriesarray as $query) {
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		$queries = array_filter($queries);
		foreach ($queries as $query) {
			$str1 = substr($query, 0, 1);
			if ($str1 != '#' && $str1 != '-') {
				$ret[$num] .= $query;
			}
		}
		$num++;
	}
	return $ret;
}

/**
 * 递归创建目录
 * @param type $path 需要创建的目录
 * @param type $mode 目录权限
 * @return boolean
 */
function dir_create($path, $mode = 0777) {
	if (is_dir($path)) {
		return TRUE;
	}
	$ftp_enable = 0;
	$path = dir_path($path);
	$temp = explode('/', $path);
	$cur_dir = '';
	$max = count($temp) - 1;
	for ($i = 0; $i < $max; $i++) {
		$cur_dir .= $temp[$i] . '/';
		if (is_dir($cur_dir)) {
			continue;
		}
		@mkdir($cur_dir, $mode, true);
		chmod($cur_dir, $mode);
	}
	return is_dir($path);
}

/**
 * 目录分割号转换
 * @param type $path
 * @return string
 */
function dir_path($path) {
	$path = str_replace('\\', '/', $path);
	if (substr($path, -1) != '/') {
		$path = $path . '/';
	}
	return $path;
}

/**
 * 获取随机数
 * @param type $length 随机数长度
 * @return string
 */
function sp_random_string($length = 6) {
	$chars = array(
		"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
		"l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
		"w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
		"H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
		"S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
		"3", "4", "5", "6", "7", "8", "9"
	);
	$charsLen = count($chars) - 1;
	shuffle($chars); // 将数组打乱
	$output = "";
	for ($i = 0; $i < $length; $i++) {
		$output .= $chars[mt_rand(0, $charsLen)];
	}
	return $output;
}
