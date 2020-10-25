<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14-6-29
 * Time: 下午7:23
 */
namespace framework\core;

trait Singleton
{
    /** @var array 实例化对象存储数组*/
    private static $_instance = [];

    /**
     * 获取类的 实例
     * @return mixed
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$_instance[$class])) {
            self::$_instance[$class] = new $class();
        }
        return self::$_instance[$class];
    }
    /**
     * 销毁当前实例
     */
    public function __destruct()
    {
        $class = get_class($this);
        unset(self::$_instance[$class], $this);
    }
    /**
     * 禁止在外部直接 实例化
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
        if(method_exists($this, 'init')){
            $this->init();
        }
    }
    
    /**
     * 禁止 clone
     */
    final public function __clone()
    {
        trigger_error( 'Clone is not allowed.', E_USER_ERROR );
    }
    
    /**
     *  禁止 unserialize 
     */
    private function __wakeup()
    {
    }
    
}