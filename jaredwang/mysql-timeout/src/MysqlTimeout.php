<?php
/**
 * mysqli-query-timeout
 * wangyang
 */
namespace MysqlTimeout;
use Exception;
use mysqli;

class MysqlTimeout
{
    public $db;
    public $error;
    public $errno;
    public $affected_rows;
    public $timeout = 3;
    function __construct($config){
        $this->db = @new mysqli($config['host'], $config['user'], $config['password'], $config['dbname'], isset($config['port']) ? $config['port'] : 3306);
        if($this->error = mysqli_connect_error()){
            $this->db = null;
            throw new Exception('connection mysql fail');
        }
        $this->timeout = isset($config['timeout']) ? $config['timeout'] : 3;
        $this->db->set_charset($config['charset']);
    }

    protected function getResult($sql,$timeout,$func){
        $timeout == null && $timeout = 3;
        $this->db->query($sql, MYSQLI_ASYNC);
        $all_links = array($this->db);
        $processed = 0;
        $begin = microtime(true);
        do {
            $links = $errors = $reject = array();
            foreach ($all_links as $link) {
                $links[] = $errors[] = $reject[] = $link;
            }
            if (!mysqli_poll($links, $errors, $reject, 0, 50000)) {
                if(microtime(true)-$begin > $timeout){
                    //$result = $this->db->reap_async_query();
                    //mysqli_free_result($result);
                    throw new Exception('timeout',922922);
                    //break;
                }
                continue;
            }
            foreach ($links as $link) {
                if ($result = $link->reap_async_query()) {
                    is_callable($func) && $func($result,$link);
                } else {
                    $this->error = sprintf("MySQLi Error: %s", mysqli_error($link));
                    throw new Exception($this->error);
                }
                $processed++;
            }
        } while ($processed < count($all_links));
    }

    public function query($sql,$timeout = null){
        $ret = null;
        $this->getResult($sql,$timeout,function($result) use (&$ret){
            //while($row = $result->fetch_assoc()){
            //    $ret[] = $row;
            //}
            $ret = $result->fetch_all(MYSQL_ASSOC);
            if (is_object($result)){
                mysqli_free_result($result);
            }
        });
        return $ret;
    }

    public function update($sql,$timeout = null){
        $ret = false;
        $this->getResult($sql,$timeout,function($result,$link) use (&$ret){
            if ($result === TRUE){
                $this->affected_rows = $link->affected_rows;
                $ret = $this->affected_rows;
            }else{
                $this->error = $link->error;
                $this->errno = $link->errno;
            }
            if (is_object($result)){
                mysqli_free_result($result);
            }
        });
        return $ret;
    }

    public function insert($sql,$timeout = null){
        $ret = false;
        $this->getResult($sql,$timeout,function($result,$link) use (&$ret){
            if ($result === TRUE){
                $this->insert_id = $link->insert_id;
                $ret = $this->insert_id;
            }else{
                $this->error = $link->error;
                $this->errno = $link->errno;
            }
            if (is_object($result)){
                mysqli_free_result($result);
            }
        });
        return $ret;
    }

    public function delete($sql,$timeout = null){
        $this->update($sql,$timeout);
    }

    public function insert_id(){
        return $this->db->insert_id;
    }

    public function close(){
        $this->db->close();
        unset($this->db);
    }

    function __destruct(){
        $this->db && $this->db->close();
        if(isset($this->db))
            unset($this->db);
    }

}
