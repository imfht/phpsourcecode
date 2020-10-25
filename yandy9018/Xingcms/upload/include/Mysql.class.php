<?php

class Mysql
{
public $conn_id = '';
public $query_result;
protected $db_error = false;
public $tb_prefix = '';
public $db_name = '';
function __construct($db_config,$pconnect=0)
{
if(isset($db_config['DB_ERROR'])) $this->db_error = $db_config['DB_ERROR'];
if(isset($db_config['TB_PREFIX'])) $this->tb_prefix = $db_config['TB_PREFIX'];
$this->db_name = $db_config['DB_NAME'];
$connect_type = $pconnect ?'mysql_pconnect': 'mysql_connect';
$this->connect($db_config,$connect_type);
$this->select_db($this->db_name);
$this->db_charset($db_config["DB_CHARSET"]);
}
function connect($db_config,$connect_type='mysql_connect')
{
$this->conn_id = @$connect_type($db_config["DB_HOST"].':'.$db_config['DB_PORT'],$db_config["DB_USER"],$db_config["DB_PASS"]);
if (!$this->conn_id) $this->halt("Access denied for user '{$db_config['DB_USER']}'@'{$db_config['DB_HOST']}' (using password: ".(empty($db_config['DB_PASS']) ?'NO': 'YES').")");
}
function select_db($db_name)
{
if (!@mysql_select_db($db_name,$this->conn_id)) $this->halt($this->get_error());
}
function db_charset($db_charset)
{
@mysql_unbuffered_query("SET NAMES '$db_charset'");
}
function query($sql,$query_type = 'mysql_query')
{
$this->query_result = @$query_type($sql,$this->conn_id);
if (!$this->query_result) $this->halt($this->get_error($sql));
return $this->query_result;
}
function query_unbuffered($sql)
{
return $this->query($sql,'mysql_unbuffered_query');
}
function fetch_assoc($query_result = '')
{
if ($query_result == '') $query_result = $this->query_result;
return @mysql_fetch_assoc($query_result);
}
function affected_rows()
{
return @mysql_affected_rows($this->conn_id);
}
function num_rows($query_result = '')
{
if ($query_result == '') $query_result = $this->query_result;
return @mysql_num_rows($query_result);
}
function get_error($msg = '')
{
$error = '';
$error .= '错误编码：'.@mysql_errno($this->conn_id)."\r\n";
$error .= '错误信息：'.@mysql_error($this->conn_id)."\r\n";
$error .= $msg ?'错误语句：'.$msg : '';
return $error;
}
function insert_id()
{
return @mysql_insert_id($this->conn_id);
}
function next_id($tbname)
{
$sql = "show table status where name='{$this->tb_prefix}$tbname'";
$rs = $this->row_query_one($sql);
return $rs['Auto_increment'];
}
function free_result($query_result = '')
{
if ($query_result == '') $query_result = $this->query_result;
@mysql_free_result($query_result);
}
function close_db()
{
return @mysql_close($this->conn_id);
}
function get_server_info()
{
$rs= @mysql_query("SELECT VERSION();",$this->conn_id);
$row = @mysql_fetch_array($rs);
$mysql_version = $row[0];
@mysql_free_result($rs);
$mysql_versions = explode(".",trim($mysql_version));
$mysql_version = number_format($mysql_versions[0].".".$mysql_versions[1],2);
return $mysql_version;
}
function show_tables($database = '')
{
if ($database == '') $database = $this->db_name;
$rs = $this->row_query("SHOW TABLES IN $database");
$tables = array();
foreach ($rs as $v)
{
$tables[] = $v["Tables_in_$database"];
}
return $tables;
}
function desc_table($tbname,$only_filed = 0)
{
$rs = $this->row_query("DESC {$this->tb_prefix}{$tbname}");
if(!$only_filed) return $rs;
$fileds = array();
foreach ($rs as $v)
{
$fileds[] = $v['Field'];
}
return $fileds;
}
function operation_table($tbname,$do='')
{
if (is_array($tbname))
{
$str_tbname = '';
foreach ($tbname as $v)
{
$str_tbname .= '`'.$v.'`,';
}
$str_tbname = substr($str_tbname,0,-1);
}
else $str_tbname = '`'.$tbname.'`';
return $this->query("$do TABLE $str_tbname");
}
function halt($message = '')
{
if ($this->db_error)
{
$message = "MySQL错语信息\r\n".$message;
echo "<html><head><title>MySQL 错误</title>";
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo "<style type=\"text/css\"><!--.error { font: 11px tahoma, verdana, arial, sans-serif, simsun; }--></style></head>\r\n";
echo "<body>\r\n";
echo "<blockquote>\r\n";
echo "<textarea class=\"error\" rows=\"15\" cols=\"80\" wrap=\"on\" >".htmlspecialchars($message) ."</textarea>\r\n";
echo "</blockquote>\r\n</body></html>";
}
exit;
}
function __destruct()
{
$this->close_db();
}
function sql_select($tbname,$where='',$fields='*',$limit=0,$order_by='',$group_by='')
{
$sql = "SELECT $fields FROM `".$this->tb_prefix.$tbname.'`'.($where ?' WHERE '.$where : '').($group_by ?" GROUP BY $group_by": '').($order_by ?" ORDER BY $order_by": '').($limit ?' LIMIT '.$limit : '');
return $sql;
}
function sql_insert($tbname,$row,$replace=0)
{
$sql_field = $sql_value = '';
foreach ($row as $key=>$value)
{
$sql_field .= '`'.$key.'`,';
$sql_value .= "'".$value."',";
}
return ($replace ?'REPLACE': 'INSERT').' INTO `'.$this->tb_prefix.$tbname.'`('.substr($sql_field,0,-1).') VALUES ('.substr($sql_value,0,-1).')';
}
function sql_arr_insert($tbname,$arr,$replace=0)
{
$sql_field = $sql_value = '';
$arr_value = array();
foreach ($arr as $key=>$val)
{
$sql_field .= '`'.$key.'`,';
$num  = count($val);
for ($i = 0;$i <$num;$i++)
{
$arr_value[$i][] = $val[$i];
}
}
foreach ($arr_value as $arr)
{
$str_value = '';
foreach ($arr as $v)
{
$str_value .= "'".$v."',";
}
$sql_value .= '('.substr($str_value,0,-1).'),';
}
$sql = ($replace ?'REPLACE': 'INSERT').' INTO `'.$this->tb_prefix.$tbname.'`('.substr($sql_field,0,-1).') VALUES '.substr($sql_value,0,-1);
return $sql;
}
function sql_update($tbname,$row,$where)
{
$sqlud = '';
foreach ($row as $key=>$value)
{
$sqlud .= '`'.$key."`='".$value."',";
}
return "UPDATE `".$this->tb_prefix.$tbname."` SET ".substr($sqlud,0,-1)." WHERE ".$where;
}
function sql_delete($tbname,$where)
{
return 'DELETE FROM `'.$this->tb_prefix.$tbname.'` WHERE '.$where;
}
function row_insert($tbname,$row,$replace=0)
{
$sql = $this->sql_insert($tbname,$row,$replace);
return $this->query_unbuffered($sql);
}
function arr_insert($tbname,$row,$replace=0)
{
$sql = $this->sql_arr_insert($tbname,$row,$replace);
return $this->query_unbuffered($sql);
}
function row_update($tbname,$row,$where)
{
$sql = $this->sql_update($tbname,$row,$where);
return $this->query_unbuffered($sql);
}
function row_delete($tbname,$where)
{
$sql = $this->sql_delete($tbname,$where);
return $this->query_unbuffered($sql);
}
function row_select($tbname,$where='',$fields='*',$limit=0,$order_by='',$group_by='')
{
$sql = $this->sql_select($tbname,$where,$fields,$limit,$order_by,$group_by);
return $this->row_query($sql);
}
function row_select_one($tbname,$where='',$fields='*',$order_by='',$group_by='')
{
$sql = $this->sql_select($tbname,$where,$fields,1,$order_by,$group_by);
return $this->row_query_one($sql);
}
function row_query($sql)
{
$rs = $this->query($sql);
$rows = array();
$rs_num = $this->num_rows($rs);
for($i=0;$i<$rs_num;$i++)
{
$rows[] = $this->fetch_assoc($rs);
}
$this->free_result($rs);
return $rows;
}
function row_query_one($sql)
{
$rs = $this->query($sql);
$row = $this->fetch_assoc($rs);
$this->free_result($rs);
return $row;
}
function row_count($tbname,$where='')
{
$sql = 'SELECT count(*) as row_num FROM `'.$this->tb_prefix.$tbname.'` '.($where ?' WHERE '.$where : '');
$row = $this->row_query_one($sql);
return $row['row_num'];
}
function GetError()
{
$str = mysql_error();
return $str;
}
function viewinfo_table($tbname)
{
$sql= 'SHOW CREATE TABLE `'.$tbname.'`';
$rs= mysql_query($sql) OR die("SQL Error!");
return $rs;
}
function fields_count($tbname)
{
$sql = "SELECT * FROM ".$tbname;
$result = mysql_query($sql);
$filedscount=mysql_num_fields($result);
return $filedscount;
}
}
?>