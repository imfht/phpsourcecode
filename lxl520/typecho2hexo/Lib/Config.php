<?php
/**
 * config设置
 */
namespace Mohuishou\Lib;
class Config
{
    /**
     * 读取config.php文件当中的配置项
     * @var array
     */
    protected $_configs;

    /**
     * 对象
     * @var
     */
    public static $_instance;

    /**
     * 单例模式，私有化构造函数，防止多次加载
     * Config constructor.
     */
    private function __construct()
    {
        $this->_configs=require_once __DIR__."/../config.php";
        $db=$this->_configs["db"];
        $this->_configs['db']['dsn']="mysql:host=".$db["host"].";dbname=".$db["name"];
    }

    /**
     * 防止对象被复制
     */
    public function __clone(){
        trigger_error('Clone is not allow!',E_USER_ERROR);
    }

    /**
     * 单例方法，创建对象
     * @return Config
     */
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * 获取配置项
     * @param string $arg 配置参数的key
     * @return mixed
     */
    public function get($arg){
        return $this->_configs[$arg];
    }


}
