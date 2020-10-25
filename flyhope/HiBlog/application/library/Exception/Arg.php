<?php

/**
 * 参数异常
 *
 * @package Exception
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Exception;
class Arg Extends Abs {
    
    /**
     * 异常代码：参数为空
     * 
     * @var int
     */
    const CODE_NULL = 100011;
    
    /**
     * 异学中代码：参数过滤错误
     * 
     * @var int
     */
    const CODE_FILTER = 100012;
    
    /**
     * 参数错误原始消息
     * 
     * @var string
     */
    protected $_ori_message = '';
    
    /**
     * 构造方法
     * 
     * @param string $message  消息
     * @param int    $code     错误代码
     * @param string $arg_name 参数名称
     */
    public function __construct($message, $code, $arg_name) {
        $this->_ori_message = $message;
        $message = $message .= " ({$arg_name})";
        parent::__construct($message, $code, ['arg_name' => $arg_name]);
    }
    
} 