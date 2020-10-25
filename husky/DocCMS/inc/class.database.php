<?php
//  DocCms DB Class

//  ORIGINAL CODE FROM:
//  Justin Vincent (justin@visunet.ie)
//	http://php.justinvincent.com

define('EZSQL_VERSION', 'DT3.4');
define('OBJECT', 'OBJECT', true);
define('ARRAY_A', 'ARRAY_A', false);
define('ARRAY_N', 'ARRAY_N', false);

if (!defined('SAVEQUERIES'))
define('SAVEQUERIES', false);

class dtdb {

	var $show_errors = true;
	var $num_queries = 0;
	var $last_query;
	var $col_info;
	var $queries;
	var $rows_affected;


	// ==================================================================
	//	DB Constructor - connects to the server and selects a database

	function dtdb($dbuser, $dbpassword, $dbname, $dbhost) {
		$this->dbh = @mysql_connect($dbhost, $dbuser, $dbpassword);
		if (!$this->dbh) {
			$this->bail("
		<h1>建立数据库链接时出错！</h1>
		<p>也可能是存在于<code>doc-config.php</code>文件中的数据库用户名或密码不正确，不能建立与数据库服务器<code>$dbhost</code>的连接. </p>
		<ul>
			<li>你确定你的数据库用户名密码没错？</li>
			<li>你确定你输入了正确的主机名？</li>
			<li>你确定你的数据库服务器正在运行？</li>
		</ul>
		<p>如果你不确定这些信息请联系你的虚拟主机服务供应商. 如果仍然不能解决请登陆 <a href='http://www.doccms.net/'>稻壳CMS官方技术支持论坛</a>.</p>
		");
		}
		$this->dbselect($dbname);
		$this->query("SET NAMES 'utf8' ");
	}

	// ==================================================================
	//	Select a DB (if another one needs to be selected)

		function dbselect($db) {
			if (!@mysql_select_db($db, $this->dbh)) {
				$this->bail("
		<h1>不能选择数据库</h1>
		<p>我们可以正确的连接到数据库 (这说明你的用户名和密码没问题) 但是不能选择(select) <code>$db</code> 数据库.</p>
		<ul>
			<li>你确定这个数据库存在?</li>
			<li>一些系统中会将你的用户名作为你数据库的前缀,就像这样 username_doccms. 检查一下你是否是这个问题?</li>
		</ul>
		<p>如果你不知道怎样在MYSQL中 设置/安装 一个数据库，那么请<strong>联系你的虚拟主机供应商</strong>. 如果都不是以上的问题请登陆 <a href='http://www.doccms.net/'>稻壳CMS官方技术支持论坛</a>.</p>");
			}
		}

	// ====================================================================
	//	Format a string correctly for safe insert under all PHP conditions

	function escape($string) {
		return addslashes( $string ); // Disable rest for now, causing problems
		if( !$this->dbh || version_compare( phpversion(), '4.3.0' ) == '-1' )
		return mysql_escape_string( $string );
		else
		return mysql_real_escape_string( $string, $this->dbh );
	}

	// ==================================================================
	//	Print SQL/DB error.

	function print_error($str = '') {
		global $EZSQL_ERROR;
		if (!$str) $str = mysql_error();
		$EZSQL_ERROR[] =
		array ('query' => $this->last_query, 'error_str' => $str);

		$str = htmlspecialchars($str, ENT_QUOTES);
		$query = htmlspecialchars($this->last_query, ENT_QUOTES);
		// Is error output turned on or not..

		
		if ( $this->show_errors ) {
			// If there is an error then take note of it
			print "<div id='error'>
			<p class='dtdberror'><strong>数据库错误:</strong> [$str]<br />
			<code>$query</code></p>
			</div>";
		} else {
			return false;
		}
	}

	// ==================================================================
	//	Turn error handling on or off..

	function show_errors() {
		$this->show_errors = true;
	}

	function hide_errors() {
		$this->show_errors = false;
	}

	// ==================================================================
	//	Kill cached query results

	function flush() {
		$this->last_result = null;
		$this->col_info = null;
		$this->last_query = null;
	}

	// ==================================================================
	//	Basic Query	- see docs for more detail

	function query($query,$execRows=false) {
		// initialise return
		$return_val = 0;
		$this->flush();

		// Log how the function was called
		$this->func_call = "\$db->query(\"$query\")";

		// Keep track of the last query for debug..
		$this->last_query = $query;

		// Perform the query via std mysql_query function..
		if (SAVEQUERIES)
		$this->timer_start();
		
		
		if($execRows)
		{
			$sql_arr=explode(';',$query);
			foreach ($sql_arr as $sql_o)
			{
				@mysql_query($sql_o,$this->dbh);
			}
		}
		else
		{
			$this->result = @mysql_query($query, $this->dbh);
		}
		++$this->num_queries;

		if (SAVEQUERIES)
		$this->queries[] = array( $query, $this->timer_stop() );

		// If there is an error then take note of it..
		if ( mysql_error() ) {
			$this->print_error();
			return false;
		}


		if ( preg_match("/^\\s*(insert|delete|update|replace) /i",$query) ) {
			$this->rows_affected = mysql_affected_rows();
			// Take note of the insert_id
			if ( preg_match("/^\\s*(insert|replace) /i",$query) ) {
				$this->insert_id = mysql_insert_id($this->dbh);
			}
			// Return number of rows affected
			$return_val = $this->rows_affected;
		} else {
			$i = 0;
			while ($i < @mysql_num_fields($this->result)) {
				$this->col_info[$i] = @mysql_fetch_field($this->result);
				$i++;
			}
			$num_rows = 0;
			while ( $row = @mysql_fetch_object($this->result) ) {
				$this->last_result[$num_rows] = $row;
				$num_rows++;
			}

			@mysql_free_result($this->result);
			@mysql_close($this->result);

			// Log number of rows the query returned
			$this->num_rows = $num_rows;

			// Return number of rows selected
			$return_val = $this->num_rows;
		}

		return $return_val;
	}

	// ==================================================================
	//	Get one variable from the DB - see docs for more detail

	function get_var($query=null, $x = 0, $y = 0) {
		$this->func_call = "\$db->get_var(\"$query\",$x,$y)";
		if ( $query )
		$this->query($query);

		// Extract var out of cached results based x,y vals
		if ( $this->last_result[$y] ) {
			$values = array_values(get_object_vars($this->last_result[$y]));
		}

		// If there is a value return it else return null
		return (isset($values[$x]) && $values[$x]!=='') ? $values[$x] : null;
	}

	// ==================================================================
	//	Get one row from the DB - see docs for more detail

	function get_row($query = null, $output = OBJECT, $y = 0) {
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";
		if ( $query )
		$this->query($query);

		if ( $output == OBJECT ) {
			return $this->last_result[$y] ? $this->last_result[$y] : null;
		} elseif ( $output == ARRAY_A ) {
			return $this->last_result[$y] ? get_object_vars($this->last_result[$y]) : null;
		} elseif ( $output == ARRAY_N ) {
			return $this->last_result[$y] ? array_values(get_object_vars($this->last_result[$y])) : null;
		} else {
			$this->print_error(" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N");
		}
	}

	// ==================================================================
	//	Function to get 1 column from the cached result set based in X index
	// se docs for usage and info

	function get_col($query = null , $x = 0) {
		if ( $query )
		$this->query($query);

		// Extract the column values
		for ( $i=0; $i < count($this->last_result); $i++ ) {
			$new_array[$i] = $this->get_var(null, $x, $i);
		}
		return $new_array;
	}

	// ==================================================================
	// Return the the query as a result set - see docs for more details

	function get_results($query = null, $output = OBJECT) {
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		if ( $query )
		$this->query($query);

		// Send back array of objects. Each row is an object
		if ( $output == OBJECT ) {
			return $this->last_result;
		} elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
			if ( $this->last_result ) {
				$i = 0;
				foreach( $this->last_result as $row ) {
					$new_array[$i] = (array) $row;
					if ( $output == ARRAY_N ) {
						$new_array[$i] = array_values($new_array[$i]);
					}
					$i++;
				}
				return $new_array;
			} else {
				return null;
			}
		}
	}


	// ==================================================================
	// Function to get column meta data info pertaining to the last query
	// see docs for more info and usage

	function get_col_info($info_type = 'name', $col_offset = -1) {
		if ( $this->col_info ) {
			if ( $col_offset == -1 ) {
				$i = 0;
				foreach($this->col_info as $col ) {
					$new_array[$i] = $col->{$info_type};
					$i++;
				}
				return $new_array;
			} else {
				return $this->col_info[$col_offset]->{$info_type};
			}
		}
	}

	function timer_start() {

		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$this->time_start = $mtime[1] + $mtime[0];
		return true;
	}

	function timer_stop($precision = 3) {
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$time_end = $mtime[1] + $mtime[0];
		$time_total = $time_end - $this->time_start;
		return $time_total;
	}

	function bail($message) { // Just wraps errors in a nice header and footer
		if ( !$this->show_errors )
		return false;
		header( 'Content-Type: text/html; charset=utf-8');
		echo <<<HEAD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
		<title>稻壳企业建站系统 错误页面</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style media="screen" type="text/css">
		<!--
		html {
			background: #eee;
		}
		body {
			background: #fff;
			color: #000;
			font-family: Georgia, "Times New Roman", Times, serif;
			margin-left: 25%;
			margin-right: 25%;
			padding: .2em 2em;
		}
		
		h1 {
			color: #006;
			font-size: 18px;
			font-weight: lighter;
		}
		
		h2 {
			font-size: 16px;
		}
		
		p, li, dt {
			line-height: 140%;
			padding-bottom: 2px;
		}
	
		ul, ol {
			padding: 5px 5px 5px 20px;
		}
		#logo {
			margin-bottom: 2em;
		}
		-->
		</style>
	</head>
	<body>
	<h1 id="logo"><a href="http://www.doccms.com">DOCCMS</a></h1>
HEAD;
		echo "<div><font color=\"Red\">出现这种错误的原因是可能您还没有安装数据库，请点击<a href='/setup/setup.php'>安装</a>您的数据库</font></div>";
		echo $message;
		echo "</body></html>";
		die();
}
}
class DtDatabase extends dtdb {
	function DtDatabase() {
		$this->dtdb(DB_USER, DB_PASSWORD, DB_DBNAME, DB_HOSTNAME);
	}
}
$db = new DtDatabase();