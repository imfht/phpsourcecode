<?php
/*
 * class mysql
 * www.php100.com 编辑器教程
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
	include_once  WEB_ROOT."util/config.php";
	
   class mysql{
   
		public $link;

     function __construct($ut = "gb2312"){
		//$db =  new mysql();
     	//$this->connect('localhost','u114841','q9LhiBaD','u114841a',"gb2312");
		$user = $GLOBALS ['database'] ['db_user'];
		$passwd = $GLOBALS ['database'] ['passwd'];
		$dbname = $GLOBALS ['database'] ['dbname'];
		$this->connect($GLOBALS ['database'] ['hostname'],$user,$passwd,$dbname,$ut);
     }


     function connect($host,$name,$pass,$table,$ut){
      $link=mysql_pconnect($host,$name,$pass) or die ($this->error());
      mysql_select_db($table,$link) or die("没该数据库：".$table);
      mysql_query("SET NAMES '$ut'");
     }

	function query($sql, $type = '') {
	    if(!($query = mysql_query($sql))) $this->show('Say:', $sql);
	    return $query;
	}

    function show($message = '', $sql = '') {
		if(!$sql) echo $message;
		else echo $message.'<br>'.$sql;
	}

    function affected_rows() {
		return mysql_affected_rows();
	}

	function result($query, $row) {
		return mysql_result($query, $row);
	}

	function num_rows($query) {
		return @mysql_num_rows($query);
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		return mysql_insert_id();
	}

	function fetch_row($query) {
		return mysql_fetch_row($query);
	}

	function fetch_row_array($query){
		return mysql_fetch_array($query);
	}

	function version() {
		return mysql_get_server_info();
	}

	/**
	默认使用最后被 mysql_connect() 打开的连接。如果没有找到该连接，函数会尝试调用 mysql_connect() 建立连接并使用它。如果发生意外，没有找到连接或无法建立连接，系统发出 E_WARNING 级别的警告信息。
	**/
	function close() {
		return mysql_close();
	}
	
	
	function __destruct() {
		if($this->link){
			mysql_close ( $this->link );
		}
	}


   //==============

    public function fn_insert($table,$name,$value){

    	$this->query("insert into $table ($name) values ($value)");

    }


   }


  //$db =  new mysql('localhost','root','','daohang',"GBK");

  //$db->fn_insert('collect_chapter','id,title,dates',"'','我插入的信息',now()");



?>
