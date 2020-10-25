<?php
/**
 * mysql数据库操作类
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date 2013-1-19
 */
namespace framework\database;

use framework\core\Object;

abstract class DB extends Object
{
    public $prefix = '';        //表前缀
    public $table_name ='';     //表名
    protected $dbname='';     //数据库名
    protected $last_sql='';     //最后一次执行的sql
    protected $dbhost = '';
    protected $dbuser = '';
    protected $dbpsw = '';
    protected $charset = 'utf8';
    protected $pconnect = 0;
    /**
     * 构造函数 连接数据可
     * @param array $config
     */
    public function __construct(array $config){
        if(is_array($config)){
            foreach ($config as $key=>$val){
                if(isset($this->$key))
                    $this->$key = $val;
            }
        }
        $this->connect();
    }
    /**
     * 数据库连接
     */
    abstract public function connect();
     
    /**
     * 执行基本的 mysql查询 
     * 并返回结果集
     * @param string $sql
     * @return mixed
     */
    abstract function query($sql);
    /**
     * 执行mysql instert update 语句 
     * 并返回影响行数
     * @param string $sql
     * @return mixed
     */
    abstract public function execute($sql);
    /**
     * 获取最后一次添加数据的主键号
     */
    abstract public function insertId();

    /**
     * 关闭数据库连接
     * @return bool
     */
    abstract public function close();
    /**
     * 输出错误提示信息
     * @param string $message
     * @param string $sql
     * @return string
     */
    protected function errorMsg($message='',$sql='')
    {
        return $message.'<br/>SQL:'.$sql;
    }

}