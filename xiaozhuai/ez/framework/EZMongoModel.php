<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/21
 * Time: 下午3:53
 */
class EZMongoModel extends EZModel
{
    protected $conn;
    protected $db;
    protected $collection;

    function __construct($collectionName=null){
        $this->conn = EZMongoDB::getInstance()->conn;
        $this->db = $this->conn->selectDB(EZConfig()->MONGO_DBNAME);
        if(!empty($collectionName)) $this->collection = $this->db->selectCollection($collectionName);
    }

    public function getCONN(){
        return $this->conn;
    }

    public function getDB(){
        return $this->db;
    }

    public function exec($command){
        return $this->db->execute($command);
    }

    public function getCollection(){
        return $this->collection;
    }

    public function toArr($cursor){
        $list = [];
        foreach ($cursor as $row){
            if(isset($row['_id'])){
                $row['_id'] = $row['_id']->__toString();
            }
            array_push($list, $row);
        }
        return $list;
    }

}