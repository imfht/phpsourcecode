<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


use Timo\Config\Config;

class MongoDbModel extends App
{
    protected $dbName;

    protected $collectionName;

    /**
     * @var MongoDb
     */
    protected $db;

    public function __construct($dbType = '', $dbName = '')
    {
        $dbType = !$dbType ? 'mongodb' : $dbType;
        $dbName = !$dbName ? 'master' : $dbName;
        $this->connect($dbType, $dbName);
    }

    protected function connect($dbType, $dbName)
    {
        $config = Config::runtime($dbType . '.' . $dbName);
        $this->db = MongoDb::getInstance($config);
        $this->dbName = $dbName;
    }

    public function insert($data)
    {
        return $this->db->insert($this->collectionName, $data);
    }

    public function find($where, $fields = '*', $order = '', &$page = ['p' => 1, 'limit' => 20])
    {
        return $this->db->find($this->collectionName, $where, $fields, $order, $page);
    }

    public function count($where)
    {
        return $this->db->count($this->collectionName, $where);
    }
}
