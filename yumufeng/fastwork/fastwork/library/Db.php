<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/5
 * Time: 22:02
 */

namespace fastwork;


use fastwork\db\Query;
use fastwork\facades\Config;
use traits\Pools;

/**
 * Class Db
 * @package fastwork
 * @method Query name($table) static 数据库表，不带前缀
 * @method Query  transaction(\Closure $success, \Closure $fail)  static 事务执行
 * @method Query query(Array $params) 执行原始SQL语句
 */
class Db
{

    use Pools;
    //配置
    public $config = [
        //服务器地址
        'host' => '127.0.0.1',
        //端口
        'port' => 3306,
        //用户名
        'user' => '',
        //密码
        'password' => '',
        //数据库编码，默认为utf8
        'charset' => 'utf8',
        //数据库名
        'database' => '',
        //表前缀
        'prefix' => 'fast_',
        //空闲时，保存的最大链接，默认为5
        'poolMin' => 5,
        //地址池最大连接数，默认1000
        'poolMax' => 1000,
        //清除空闲链接的定时器，默认60s
        'clearTime' => 60,
        //空闲多久清空所有连接,默认300s
        'clearAll' => 300,
        //设置是否返回结果
        'setDefer' => true
    ];

    public static function start()
    {
        $app = new static();
        $app->init(Config::get('db.mysql'));
        return $app;
    }

    /**
     * 创建连接池
     */
    protected function createPool()
    {
        //无空闲连接，创建新连接
        $mysql = new \Swoole\Coroutine\Mysql();

        $mysql->connect([
            'host' => $this->config['host'],
            'port' => $this->config['port'],
            'user' => $this->config['user'],
            'password' => $this->config['password'],
            'charset' => $this->config['charset'],
            'database' => $this->config['database']
        ]);

        return $mysql;
    }


    public static function __callStatic($name, $arguments)
    {
        $className = "\\fastwork\\db\\Query";
        return call_user_func_array([new $className, $name], $arguments);
    }

}