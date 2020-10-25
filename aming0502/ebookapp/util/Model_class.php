<?php

include_once  WEB_ROOT."util/config.php";
/**
 *
 * 持久层操作类
 * @since 2013-11-27
 * @copyright kaxinpig.net
 * this是指向当前对象的指针（可以看成C里面的指针），self是指向当前类的指针，parent是指向父类的指针
 */

class Model{

	private $query;

	private $link;

	private $sql;

	public function __construct($ut = "gb2312"){
		$user = $GLOBALS ['database'] ['db_user'];
		$passwd = $GLOBALS ['database'] ['passwd'];
		$dbname = $GLOBALS ['database'] ['dbname'];
		$this->connect($GLOBALS ['database'] ['hostname'],$user,$passwd,$dbname,$ut);
	}

	private function connect($host,$name,$pass,$dbname,$ut){
		$this->link=mysql_pconnect($host,$name,$pass) or die ($this->error());
		mysql_select_db($dbname,$this->link) or die("没该数据库：".$dbname);
		mysql_query("SET NAMES '$ut'");
	}
	/**
	 *
	 * 查询指定sql
	 * @param string $sql
	 * @throws Exception
	 */
	public function query($sql) {
		try{
			if(!($this->query = @mysql_query($sql,$this->link))){
				throw new Exception(mysql_error());
			}
		}catch (Exception $e){
			echo $e->getMessage(), '<br/>';
			echo '<pre>', $e->getTraceAsString(), '</pre>';
			echo '<strong>Query: </strong> ' . $sql;
		}

		return $this->query;
	}
	/**
	 *
	 * 通用查询操作，返回结果集数组
	 * @param string $tablename
	 * @param string $fields
	 * @param string $condition
	 * @return array
	 */
	public function select($table,$fields="*",$condition = "1=1"){
		try{
			if (empty($table) || empty($fields) || empty($condition))
			{
				throw new Exception('查询数据的表名，字段，条件不能为空', 444);
			}
			$this->sql = "SELECT {$fields} FROM `{$table}` WHERE {$condition}";
			$this->ping();
			$result = $this->query($this->sql);
			return $this->fetch_all();

		}catch (Exception $e){
			echo $e->getMessage(), '<br/>';
			echo '<pre>', $e->getTraceAsString(), '</pre>';
			echo '<strong>Query: </strong>[select] ', (!empty($this->sql)) && $this->sql;
		}
	}

	/**
	 * 更新数据库记录 UPDATE，返回更新的记录数量
	 *
	 * @param string $table
	 * @param array $data
	 * @param string $condition
	 * @return int
	 */
	public  function update($table, $data, $condition)
	{
		try
		{
			if (empty($table) || empty($data) || empty($condition))
			throw new Exception('更新数据的表名，数据，条件不能为空', 444);

			if(!is_array($data))
			throw new Exception('更新数据必须是数组', 444);

			$set = '';
			foreach ($data as $k => $v)
			$set .= empty($set) ? ("`{$k}` = '{$v}'") : (", `{$k}` = '{$v}'");

			if (empty($set)) throw new Exception('更新数据格式化失败', 444);

			$this->sql = "UPDATE `{$table}` SET {$set} WHERE {$condition}";
			$result = $this->query($this->sql);

			// 返回影响行数
			return $this->affected_rows();
		}
		catch (Exception $e)
		{
			echo $e->getMessage(), '<br/>';
			echo '<pre>', $e->getTraceAsString(), '</pre>';
			echo '<strong>Query: </strong>[update]' . (!empty($this->sql)) && $this->sql;
		}
	}

	/**
	 * 插入数据
	 *
	 * @param string $table
	 * @param array $fields
	 * @param array $data
	 * @return boolean
	 */
	public function insert($table, $fields, $data)
	{
		try
		{
			if (empty($table) || empty($fields) || empty($data)) {
				throw new Exception('插入数据的表名，字段、数据不能为空', 444);
			}

			if (!is_array($fields) || !is_array($data))
			{
				throw new Exception('插入数据的字段和数据必须是数组', 444);
			}

			// 格式化字段
			$_fields = '`' . implode('`, `', $fields) . '`';

			// 格式化需要插入的数据
			$_data = $this->format_insert_data($data);

			if (empty($_fields) || empty($_data))
			{
				throw new Exception('插入数据的字段和数据必须是数组', 444);
			}

			$this->sql = "INSERT INTO `{$table}` ({$_fields}) VALUES {$_data}";
			$result = $this->query($this->sql);

			return $this->affected_rows();
		}
		catch (Exception $e)
		{
			echo $e->getMessage(), '<br/>';
			echo '<pre>', $e->getTraceAsString(), '</pre>';
			echo '<strong>Query: </strong>[insert] ' . (!empty($this->sql)) && $this->sql;

		}
	}



