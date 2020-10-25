<?php

namespace Library\Db;

class MongoDb {

    protected $_table      = 'test';
    protected $_collection =  NULL;
    protected $_connection =  NULL;
    protected $_database   =  NULL;

    private static $_configs   = array();
    private static $_instances = array();

    /**
     * @param $name
     * @param string $env
     * @return MongoDb
     * @throws \Exception
     */
    public static function instance($name, $env='product') {
        if ( empty(self::$_configs) ) {
            self::$_configs = \Config\Mongo::getConfig($name, $env);
        }

        if ( ! isset(self::$_instances[$name]) ) {
            $instance = new MongoDb ( self::$_configs['host'], self::$_configs['port'], self::$_configs['database'] );
            self::$_instances[$name] = $instance;

            return $instance;

        }
        return self::$_instances[$name];
    }

    /**
     * @param $host         主机地址
     * @param $port         端口
     * @param $database     数据库
     * @throws \Exception
     */
    public function __construct($host, $port, $database) {
        if(extension_loaded('Mongo')) {

            $conStr = "mongodb://" . self::$_configs['host'] . ":" . self::$_configs['port'];
            $this->_connection = new \Mongo($conStr, array(
                "connect" => false
            ));

            $this->_database = $this->_connection->selectDB($database);
        } else {
            throw new \Exception("extension memcached is not installed");
        }
    }

    /**
     * 对外提供mongo链接
     */
    public function setCollection($table=false) {
        $this->_collection = $this->_database->selectCollection($table);
        return $this->_collection;
    }

    /**
     * 插入数据
     *
     * @param array $data
     * @return bool/  MongoId Object([$id] => 5552c05f7f8b9afe73000000)
     */
    public function insert($data = array()) {
        $option = array('safe'=>true);
        $result = $this->_collection->insert($data, $option);

        if($result['ok'] == 1) {
            $s = '$id';
            return $data['_id']->$s;
        } else {
            return false;
        }
    }

    /**
     * 删除数据
     * @param array $where
     * @example  $where = array('_id'=>new MongoId('5552c0607f8b9aff73000000'));
     * @return bool
     */
    public function remove($where = array()) {
        return $this->_collection->remove($where);
    }

    /**
     * 修改数据
     *
     * @param array $where     $where = array('_id'=>new MongoId('5552c5bc7f8b9a3174000000'));
     * @param array $data      $data = array('$set'=>array('uid'=>622124));
     * @return bool
     */
    public function update($where = array(), $data = array() ) {
        return $this->_collection->update($where, $data);
    }

    /**
     * 查询多条数据
     *
     * @param array $where
     * @param array $sort    array('time'=>1);  //-1降序，1升序
     * @param array $field   array( '_id'=> false); //除_id 所有字段
     * @param array $limit   array(0, 10)   limit 0,10
     * @return array
     */
    public function find($where = array(), $sort = array(), $field = array('_id'=> false),  $limit = array(0, 10)) {
        $cursor = $this->_collection->find($where)->fields($field)->sort($sort)->limit($limit[1])->skip($limit[0]);  //MongoCursor Object

        $list = array();
        foreach ($cursor as $key => $value) {
            $list[] = $value;
        }

        $count = $this->_collection->count($where);


        return array('count'=>$count, 'list'=>$list);

    }

    /**
     * 查询单条数据
     *
     * @param array $where $where = array('_id'=>new MongoId('5552c5bc7f8b9a3174000000'));
     * @return mixed
     */
    public function findOne($where = array()) {
        return $this->_collection->findOne($where);
    }
}