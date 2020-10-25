<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------

/**
 * 数据库驱动工厂
 * @package     Db
 * @author      后盾向军 <houdunwangxj@gmail.com>
 */
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
     * @param bool $full 加表前缀
     * @return bool
     */
    public static function factory($driver, $table, $full)
    {
        //只实例化一个对象
        if (is_null(self::$dbFactory)) {
            self::$dbFactory = new dbFactory();
        }
        if (is_null($driver)) {
            $driver = ucfirst(C("DB_DRIVER"));
        }

        //数据库驱动存在并且数据库连接正常
        if (isset(self::$dbFactory->driverList[$table]) && self::$dbFactory->driverList[$table]->link) {
            return self::$dbFactory->driverList[$table];
        }
        //获得数据库驱动
        if (self::$dbFactory->getDriver($driver, $table, $full)) {
            return self::$dbFactory->driverList[$table];
        } else {
            return false;
        }
    }

    /**
     * 获得数据库驱动接口
     * @param string $driver 驱动
     * @param string $table 数据表
     * @param bool $full 全表名
     * @return mixed
     */
    private function getDriver($driver, $table, $full)
    {
        $class = "Db" . $driver; //数据库驱动
        $this->driverList[$table] = new $class;
        return $this->driverList[$table]->link($table, $full);
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
