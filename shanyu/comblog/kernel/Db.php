<?php
namespace Kernel;

use PDO;

class Db
{
    protected $pdo;
    protected $pdos;
    protected $sqls=[]; //保存执行语句
    protected $config=[
        'param'=>[
            //文档:http://php.net/manual/en/pdo.setattribute.php
            PDO::ATTR_CASE              => PDO::CASE_NATURAL,//字段名称大小写
            PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,//抛出异常
            PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,//在获取数据时将空字符串转换成 SQL 中的 NULL
            PDO::ATTR_STRINGIFY_FETCHES => false,//将数值转换为字符串
            PDO::ATTR_EMULATE_PREPARES  => false,//进行参数转义
            PDO::ATTR_PERSISTENT        => false,//开启长连接
            PDO::MYSQL_ATTR_INIT_COMMAND=> 'SET NAMES UTF8',//设置编码
            PDO::ATTR_DEFAULT_FETCH_MODE=> PDO::FETCH_ASSOC,//以数组形式返回数据
        ]
    ];

    // 实例
    protected static $instance;

    public function __construct()
    {

        $config = require BOOT_PATH.'database.php';
        $this->config = array_merge($this->config,$config);
        $this->pdo = new PDO($this->config['dsn'], $this->config['user'], $this->config['pass'],$this->config['param']);
    }
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function close()
    {
        $this->pdo = null;
    }
    public function __destruct()
    {
        $this->close();
    }
    public function __call($method,$args=[])
    {
        if($method == 'query' || $method == 'excuse'){
            $this->sqls[]=reset($args);
        }
        return call_user_func_array([$this->pdo,$method],$args);
    }
    public function getSqls()
    {
        return $this->sqls;
    }
}