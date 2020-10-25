<?php
class Mysql
{
	var $queryCount = 0;
	var $queryTime  = '';
	private $_user;  //连接数据库用户名
	private $_pwd;  //连接密码
	private $_host;  //数据库地址
	private $_conn;  //数据库连接指针
	private $_err;   //返回错误信息
	private $_db;    //连接的数据库名

	/**
	  * 构造方法
	  * @return Connect
	  */
	function __construct(){
		$this->_host = DATA_HOST;
		$this->_user = DATA_USERNAME;
		$this->_pwd = DATA_PASSWORD;
		$this->_db = DATA_NAME;
	  $this->Connect($this->_host,$this->_user,$this->_pwd,$this->_db);
	}

	/**
	  * 建立数据库连接
	  * @param string $host 服务器地址
	  * @param string $user 登陆用户名
	  * @param string $pwd 登陆密码
	  * @param string $db    数据库名称
	  */
	function Connect($host,$user,$pwd,$db=""){
	  $this->_conn=@mysql_connect($host,$user,$pwd);
	  $this->_host=DATA_HOST;
	  $this->_user=DATA_USERNAME;
	  $this->_pwd=DATA_PASSWORD;
	  if(mysql_errno()){
	   $this->_err=mysql_error();
	   return ;
	  }
	  mysql_query("set names utf8");
	  if(!empty($db)){
	   $this->selectDB($db);
	  }
	}

	/**
	  * 选择数据库
	  * @param string $db 数据库名称
	  */
	function selectDB($db){
	  $this->_db=$db;
	  if(!empty($this->_db)){
	   @mysql_select_db($this->_db,$this->_conn);
	   if(mysql_errno()){
	    $this->_err=mysql_error();
	    return ;
	   }
	  }else{
	   $this->_err="请输入数据库名";
	   return ;
	  }
	}

	function query($sql) {
	  if(empty($sql) || $sql == NULL){
	   $this->_err="没有输入SQL语句";
	   return false;
	  }

	  $query = @mysql_query($sql,$this->_conn);

	  if(!$query){
	   $this->_err="请合查数据表前缀是否有误！";
	   return ;
	  }
	  if(mysql_errno()){
	   $this->_err=mysql_error();
	   return false;
	  }

	  return $query;
	}

	/* 仿真 Adodb 函数 */
	function selectLimit($sql, $num, $start = 0)
	{
		if ($start == 0)
		{
			$sql .= ' LIMIT ' . $num;
		}
		else
		{
			$sql .= ' LIMIT ' . $start . ', ' . $num;
		}

		return $this->query($sql);
	}

	function getOne($sql, $limited = false)
	{
		if ($limited == true)
		{
			$sql = trim($sql . ' LIMIT 1');
		}

		$res = $this->query($sql);
		if ($res !== false)
		{
			$row = mysql_fetch_row($res);

			if ($row !== false)
			{

				return $row[0];
			}
			else
			{
				return '';
			}
		}
		else
		{
			return false;
		}
	}

	function getAll($sql)
	{
		$res = $this->query($sql);
		if ($res !== false)
		{
			$arr = array();
			while ($row = mysql_fetch_assoc($res))
			{
				$arr[] =  $row;
			}

			return $arr;
		}
		else
		{
			return false;
		}
	}

	function getRow($sql, $limited = true)
	{
		if ($limited == true)
		{
			$sql = trim($sql . ' LIMIT 1');
		}

		$res = $this->query($sql);
		if ($res !== false)
		{
			return @mysql_fetch_assoc($res);
		}
		else
		{
			return false;
		}
	}

	function getCol($sql)
	{
		$res = $this->query($sql);
		if ($res !== false)
		{
			$arr = array();
			while (@$row = mysql_fetch_row($res))
			{
				$arr[] = $row[0];
			}

			return $arr;
		}
		else
		{
			return false;
		}
	}

	function autoExecute($table, $field_values, $mode = 'INSERT', $where = '', $querymode = '')
	{
		$field_names = $this->getCol('DESC ' . $table);

		$sql = '';
		if ($mode == 'INSERT')
		{
			$fields = $values = array();
			foreach ($field_names AS $value)
			{
				if (array_key_exists($value, $field_values) == true)
				{
					$fields[] = $value;
					$values[] = "'" . $field_values[$value] . "'";
				}
			}

			if (!empty($fields))
			{
				$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
			}
		}
		else
		{
			$sets = array();
			foreach ($field_names AS $value)
			{
				if (array_key_exists($value, $field_values) == true)
				{
					$sets[] = $value . " = '" . $field_values[$value] . "'";
				}
			}

			if (!empty($sets))
			{
				$sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
			}
		}

		if ($sql)
		{
			return $this->query($sql, $querymode);
		}
		else
		{
			return false;
		}
	}

	function autoReplace($table, $field_values, $update_values, $where = '', $querymode = '')
	{
		$field_descs = $this->getAll('DESC ' . $table);

		$primary_keys = array();
		foreach ($field_descs AS $value)
		{
			$field_names[] = $value['Field'];
			if ($value['Key'] == 'PRI')
			{
				$primary_keys[] = $value['Field'];
			}
		}

		$fields = $values = array();
		foreach ($field_names AS $value)
		{
			if (array_key_exists($value, $field_values) == true)
			{
				$fields[] = $value;
				$values[] = "'" . $field_values[$value] . "'";
			}
		}

		$sets = array();
		foreach ($update_values AS $key => $value)
		{
			if (array_key_exists($key, $field_values) == true)
			{
				if (is_int($value) || is_float($value))
				{
					$sets[] = $key . ' = ' . $key . ' + ' . $value;
				}
				else
				{
					$sets[] = $key . " = '" . $value . "'";
				}
			}
		}

		$sql = '';
		if (empty($primary_keys))
		{
			if (!empty($fields))
			{
				$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
			}
		}
		else
		{
			if ($this->version() >= '4.1')
			{
				if (!empty($fields))
				{
					$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
					if (!empty($sets))
					{
						$sql .=  'ON DUPLICATE KEY UPDATE ' . implode(', ', $sets);
					}
				}
			}
			else
			{
				if (empty($where))
				{
					$where = array();
					foreach ($primary_keys AS $value)
					{
						if (is_numeric($value))
						{
							$where[] = $value . ' = ' . $field_values[$value];
						}
						else
						{
							$where[] = $value . " = '" . $field_values[$value] . "'";
						}
					}
					$where = implode(' AND ', $where);
				}

				if ($where && (!empty($sets) || !empty($fields)))
				{
					if (intval($this->getOne("SELECT COUNT(*) FROM $table WHERE $where")) > 0)
					{
						if (!empty($sets))
						{
							$sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
						}
					}
					else
					{
						if (!empty($fields))
						{
							$sql = 'REPLACE INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
						}
					}
				}
			}
		}

		if ($sql)
		{
			return $this->query($sql, $querymode);
		}
		else
		{
			return false;
		}
	}


	/**
	  * 关闭数据源
	  */
	function close(){
	  if($this->_conn){
	   @mysql_close($this->_conn);
	   unset($this->_conn);
	   unset($this->_host);
	   unset($this->_db);
	   unset($this->_user);
	   unset($this->_pwd);
	   unset($this->_err);
	  }
	}

	/**
	  * 析构函数
	  */
	function __destruct(){
	  $this->close();
	}
}
?>
