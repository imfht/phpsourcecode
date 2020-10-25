<?php 
ini_set ( "display_errors" , true ) ;

/*
$dbhost="210.28.199.254";
$dbport="1521";
//这里是数据库名称，不是sid，也不是service_name
$dbsid="orcl";

$dbpara['oracle']['username'] = 'libsys';
$dbpara['oracle']['password'] = 'mypassword';
//$dbpara['oracle']['url'] = '210.28.196.50:1531/orcl';
$dbpara['oracle']['url'] = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST={$dbhost})(PORT = {$dbport}))(CONNECT_DATA = (SID={$dbsid})))";
$dbpara['oracle']['usepdo'] = false;
//$dbpara['oracle']['dsn'] = 'oci:dbname=orcl;host=210.28.96.50:1531';

*/
class OraDBConnection
{

    private $connection = null;

    public function __construct($orc_user,$orc_pw,$orc_url)
    {
        global $dbpara;
        $this->connection = oci_connect($orc_user,$orc_pw,$orc_url,'al32utf8');
        if (!$this->connection)
        {
            header('HTTP/ 500');
            $e = oci_error();;
            die('failed to connect to database: ' . $e['message']);
        }
    }

    public function __destruct()
    {
        if ($this->connection)
        {
            oci_close($this->connection);
        }
    }

    public function prepare($sqlString)
    {
        return new OraStatement($this->connection, $sqlString);
    }
}

class OraStatement
{
    private $statement = null;

    public function __construct($connection, $sqlString)
    {
        $this->statement = oci_parse($connection, $sqlString);
    }

    public function __destruct()
    {
        if ($this->statement)
        {
           oci_free_statement($this->statement);
       }
    }

    public function execute()
    {
        $result = oci_execute($this->statement);
    }

    public function bindParam($param, $value)
    {
        oci_bind_by_name($this->statement, $param, $value);
    }

    public function fetchObject()
    {
        
        $result = oci_fetch_array($this->statement);
        if ($result == false)
        {
            return false;
        }
        $resultObj = new stdClass();
        foreach ($result as $key => $value)
        {
            $property = strtolower($key);
            $resultObj->$property = $value;
        }
        return $resultObj;
    }

        /** return 0 or 1, it will not return the row num */
    public function rowCount()
    {
        /* before oci_num_rows, we must call oci_fetch_object */
        $this->fetchObject();
        return oci_num_rows($this->statement);
    }
}
?>