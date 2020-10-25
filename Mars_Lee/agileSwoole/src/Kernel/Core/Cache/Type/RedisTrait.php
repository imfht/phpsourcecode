<?php


namespace Kernel\Core\Cache\Type;


trait RedisTrait
{
        protected $_key;
        /* @var \Redis $_redis */
        protected $_redis;
        public function select(int $db)
        {
                $this->_redis->select($db);
        }
        public function setKey(string $key) {
                $this->_key = $key;
        }

        public function getKey() : string
        {
                return $this->_key;
        }

        /**
         * 是否存在key
         * @return bool
         */
        public function hasKey() : bool
        {
                return $this->_redis->exists($this->_key) > 0 ? true : false;
        }

        /**
         * 删除key
         * @return bool
         */
        public function delKey() : bool
        {
                echo $this->_key.PHP_EOL;
                return $this->_redis->del($this->_key) > 0 ? true : false;
        }

        /**
         * 设置多少时间后过期
         * @param int $seconds
         * @return bool
         */
        public function setExpire(int $seconds) : bool
        {
                return $this->_redis->expire($this->_key, $seconds) > 0 ? true : false;
        }

        /**
         * 设置过期时间点
         * @param int $time
         * @return bool
         */
        public function setExpireTime(int $time) : bool
        {
                return $this->_redis->expireat($this->_key, $time) > 0 ? true : false;
        }

        /**
         * 获取剩余过期时间
         * @return int
         */
        public function getTtl() : int
        {
                return $this->_redis->ttl($this->_key);
        }
}