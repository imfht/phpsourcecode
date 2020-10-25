<?php
declare(strict_types=1);
namespace Kernel\Core\Cache\Type;


use Kernel\Core\Cache\Redis;

class Lists extends Redis implements \Iterator
{
	protected $_length = 0;
	protected $_position = 0;
	protected $_hasGetLen = false;
	
	/**
	 * 获取队列的长度
	 * @return int
	 */
        public function getLength()
        {
        	if(!$this->_hasGetLen) {
        		$this->_hasGetLen = true;
		        $this->_length = $this->_redis->llen($this->_key);
	        }
                return $this->_length;
        }
	
	/**
	 * 获取第N个元素
	 * @param int $index
	 * @return string
	 */
        public function getByIndex(int $index) : string
        {
                $value = $this->_redis->lindex($this->_key, $index);
                return !empty($value)?$value:'';
        }
	/**
	 * 出栈第一个元素
	 * @return string
	 */
        public function getFisrt() : string
        {
                $value = $this->_redis->lpop($this->_key);
                return !empty($value)?$value:'';
        }
	
	/**
	 * 出栈最后一个元素
	 * @return string
	 */
        public function getEnd() : string
        {
                $value = $this->_redis->rpop($this->_key);
                return !empty($value)?$value:'';
        }
	
	/**
	 * 从队列头部添加
	 * @param \string[] ...$value
	 * @return bool
	 */
        public function setFirst(string ...$value) : bool
        {
                return $this->_redis->lpush($this->_key, $value) > 0 ? true : false;
        }
	
	/**
	 * 从队列头部添加
	 * @param array $values
	 * @return mixed
	 */
	public function setFirstArray(array $values)
	{
		return $this->_redis->lpush($this->_key, $values);
	}
	
	/**从队列末尾添加
	 * @param \string[] ...$value
	 * @return mixed
	 */
        public function setEnd(string ...$value)
        {
                return $this->_redis->rpush($this->_key, $value);
        }
	
	/**
	 * 从队列末尾添加
	 * @param array $values
	 * @return mixed
	 */
        public function setList(array $values)
        {
        	return $this->_redis->rpush($this->_key, $values);
        }
	
	/**
	 * 获取整个队列
	 * @return array
	 */
        public function getAll() : array
        {
                $return =  $this->getArea(0,-1);
                return !empty($return)?$return:[];
        }
	
	/**
	 * 获取范围内值
	 * @param int $start
	 * @param int $end
	 * @return array
	 * @throws \Exception
	 */
        public function getArea(int $start,int $end) : array
        {
                $len = $this->getLength();
                if($end!='-1' and $end>$len) {
                        throw new \Exception('end > len');
                }
                $response = $this->_redis->lrange($this->_key, $start, $end);
                return !empty($response)?$response:[];
        }

        public function setIndex(int $index, string $value)
        {
                return $this->_redis->lset($this->_key, $index, $value);
        }

        public function delCountValue(int $count, string $value) : bool
        {
                return $this->_redis->lrem($this->_key, $count, $value) > 0 ? true : false;
        }
	
	public function current()
	{
		return $this->getByIndex($this->_position);
	}
	
	public function next()
	{
		if($this->valid()){
			$this->_position++;
		}
	}
	
	public function key()
	{
		return $this->_position;
	}
	
	public function valid()
	{
		$len = $this->getLength();
		if($this->_position < $len) {
			return true;
		}
		return false;
	}
	
	public function rewind()
	{
		$this->_position = 0;
	}
}