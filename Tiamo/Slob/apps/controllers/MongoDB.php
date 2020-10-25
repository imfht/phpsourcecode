<?php
/**
 * Created by PhpStorm.
 * User: xiangdong
 * Date: 15/11/24
 * Time: 上午11:21
 */

namespace App\Controller;

use Swoole;

class MongoDB extends Swoole\Controller
{
    /**
     * 插入数据
     */
    function testSet(){
        $doc = [
            "_id"=>001,
            "id"=>1,
            "location"=>[123,234]
        ];
        $mongo=$this->swoole->mongo->test;
        $db_test=$mongo->selectCollection("test");
        $db_test->insert($doc);
    }

    /**
     * 获取数据
     */
    function testGet(){
        $mongo=$this->swoole->mongo->test;
        $db_test=$mongo->selectCollection("test");
        $params=[
            "_id"=>1
        ];
        $list=$db_test->find($params);
        foreach ($list as $doc) {
            var_dump($doc);
        }
    }

    /**
     * 创建集合  类似创建表
     */
    function createCollection(){
        $collection = $this->swoole->mongo->test;
        $collection->createCollection("geo");
    }
    function map(){
        $this->display("map/map.html");
    }
    function saveUser(){
        $user=model("Admin");
        $user->put(['username'=>123]);
        exit;
    }


}