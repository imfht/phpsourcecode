<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

if ( !defined('IN_PHPBB') ) exit;

if(!defined('SQL_LAYER'))
{

	//防止二次调用
	define('SQL_LAYER', 'mysql');

	/**
	* 数据库操作类
	**/
	class sql_db
	{

		//声明一些变量
		var $db_connect_id;
		var $query_result;
		var $row = array();
		var $rowset = array();
		var $num_queries = 0;
		var $in_transaction = 0;
		
		var $persistency;
		var $user;
		var $password;
		var $server;
		var $dbname;

		var $sql_code = '';

		/**
		* 建立数据库链接
		*
		* @参数 字符串 $sqlserver 服务器地址
		* @参数 字符串 $sqluser 用户名
		* @参数 字符串 $sqlpassword 密码
		* @参数 字符串 $database 数据库名
		* @参数 布尔值 $persistency 是否建立持久链接（true 为建立持久链接，false 为临时链接，默认为true）
		* @返回 已建立链接返回查询ID，否则返回false
		**/
		function __construct($sqlserver, $sqluser, $sqlpassword, $database, $persistency = true)
		{
			$this->persistency 	= $persistency;
			$this->user 		= $sqluser;
			$this->password 	= $sqlpassword;
			$this->server 		= $sqlserver;
			$this->dbname 		= $database;
			
			// 建立链接或持久链接
			$this->db_connect_id = ($this->persistency) ? @mysql_pconnect($this->server, $this->user, $this->password) : @mysql_connect($this->server, $this->user, $this->password);

			if( $this->db_connect_id )
			{
				if( $database != '' )
				{
					$this->dbname = $database;
					$dbselect = @mysql_select_db($this->dbname);

					if( !$dbselect )
					{
						@mysql_close($this->db_connect_id);
						$this->db_connect_id = $dbselect;
					}
				}

				return $this->db_connect_id;
			}
			else
			{
				return false;
			}
		}
		
		/**
		* 关闭数据库链接
		**/
		function sql_close()
		{
			if( $this->db_connect_id )
			{
				if( $this->in_transaction )
				{
					@mysql_query("COMMIT", $this->db_connect_id);
				}

				return @mysql_close($this->db_connect_id);
			}
			else
			{
				return false;
			}
		}
		
		/**
		* 执行一个SQL查询
		*
		* @参数 字符串 $query MYSQL查询语句
		* @参数 ？ $transaction  
		**/
		function sql_query($query = '', $transaction = FALSE)
		{
			unset($this->query_result);

			$this->sql_code = $query;

			if( $query != "" )
			{
				$this->num_queries++;
				if( $transaction == BEGIN_TRANSACTION && !$this->in_transaction )
				{
					$result = @mysql_query("BEGIN", $this->db_connect_id);
					if(!$result)
					{
						return false;
					}
					$this->in_transaction = TRUE;
				}

				$this->query_result = @mysql_query($query, $this->db_connect_id);
			}
			else
			{
				if( $transaction == END_TRANSACTION && $this->in_transaction )
				{
					$result = @mysql_query("COMMIT", $this->db_connect_id);
				}
			}

			if( $this->query_result )
			{
				unset($this->row[$this->query_result]);
				unset($this->rowset[$this->query_result]);

				if( $transaction == END_TRANSACTION && $this->in_transaction )
				{
					$this->in_transaction = FALSE;

					if ( !mysql_query("COMMIT", $this->db_connect_id) )
					{
						@mysql_query("ROLLBACK", $this->db_connect_id);
						return false;
					}
				}
				
				return $this->query_result;
			}
			else
			{
				if( $this->in_transaction )
				{
					@mysql_query('ROLLBACK', $this->db_connect_id);
					$this->in_transaction = FALSE;
				}
				return false;
			}
		}
		
		/**
		* 算出返回结果集数
		* @参数 $query_id 查询ID
		* @返回 $query_id为真时返回查询结果集，否则返回false
		**/
		function sql_numrows($query_id = 0)
		{
			if( !$query_id )
			{
				$query_id = $this->query_result;
			}

			return ( $query_id ) ? @mysql_num_rows($query_id) : false;
		}
		
		/**
		* 取得前一次 MySQL 操作所影响的记录行数
		**/
		function sql_affectedrows()
		{
			return ( $this->db_connect_id ) ? @mysql_affected_rows($this->db_connect_id) : false;
		}
		
		/**
		* 取得结果集中字段的数目
		**/
		function sql_numfields($query_id = 0)
		{
			if( !$query_id )
			{
				$query_id = $this->query_result;
			}

			return ( $query_id ) ? @mysql_num_fields($query_id) : false;
		}
		
		/**
		* 取得结果中指定字段的字段名
		**/
		function sql_fieldname($offset, $query_id = 0)
		{
			if( !$query_id )
			{
				$query_id = $this->query_result;
			}

			return ( $query_id ) ? @mysql_field_name($query_id, $offset) : false;
		}
		/**
		* 取得结果集中指定字段的类型
		**/
		function sql_fieldtype($offset, $query_id = 0)
		{
			if( !$query_id )
			{
				$query_id = $this->query_result;
			}

			return ( $query_id ) ? @mysql_field_type($query_id, $offset) : false;
		}

		/**
		* 函数从结果集中取得一行作为关联数组
		**/
		function sql_fetchrow($query_id = 0)
		{
			if( !$query_id )
			{
				$query_id = $this->query_result;
			}

			if( $query_id )
			{
				$this->row[$query_id] = @mysql_fetch_array($query_id, MYSQL_ASSOC);
				return $this->row[$query_id];
			}
			else
			{
				return false;
			}
		}

		/**
		* 直接循环出结果
		* 参数 ？ $query_id 查询结果集
		* 返回 $query_id 为真时返回查询结果，否则返回 false
		**/
		function sql_fetchrowset($query_id = 0)
		{
			if( !$query_id )
			{
				$query_id = $this->query_result;
			}

			if( $query_id )
			{
				unset($this->rowset[$query_id]);
				unset($this->row[$query_id]);

				$result = array();
				while($this->rowset[$query_id] = @mysql_fetch_array($query_id, MYSQL_ASSOC))
				{
					$result[] = $this->rowset[$query_id];
				}

				return $result;
			}
			else
			{
				return false;
			}
		}

		/**
		* 函数移动内部结果的指针
		**/
		function sql_rowseek($rownum, $query_id = 0)
		{
			if( !$query_id )
			{
				$query_id = $this->query_result;
			}

			return ( $query_id ) ? @mysql_data_seek($query_id, $rownum) : false;
		}

		/**
		* 返回上一步 INSERT 操作产生的 ID
		**/
		function sql_nextid()
		{
			return ( $this->db_connect_id ) ? @mysql_insert_id($this->db_connect_id) : false;
		}

		/**
		* 函数释放结果内存
		**/
		function sql_freeresult($query_id = 0)
		{
			if( !$query_id )
			{
				$query_id = $this->query_result;
			}

			if ( $query_id )
			{
				unset($this->row[$query_id]);
				unset($this->rowset[$query_id]);

				@mysql_free_result($query_id);

				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		* 函数返回上一个 MySQL 操作产生的文本错误信息
		**/
		function sql_error()
		{
			$result['message'] = @mysql_error($this->db_connect_id);
			$result['code'] = @mysql_errno($this->db_connect_id);
			$result['sql']	= $this->sql_code;

			return $result;
		}
		
		//
		// 下面是一些SQL的处理语句
		// 它不依靠于mysql函数的解析
		//
		
		/**
		* 去除引号
		**/
		function sql_escape($msg)
		{
			return mysql_real_escape_string($msg);
		}

		/**
		* 构建SQL中的INSERT、UPDATE、SELECT语句
		*
		* 可以的语句类型: INSERT, INSERT_SELECT, UPDATE, SELECT
		*
		*/
		function sql_build_array($query, $assoc_ary = false)
		{
			if (!is_array($assoc_ary))
			{
				return false;
			}

			$fields = $values = array();

			if ($query == 'INSERT' || $query == 'INSERT_SELECT')
			{
				foreach ($assoc_ary as $key => $var)
				{
					$fields[] = $key;

					if (is_array($var) && is_string($var[0]))
					{
						// 这是用于 INSERT_SELECT(s)
						$values[] = $var[0];
					}
					else
					{
						$values[] = $this->_sql_validate_value($var);
					}
				}

				$query = ($query == 'INSERT') ? ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')' : ' (' . implode(', ', $fields) . ') SELECT ' . implode(', ', $values) . ' ';
			}
			else if ($query == 'MULTI_INSERT')
			{
				trigger_error('The MULTI_INSERT query value is no longer supported. Please use sql_multi_insert() instead.', E_USER_ERROR);
			}
			else if ($query == 'UPDATE' || $query == 'SELECT')
			{
				$values = array();
				foreach ($assoc_ary as $key => $var)
				{
					$values[] = "$key = " . $this->_sql_validate_value($var);
				}
				$query = implode(($query == 'UPDATE') ? ', ' : ' AND ', $values);
			}

			return $query;
		}
		
		/**
		* 验证值
		* @权限 私有
		*/
		function _sql_validate_value($var)
		{
			if (is_null($var))
			{
				return 'NULL';
			}
			else if (is_string($var))
			{
				return "'" . $this->sql_escape($var) . "'";
			}
			else
			{
				return (is_bool($var)) ? intval($var) : $var;
			}
		}

	}

}

?>