<?php
/**
 * 数据库驱动工厂
 * @package     Db
 * @author      lajox <lajox@19www.com>
 */
namespace Took\Db;
final class DbFactory
{

    public static $dbFactory = null; //静态工厂实例
    protected $driverList = array(); //驱动组

    /**
     * 构造函数
     */
    private function __construct()
    {

    }

    /**
     * 返回工厂实例，单例模式
     * @param $driver 连接驱动
     * @param $table 表
     * @param string $prefix 加表前缀
     * @return bool
     */
    public static function factory($driver, $table, $prefix)
    {
        //只实例化一个对象
        if (is_null(self::$dbFactory)) {
            self::$dbFactory = new DbFactory();
        }
        if (is_null($driver)) {
            $driver = ucfirst(C("DB_DRIVER"));
        }
        //数据库驱动存在并且数据库连接正常
        if (isset(self::$dbFactory->driverList[$table]) && self::$dbFactory->driverList[$table]->link) {
            return self::$dbFactory->driverList[$table];
        }
        //获得数据库驱动
        if (self::$dbFactory->getDriver($driver, $table, $prefix)) {
            return self::$dbFactory->driverList[$table];
        } else {
            return false;
        }
    }

    /**
     * 获得数据库驱动接口
     * @param string $driver 驱动
     * @param string $table 数据表
     * @param string $prefix 是否全表名
     * @return mixed
     */
    private function getDriver($driver, $table, $prefix)
    {
        $class = $driver; //数据库驱动
        $class = 'Took\\Db\\Driver\\'.$class;
        $this->driverList[$table] = new $class;
        return $this->driverList[$table]->link($table, $prefix);
    }

    /**
     * 释放连接驱动
     */
    private function close()
    {
        foreach ($this->driverList as $db) {
            $db->close();
        }
    }

    /**
     * 析构函数
     */
    function __destruct()
    {
        $this->close();
    }

}
