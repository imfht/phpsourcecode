<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/8/26
 * Time: 上午11:13
 * 代理模式,自动实现读写分离
 */

namespace LuciferP\Orm\db;


use LuciferP\Orm\base\Factory;

/**
 * Class Proxy
 * @package LuciferP\Orm\db
 * @author Luficer.p <81434146@qq.com>
 */
class Proxy
{
    /**
     * @var
     */
    private $db;

    /**
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function query($sql, $params = [])
    {
        $debug = "==>>sql bug: $sql \n";
//        echo $debug;
        if (preg_match("#select#i", $sql)) {
            $this->db = Factory::getDb('slave');
        } else {
            $this->db = Factory::getDb('master');
        }
        return $this->db->query($sql, $params);
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db->getDb();
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->db->getErrors();
    }


}