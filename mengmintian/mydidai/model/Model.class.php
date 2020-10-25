<?php
class Model{
    

    public $conn = null;

    public function __construct(){
        $this->connect();
    }

    public function connect(){
        $this->conn = @mysql_connect(DB_HOST,DB_USER,DB_PASS);
        if(!$this->conn){
            exit('数据库连接出错：'.mysql_error());
        }

        $this->query("use ".DB_NAME);

        $this->query("set names utf8");

    }

    public function query($sql){

        if(!$rs = mysql_query($sql,$this->conn)){
            exit('执行出错：'.mysql_error());
        }
        return $rs;
    }

   public function getAll($sql){
        
        $rs = $this->query($sql);
        $data = array();
        while($row = mysql_fetch_assoc($rs)){
            $data[] = $row;
        }
        return $data;
    }

    public function getOne($sql){
        $rs = mysql_fetch_row($this->query($sql));
        
        return $rs[0];
    }

    public function getRow($sql){
        $rs = array();
        $rs[0] = mysql_fetch_assoc($this->query($sql));
        
        return $rs;
    }
}
?>
