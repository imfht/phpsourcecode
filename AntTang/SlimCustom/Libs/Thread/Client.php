<?php
/**
 * 线程客户端
 * 
 * @package     Client.php
 * @author      Jing <tangjing3321@gmail.com>
 * @version     1.0
 * @date        2018年3月29日
 */

namespace SlimCustom\Libs\Thread;

class Client extends \Thread
{
    /**
     * 线程任务代码
     * 
     * @var \Closure
     */
    private $closure;
    
    /**
     * 闭包参数
     * 
     * @var array
     */
    private $args;
    
    /**
     * 初始化线程
     * 
     * @param array $args
     * @param \Closure $closure
     */
    public function __construct($args, \Closure $closure){
        $this->closure = $closure;
        $this->args = serialize($args);
    }
    
    /**
     * 启动线程
     */
    public function run(){
        call_user_func_array($this->closure, unserialize($this->args));
    }
}