<?php
declare(strict_types=1);
namespace model\common\redis;


use model\common\Redis;

abstract class Str extends Redis
{
	/**
	 * 获取
	 * @return string
	 */
        public function get() : string
        {
                $result = $this->_redis->get($this->_key);
                return !empty($result)?$result:'';
        }
	
	/**
	 * 设置
	 * @param string $value
	 * @return mixed
	 */
        public function set(string $value)
        {
                return $this->_redis->set($this->_key, $value);
        }
	
	/**
	 * 自增
	 * @return mixed
	 */
        public function incrementOne()
        {
                return $this->_redis->incr($this->_key);
        }
	
	/**
	 * 自增设置数
	 * @param int $num
	 * @return mixed
	 */
        public function increment(int $num)
        {
                return $this->_redis->incrby($this->_key, $num);
        }
	
	/**
	 * 自减设置数
	 * @param int $num
	 * @return mixed
	 */
        public function decrement(int $num)
        {
                return $this->_redis->decrby($this->_key, $num);
        }
	
	/**
	 * 自减
	 * @return mixed
	 */
        public function decrementOne()
        {
                return $this->_redis->decr($this->_key);
        }
	
	/**
	 * 追加
	 * @param string $string
	 * @return bool
	 */
        public function append(string $string) : bool
        {
                return $this->_redis->append($this->_key, $string) > 0 ? true : false;
        }
	
	/**
	 * 设置过期时间并设置值
	 * @param int $seconds
	 * @param string $value
	 * @return bool
	 */
        public function setTtl(int $seconds, string $value = '')
        {
                if(!empty($value)) {
                        return $this->_redis->setex($this->_key, $seconds, $value);
                }
                return parent::setExpire($seconds);
        }
}