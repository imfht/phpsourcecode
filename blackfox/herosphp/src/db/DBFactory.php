<?php
/**
 * 数据库操作工厂类,创建数据库连接对象
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\db;

use herosphp\db\driver\mongo\Mongo;
use herosphp\db\driver\Mysql;
use herosphp\db\interfaces\Idb;

class DBFactory {

    /**
     * 数据库连接池
     * @var array
     */
    private static $DB_POOL = array();

    /**
     * 数据库驱动配置
     * @var array
     */
    private static $DB_DRIVER = array(
        //单台服务器
        DB_ACCESS_SINGLE => Mysql::class,
        //服务器集群
        DB_ACCESS_CLUSTERS => ClusterDB::class,
        //mongodb
        'mongo' => Mongo::class
    );

    /**
     * 创建数据库连接实例
     * @param int $accessType   连接方式（连接单个服务器还是连接集群）
     * @param array $config 数据库的配置信息
     * @return Idb
     */
    public static function createDB($accessType=DB_ACCESS_SINGLE, &$config = null) {

        //获取包含路径
        $className = self::$DB_DRIVER[$accessType];
        $key = md5($className.$config['flag']);
        if ( !isset(self::$DB_POOL[$key]) ) {
            $reflect = new \ReflectionClass($className);
            self::$DB_POOL[$key] = $reflect->newInstance($config);
        }
        return self::$DB_POOL[$key];
	}

}

