<?php
namespace PhalApi\Core;

use PhalApi\Core\DbLib\Mssql;
use PhalApi\Core\DbLib\Mysql;
use PhalApi\Core\DbLib\Oracle;
use PhalApi\Core\DbLib\Pgsql;
use PhalApi\Core\DbLib\Sqlite;
use PhalApi\Core\DbLib\Sybase;
use PhalApi\Core\Exception\PAException;

/**
 * Class Db
 * @since   2016-09-02
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 * @inheritdoc http://medoo.in/doc
 * @link http://medoo.in/
 * @package PhalApi\Core\DbLib
 * @method array select(string $table, array $join, array $columns = null, array $where = null) static 查询实现
 * @method mixed insert(string $table, array $dataArr) static 数据插入
 * @method mixed update(string $table, array $data, array $where = null) static 数据更新
 * @method mixed delete(string $table, array $where) static 数据删除
 * @method mixed replace(string $table, string|array $columns, string $search = null, string $replace = null, array $where = null) static 数据替换
 * @method mixed get(string $table, array $join, array $columns = null, array $where = null) static 查询一条记录
 * @method bool has(string $table, array $join, array $where = null) static 判断目标数据是否存在
 * @method bool|int count(string $table, array $join, array $columns = null, array $where = null) static 统计符合条件数据的个数
 * @method bool|int max(string $table, array $join, array $columns = null, array $where = null) static 求最大值
 * @method bool|int min(string $table, array $join, array $columns = null, array $where = null) static 求最小值
 * @method bool|int avg(string $table, array $join, array $columns = null, array $where = null) static 求平均值
 * @method bool|int sum(string $table, array $join, array $columns = null, array $where = null) static 求和
 * @method mixed query(string $query) static 准备sql语句
 * @method array error() static 获取最后一个操作的错误信息
 * @method array log() static 获取全部SQL语句
 * @method string lastQuery() static 获取最后一条查询语句
 * @method array info() static 获取数据库信息
 */
class Db {

    //  数据库连接实例
    private static $instance = [];

    /**
     * 创建数据库链接
     * @param array $conf
     * @param bool $name 如果是true表示强制重新建立链接
     * @return mixed
     * @throws PAException
     */
    public static function connect($conf = [], $name = false) {
        $config = [
            'DB_TYPE'    => Config::get('DB_TYPE'),
            'DB_CHARSET' => Config::get('DB_CHARSET'),
            'DB_NAME'    => Config::get('DB_NAME'),
            'DB_HOST'    => Config::get('DB_HOST'),
            'DB_USER'    => Config::get('DB_USER'),
            'DB_PWD'     => Config::get('DB_PWD'),
            'DB_PORT'    => Config::get('DB_PORT'),
            'DB_PREFIX'  => Config::get('DB_PREFIX'),
            'DB_PARAMS'  => Config::get('DB_PARAMS'),
            'DB_DEBUG'   => Config::get('DB_DEBUG')
        ];
        $config = empty($conf) ? $config : array_merge($config, array_change_key_case($conf, CASE_UPPER));
        if ( empty($config['DB_TYPE']) ) {
            throw new PAException('Underfined db type');
        }
        $name = (false === $name) ? md5(serialize($config)) : $name;

        if (true === $name || !isset(self::$instance[$name])) {
            $handle = null;
            $type = $config['DB_TYPE'] = strtolower($config['DB_TYPE']);
            switch ( $type ){
                case 'mariadb':
                case 'mysql':
                    $handle = new Mysql($config);
                    break;
                case 'pgsql':
                    $handle = new Pgsql($config);
                    break;
                case 'sybase':
                    $handle = new Sybase($config);
                    break;
                case 'oracle':
                    $handle = new Oracle($config);
                    break;
                case 'mssql':
                    $handle = new Mssql($config);
                    break;
                case 'sqlite':
                    $handle = new Sqlite($config);
                    break;
            }
            if( !is_null($handle) ){
                if (true === $name) {
                    return $handle;
                } else {
                    self::$instance[$name] = $handle;
                }
            }else{
                throw new PAException('\\PhalApi\\Core\\DbLib\\'.$type.T('L_CLASS.L_NOT_EXIST'));
            }
        }
        return self::$instance[$name];
    }

    // 调用驱动类的方法
    public static function __callStatic($method, $params) {
        // 自动初始化数据库
        return call_user_func_array([self::connect(), $method], $params);
    }
}