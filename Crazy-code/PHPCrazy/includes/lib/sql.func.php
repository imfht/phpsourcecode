<?php

/**
* @package phpBB Forum Software
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* Removes "/* style" as well as "# style" comments from $input.
*
* @param string $input		Input string
*
* @return string			Input string with comments removed
*/
function remove_comments($input)
{
	// Remove /* */ comments (http://ostermiller.org/findcomment.html)
	$input = preg_replace('#/\*(.|[\r\n])*?\*/#', "\n", $input);
	// Remove # style comments
	$input = preg_replace('/\n{2,}/', "\n", preg_replace('/^#.*$/m', "\n", $input));
	return $input;
}

/**
* @package phpBB Forum Software
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* split_sql_file will split an uploaded sql file into single sql statements.
* Note: expects trim() to have already been run on $sql.
*/
function split_sql_file($sql, $delimiter)
{
	$sql = str_replace("\r" , '', $sql);
	$data = preg_split('/' . preg_quote($delimiter, '/') . '$/m', $sql);
	$data = array_map('trim', $data);
	// The empty case
	$end_data = end($data);
	if (empty($end_data))
	{
		unset($data[key($data)]);
	}
	return $data;
}

/*
*	将SQL分隔后存入数组中
*	SQLinArray(SQL, 默认表前缀, 新表前缀, SQL分隔符)
*/
function SQLinArray($sql_query, $prefix, $new_prefix, $delimiter) {

	$sql_query = preg_replace('#' . $prefix . '#i', $new_prefix, $sql_query);

	$sql_query = remove_comments($sql_query);

	$sql_query = split_sql_file($sql_query, $delimiter);

	return $sql_query;

}

/*
*	批量执行SQL
*	RunSQL(SQL, PDO)
*/
function RunSQL($sql_query, &$PDO) {

	$SQLarr = SQLinArray($sql_query, 'crazy_', TABLE_PREFIX, ';');

	foreach ($SQLarr as $sql) {

		$PDO->exec($sql);

	}
}
?>