	/**
	 * 格式化 insert 数据，将数组（二维数组）转换成向数据库插入记录时接受的字符串
	 *
	 * @param array $data
	 * @return string
	 */
	protected  function format_insert_data($data)
	{
		if (!is_array($data) || empty($data))
		{
			throw new Exception('数据的类型不是数组', 445);
		}

		$output = '';
		foreach ($data as $value)
		{
			// 如果是二维数组
			if (is_array($value))
			{
				$tmp = '(\'' . implode("', '", $value) . '\')';
				$output .= !empty($output) ? ", {$tmp}" : $tmp;
				unset($tmp);
			}
			else
			{
				$output = '(\'' . implode("', '", $data) . '\')';
			}
		} //foreach

		return $output;
	}


	/**
	 * 删除记录
	 *
	 * @param string $table
	 * @param string $condition
	 * @return num
	 */
	public function delete($table, $condition)
	{
		try
		{
			if (empty($table) || empty($condition))
			{
				throw new Exception('表名和条件不能为空', 444);
			}

			$this->sql = "DELETE FROM `{$table}` WHERE {$condition}";
			$result = $this->query($this->sql);

			return $this->affected_rows();
		}
		catch (Exception $e)
		{
			echo $e->getMessage(), '<br/>';
			echo '<pre>', $e->getTraceAsString(), '</pre>';
			echo '<strong>Query: </strong>[delete] ' . (!empty($this->sql)) && $this->sql;
		}
	}


	/**
	 * 查询记录数
	 *
	 * @param string $table
	 * @param string $condition
	 * @return int
	 */
	public  function get_rows_num($table, $condition)
	{
		try
		{
			if (empty($table) || empty($condition))
			throw new Exception('查询记录数的表名，字段，条件不能为空', 444);

			$this->sql = "SELECT count(*) AS total FROM {$table} WHERE {$condition}";
			$result = $this->query($this->sql);

			$tmp = $this->fetch_one();
			return (empty($tmp)) ? false : $tmp['total'];
		}
		catch (Exception $e)
		{
			echo $e->getMessage(), '<br/>';
			echo '<pre>', $e->getTraceAsString(), '</pre>';
			echo '<strong>Query: </strong>[rows_num] ' . (!empty($this->sql)) && $this->sql;
		}
	}

	/**
	 * 返回受影响数目
	 *
	 * @return init
	 */
	public  function affected_rows ()
	{
		return mysql_affected_rows($this->link);
	}


	/**
	 * 返回本次查询所得的总记录数...
	 *
	 * @return int
	 */
	public function num_rows ()
	{
		return mysql_num_rows($this->query);
	}

	/**
	 * (读)返回单条记录数据
	 *
	 * @param  int   $result_type
	 * @return array
	 */
	public  function fetch_one ()
	{
		return mysql_fetch_array($this->query);
	}

	/**
	 * (读)返回多条记录数据
	 *
	 * @param   int   $result_type
	 * @return  array
	 */
	public  function fetch_all ()
	{
		$row = $rows = array();
		while ($row = mysql_fetch_array($this->query))
		{
			$rows[] = $row;
		}
		if (empty($rows))
		{
			return false;
		}
		else
		{
			return $rows;
		}
	}
	
	function __destruct() {
		/*if($this->link){
			mysql_close ( $this->link );
		}*/
	}
	
	/**
	 * 
	 * 解决“MySQL server has gone away”的错误
	 */
	function ping(){    
 
    if(!mysql_ping($this->link)){    
 
        mysql_close($this->link); //注意：一定要先执行数据库关闭，这是关键    
 
        $this->connect();    
 
    }    
 
} 

}

?>