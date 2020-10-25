<?php
declare(strict_types=1);
namespace Kernel\Core\Cache\Type;


use Kernel\Core\Cache\Redis;

class Hash
{

        use RedisTrait;
        protected $_redis;
        public function __construct(Redis $redis)
        {
                $this->_redis = $redis->get();
        }
	/**
	 * 判断key是否存在
	 * @param string $field
	 * @return bool
	 */
        public function existsField(string $field) : bool
        {
                return $this->_redis->hexists($this->_key, $field) ? true : false;
        }
	
	/**
	 * 获取key的值
	 * @param string $field
	 * @return string
	 */
        public function getField(string $field) : string
        {
                $value = $this->_redis->hget($this->_key, $field);
                return empty($value) ? '' : strval($value);
        }
	
	/**
	 * 设置key自增
	 * @param string $field
	 * @param int $num
	 * @return int
	 */
        public function increment(string $field, int $num) : int
        {
                return intval($this->_redis->hincrby($this->_key, $field, $num));
        }
	
	/**
	 * 获取所有的key
	 * @return array
	 */
        public function getFields() : array
        {
                return $this->getKeys();
        }
	
	/**
	 * 获取某key值
	 * @param string $field
	 * @return mixed
	 */
        public function getFieldValue(string $field)
        {
                return $this->_redis->hget($this->_key, $field);
        }
	
	/**
	 * 批量获取值
	 * @param array $fields
	 * @return array
	 */
        public function getFieldsValues(array $fields) : array
        {
               return $this->_redis->hmget($this->_key, $fields);
        }
	
	/**
	 * 获取所有数据
	 * @return mixed
	 */
        public function getAll()
        {
                return $this->_redis->hgetall($this->_key);
        }
	
	/**
	 * 设置key=>value
	 * @param string $field
	 * @param string $value
	 * @return mixed
	 */
        public function setField(string $field, string $value)
        {
                return $this->_redis->hset($this->_key, $field, $value);
        }
	
	/**
	 * 批量设置key=>value
	 * @param array $fields
	 * @param array $values
	 * @return mixed
	 * @throws \Exception
	 */
        public function setFields(array $fields,array $values)
        {
                $count = count($fields);
                if($count!=count($values)) {
                        throw new \Exception('fields num != values num');
                }
                $params = [];
                for ($i=0; $i<$count; $i++) {
                        $params[] = $fields[$i];
                        $params[] = $values[$i];
                }
                return $this->_redis->hmset($this->_key, $params);
        }
	
	/**
	 * 删除key
	 * @param string $field
	 * @return bool
	 */
        public function delField(string $field) : bool
        {
                return $this->_redis->hdel($this->_key, $field) > 0 ? true : false;
        }

	
	/**
	 * 批量删除列
	 * @param array $fields
	 */
	public function delFieldsArray(array $fields)
	{
	        foreach ($fields as $field) {
                        $this->_redis->hdel($this->_key, $field);
                }
	}
	
	/**
	 * 获取所有的key
	 * @return array
	 */
        public function getKeys() : array
        {
	        $keys = $this->_redis->hkeys($this->_key);
	        return !empty($keys)?$keys:[];
        }
	
	/**
	 * 获取长度
	 * @return int
	 */
        public function getLength() : int
        {
        	return intval($this->_redis->hlen($this->_key));
        }
	
	/**
	 * 仅当key不存在时写入
	 * @param string $field
	 * @param string $value
	 * @return mixed
	 */
        public function noExistsInsert(string $field, string $value)
        {
	        return $this->_redis->hsetnx($this->_key, $field, $value);
        }
	
	/**
	 * @return mixed
	 */
        public function clear()
        {
        	return $this->_redis->del($this->_key);
        }
}