<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/29
 * Time: 上午9:47
 */

namespace Partini\Db;


class Mysqli
{

    protected $links;
    protected $options;
    protected $count_of_read;
    protected $connected = false;
    protected $sql;
    protected $read_index_now;
    protected $queryID = null;
    protected $in_trans = false;
    protected $error = null;
    protected $lastInsID;

    public function __construct($config)
    {
        if ( !extension_loaded('mysqli') ) {
            throw new DbException('mysqli extension not loaded');
        }
        $options = array();
        $keys = array(
            'host' => 'DB_HOST',
            'user' => 'DB_USER',
            'pwd' => 'DB_PWD',
            'port' => 'DB_PORT',
            'db' => 'DB_NAME',
            'pre' => 'DB_PREFIX',
            'charset' => 'DB_CHARSET'
        );
        foreach ($keys as $key => $key_in_config){
            $options[$key] = $config->get($key_in_config);
        }
        if(!is_array($options['host'])) $options['host'] = explode(',',$options['host']);
        if(count($options['host']) < 1) throw new DbException('no db host set');
        $options['db_sep'] = (count($options['host']) > 1) ? true : false;
        $this->options = $options;
        $this->count_of_read = count($options['host']) - 1;
    }

    public function connect($db_index = 0,$db_config_ext = null){
        if(isset($this->options['host'][$db_index]) && $db_config_ext != null){
            throw new DbException("host $db_index is already set");
        }
        if(isset($this->links[$db_index])){
            return $this->links[$db_index];
        }
        $host = is_null($db_config_ext) ? $this->options['host'][$db_index] : $db_config_ext['host'];
        $config = is_null($db_config_ext) ? $this->options : $db_config_ext;
        $this->links[$db_index] = new \mysqli($host,$config['user'],$config['pwd'],$config['db'],$config['port']);
        if (mysqli_connect_errno()) throw new DbException(mysqli_connect_error());
        $this->links[$db_index]->query("SET NAMES '".(empty($config['charset']) ? "utf-8" : $config['charset'])."'");
        if($this->links[$db_index]->server_version >'5.0.1'){
            $this->links[$db_index]->query("SET sql_mode=''");
        }
        $this->connected = true;
        return $this->links[$db_index];
    }

    public function free() {
        if(is_object($this->queryID)){
            $this->queryID->free_result();
        }
        $this->queryID = null;
    }

    public function query($sql,$must_master = false){
        $db_index = ($this->options['db_sep'] && $must_master === false && $this->in_trans === false) ? ($this->read_index_now > 0 ? $this->read_index_now : rand(1,$this->count_of_read)) : 0;
        $db_instance = $this->connect($db_index);
        if(!$db_instance) return false;
        $this->sql = $sql;
        if ( $this->queryID ) $this->free();
        $this->queryID = $db_instance->query($sql);
        if($this->queryID === false){
            $this->error = $db_instance->errno.':'.$db_instance->error.' IN SQL:'.$sql;
            return false;
        }else{
            $this->error = null;
            $result = array();
            $numRows = $db_instance->affected_rows;
            if($numRows > 0) {
                for($i = 0 ; $i < $numRows ; $i++ ){
                    $result[$i] = $this->queryID->fetch_assoc();
                }
                $this->queryID->data_seek(0);
            }
            return $result;
        }
    }

    public function execute($sql){
        $db_instance = $this->connect();
        if(!$db_instance) return false;
        $this->sql = $sql;
        if ( $this->queryID ) $this->free();
        $result = mysqli_query($db_instance, $sql);
        if ( false === $result ) {
            $this->error = $db_instance->errno.':'.$db_instance->error.' IN SQL:'.$sql;
            return false;
        } else {
            $this->lastInsID = $db_instance->insert_id;
            return $db_instance->affected_rows;
        }
    }

    public function startTrans(){
        if($this->in_trans) throw new DbException('already in trans now');
        $db_instance = $this->connect();
        if(!$db_instance) return false;
        $this->in_trans = true;
        return true;
    }

    public function commit(){
        if ($this->in_trans) {
            $db_instance = $this->connect();
            if(!$db_instance) return false;
            $result = $db_instance->commit();
            $db_instance->autocommit( true);
            $this->in_trans = false;
            if(!$result){
                $this->error = $db_instance->errno.':'.$db_instance->error;
                return false;
            }
        }
        return true;
    }

    public function rollback(){
        if ($this->in_trans) {
            $db_instance = $this->connect();
            if(!$db_instance) return false;
            $result = $db_instance->rollback();
            $db_instance->autocommit( true);
            $this->in_trans = false;
            if(!$result){
                $this->error = $db_instance->errno.':'.$db_instance->error;
                return false;
            }
        }
        return true;
    }

    public function getLastErr(){
        return $this->error;
    }

    public function getLastSql(){
        return $this->sql;
    }
}