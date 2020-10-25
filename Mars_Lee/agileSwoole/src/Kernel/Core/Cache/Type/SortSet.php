<?php
declare(strict_types=1);
namespace Kernel\Core\Cache\Type;


use Kernel\Core\Cache\Redis;
use Kernel\Core\Cache\Redis\RedisTrait;

class SortSet extends Redis
{
        use RedisTrait;
        protected $_redis;
        public function __construct(Redis $redis)
        {
                $this->_redis = $redis;
        }
	/**
	 * 设置字段自增
	 * @param string $field
	 * @param int $value
	 * @return bool
	 */
        public function setFieldIncrement(string $field, int $value) : bool
        {
                return $this->_redis->zincrby($this->_key, $value, $field) > 0 ? true : false;
        }
	
	/**
	 * 设置字段值
	 * @param string $field
	 * @param int $value
	 * @return bool
	 */
        public function setFieldValue(string $field, int $value) : bool
        {
                return $this->_redis->zadd($this->_key, $value, $field) > 0 ? true : false;
        }
	
	/**
	 * 批量设置字段值
	 * @param array $values
	 * @return bool
	 */
        public function batchSet(array $values) : bool
        {
        	if (count($values) == 0){
        		return true;
	        }

        	$params = [];
        	foreach ($values as $key=>$value){
        		$params[] = $value;
		        $params[] = $key;
	        }
	        return $this->_redis->zadd($this->_key, $params) > 0 ? true : false;
        }
	
	/**
	 * 获取从$max到最后的所有值（取limit行）
	 * @param int $max
	 * @param int $limit
	 * @return array
	 */
        public function rangeLimit(int $max, int $limit) : array
        {
                $response =  $this->_redis->zrevrangebyscore($this->_key, $max, '-inf','WITHSCORES', 'LIMIT', 0, $limit);
                $result   = [];
                for($i=0,$num=count($response);$i<$num;$i++) {
                        $result[$response[$i]] = intval($response[++$i]);
                }
                return $result;
        }
	
	/**
	 * 设置自增兼容
	 * @param string $member
	 * @param int $increment
	 * @return bool
	 */
        public function increment(string $member,int $increment = 1) :bool
        {
                return $this->setFieldIncrement($member,$increment);
        }
	
	/**
	 * 按区间范围取值
	 * @param int|null $min
	 * @param int|null $max
	 * @return array
	 */
        public function range(int $min=null, int $max=null)
        {
                $min      = is_null($min) ? '-inf' : $min;
                $max      = is_null($max) ? '+inf' : $max;
           
                $response = $this->_redis->zrevrangebyscore($this->_key, $max, $min, 'WITHSCORES');
                $result   = [];
                for($i=0,$num=count($response);$i<$num;$i++) {
                        $result[$response[$i]] = intval($response[++$i]);
                }
                return $result;
        }
	
	/**
	 * 获取指定元素
	 * @param array $members
	 * @return array
	 */
        public function select(array $members) : array
        {
                $result = [];
                foreach($members as $member) {
                        $member = strval($member);
                        $result[$member] = $this->_redis->zscore($this->_key, $member);
                }
                return $result;
        }
	
	/**
	 * 统计区间范围内元素数量
	 * @param $min
	 * @param $max
	 * @return mixed
	 */
        public function count($min=0, $max=-1)
        {
                if($min!=0 or $max!=-1) {
                        return $this->_redis->zcount($this->_key, $min, $max);
                } else {
                        return $this->_redis->zcard($this->_key);
                }
        }
	
	/**
	 * 判断值存在否
	 * @param string $member
	 * @return bool
	 */
        public function existsField(string $member) : bool
        {
                return $this->_redis->zscore($this->_key, $member)===null ? false : true;
        }

        /**
         * 获取排名
         * @param string $member
         * @param string $order
         * @return mixed
         */
        public function getRank(string $member, $order = 'desc')
        {
                $method = $order === 'desc' ? 'zrevrank' : 'zrank';
                $response = $this->_redis->$method($this->_key, $member);

                return $response !== null ? $response+1 : null;
        }

	/**
	 * 从值获取元素
	 * @param string $member
	 * @return string
	 */
        public function getFieldValue(string $member)
        {
        	$score = $this->_redis->zscore($this->_key, $member);
                return $score!==null ? $score: 0;
        }
	
	/**
	 * 删除元素
	 * @param string $member
	 * @return bool
	 */
        public function delField(string $member) : bool
        {
	        $result = $this->_redis->zrem($this->_key, $member);
	        return $result>0?true:false;
        }
	
	/**
	 *获取所有member
	 */
        public function getAllMember(int $start = 0, int $end = -1) : array
        {
        	$result = $this->_redis->zrange($this->_key, $start, $end);
        	return !empty($result)?$result:[];
        }

}