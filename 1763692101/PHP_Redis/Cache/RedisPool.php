<?php
/*
* 连接缓存的连接池类，只连接缓存数据库与关闭数据库，针对于Redis
*
* @copyright 2016
* @package Cache
* @version 1.0
*/

class  Cache_RedisPool
{
    protected $_host;
    protected $_port;
    //protected $_user;
    protected $_pass;
    protected $_db;
    
    protected static $_cache_handle; //连接handle
    
    public function __construct()
    {
        $this->_host = '127.0.0.1';
        $this->_prot = 6379;
        //$this->_user = '';
        $this->_pass = '';
        $this->_db = 3;
    }
    
    public function get_cache_handle()
    {
        return self::$_cache_handle;
    }
    
    /*
    * 连接数据库 一个PHP的处理 ，只使用一个连接
    * @return integer 错误代码
    */
    public function getConnection()
    {
        //一个PHP的处理 ，只使用一个连接
        if (!isset(self::$_cache_handle))
        {
            //连接,每次都返回新的连接
            $redis = new Redis();
            if ($redis->connect($this->_host, $this->_port))
            {
                if ($this->_pass)
                {
                    self::$_cache_handle = $redis->auth($this->_pass) ? $redis : NULL;
                }
                else
                {
                    self::$_cache_handle = $redis;
                }
            }
            else
            {
                self::$_cache_handle = NULL;
            }
            
            if (self::$_cache_handle === NULL)
            {
                //连接失败，记录错误日志
				echo "open redis error";
            }
            else
            {
                self::$_cache_handle->select($this->_db);
            }
        }
        
        return self::$_cache_handle;
    }
    
    /*
    * 关闭数据库连接
    * @return(integer)[错误代码]
    */
    public static function closeConnection()
    {
        if (isset(self::$_cache_handle))
        {
            self::$_cache_handle->close();
            self::$_cache_handle = null;
        }
        return ;
    }
}