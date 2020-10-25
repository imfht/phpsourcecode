<?php
//队列
class Cache_RedisList extends Cache_RedisCache
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /* 
    * 入列
    * @param String $push_value 需入列数据
    * @return int 队列长度
    */
    public function leftPush($push_value)
    {
        return $this->_redis->lPush($this->_cache_key, $push_value);
    }
    
    /* 
    * 出列
    * @return string 返回列表保存在key的最后一个元素
    */
    public function rightPop()
    {
        return $this->_redis->lPop($this->_cache_key);
    }
    
    /* 
    * 列尾阻塞出列
    * @param int $timeout 阻塞时间 0表示一直阻塞下去直到有消息
    * @return string 数据
    */
    public function rightPop($timeout)
    {
        return $this->_redis->brPop($this->_cache_key, $timeout);
    }
    
    /* 
    * 取指定范围的队列数据
    * @param int $start    开始的ID
    * @param int $end    结束的ID    -1表示到最后一条
    * @return array 
    */
    public function getListByRange($start, $end)
    {
        return $this->_redis->lRange($this->_cache_key, $start, $end);
    }

    /* 
    * 限制队列到指定的长度
    * @param int $start    开始位置
    * @param int $end    结束位置
    * @return array or bool(false)
    */
    public function trimList($start, $end)
    {
        return $this->_redis->lTrim($this->_cache_key, $start, $end);
    }

    /* 
    * 取队列长度
    * @return int 队列长度
    */
    public function getLen()
    {
        return $this->_redis->lLen($this->_cache_key);
    }   
}
?>