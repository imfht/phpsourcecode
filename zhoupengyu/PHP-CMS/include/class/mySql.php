<?php
/** ***********************
 * 作者：卢逸 www.61php.com
 * 日期：2015/5/21
 * 作用：MySql类
 ** ***********************/
class MySqlClass{
    //主机
    public $host="localhost";
    //数据库的username
    public $name="root";
    //数据库的password
    public $pass="";
    //数据库名称
    public $table="phptest";
    //编码形式
    public $ut="utf8";
    public $database="default";

    //构造函数
    function __construct(){
    	$this->host=_DATABASE_HOST;
    	$this->name=_DATABASE_USER;
    	$this->pass=_DATABASE_PASSWORD;
    	$this->table=_DATABASE_NAME;
    	$this->ut=_DATABASE_UT;
        $this->connect();
    }

    //数据库的链接
    function connect(){
        $link=mysql_connect($this->host,$this->name,$this->pass) or die (mysql_error());
        mysql_select_db($this->table,$link) or die("没该数据库：".$this->table);
        mysql_query("SET NAMES '$this->ut';");
    }

    function Execute($sql) {
    	$re=mysql_query($sql);
    	if (eregi("update",$sql) && $re){
    		return ($this->affected_rows()===false)?false:true;
    	}elseif (eregi("insert",$sql)  && $re){
    		//加个判断是否是多条一起插入
    		if (eregi("values\s*\([^\)]+\),\([^\)]+\)",$sql)){
    			return $re;
    		}else{
    			return $this->insert_id();
    		}
    	}else{
    		return $re;
    	}
    }

    function GetAll($sql){
    	$rs=mysql_query($sql);
    	while ($row=mysql_fetch_assoc($rs)){
    		$datas[]=$row;
    	}
    	return $datas;
    }

    function GetOne($sql) {
    	$rs=mysql_query($sql);
        return mysql_result($rs,0);
    }
    
    function GetRow($sql) {
    	$rs=mysql_query($sql);
    	return mysql_fetch_assoc($rs);
    }
    
    function free_result($query) {
        return mysql_free_result($query);
    }

    function insert_id() {
        return mysql_insert_id();
    }

    function affected_rows() {
    	return mysql_affected_rows();
    }
   
    function version() {
        return mysql_get_server_info();
    }

    function close() {
        return mysql_close();
    }
}
?>