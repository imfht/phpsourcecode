<?php
/**
 * 异常基础类
 *
 * @author chengxuan <i@chengxuan.li>
 */
namespace Exception;
abstract class Abs extends \Exception {
    
    /**
     * 元数据
     * 
     * @var array
     */
    protected $_metadata = array();
   
    /**
     * 构造方法
     * 
     * @param string     $message
     * @param number     $code
     * @param array      $metadata
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, array $metadata = [], \Exception $previous = NULL) {
        parent::__construct($message, $code, $previous);
        $this->_metadata = $metadata;
    }
    
    /**
     * 获取元数据
     * 
     * @return array
     */
    public function getMetadata() {
        return $this->_metadata;
    }
}
