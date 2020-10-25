<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Orm;


use Timo\Config\Config;

/**
 * Class Db
 * @package Timo\Orm
 * @method Connection table(string $name, bool $is_full = true) 指定数据表（全称）
 * @method Connection name(string $name) 指定数据表（不带前缀）
 * @method Connection query(string $sql, $params = null, $row_type = null, $mode = \PDO::FETCH_ASSOC) static 原生查询
 */
class Db
{
    /**
     * @var array 数据库实例池，一个数据库对应一个实例
     */
    protected static $instances = [];

    /**
     * @var \PDO 当前数据库连接对象
     */
    protected $connection = null;

    /**
     * 获取数据库实例
     *
     * @param $conf string|array 数据库名称|配置
     * @param array $options
     * @return Db
     */
    public static function connect($conf, $options = [])
    {
        if (!is_array($conf)) {
            $conf = Config::runtime('mysql.' . $conf);
        }
        $name = md5(serialize($conf) . implode(',', array_keys($options)));
        if (!isset(self::$instances[$name])) {
            $db = new self();
            $db->connection = Connection::instance($conf, $options);
            self::$instances[$name] = $db;
        }
        return self::$instances[$name];
    }

    /**
     * 获取数据库连接的实例化对象
     *
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([static::connect('default'), $name], $arguments);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->connection, $name], $arguments);
    }

    /**
     * 销毁数据库资源
     *
     * @return bool
     */
    public static function destroy()
    {
        self::$instances = [];
        return true;
    }
}
