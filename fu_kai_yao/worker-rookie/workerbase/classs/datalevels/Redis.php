<?php
namespace workerbase\classs\datalevels;

use workerbase\classs\Config;

/**
 * 依赖redis扩展
 * 推荐使用Redis::getInstance()形式
 */
class Redis
{
	/**
	 * 无穷大
	 * @var string
	 */
	const  MAX_VALUE = '+inf';

	/**
	 * 无穷小
	 * @var string
	 */
	const  MIN_VALUE = '-inf';

	/**
	 * 聚合类型，求和
	 * @var string
	 */
	const AGGREGATE_TYPE_SUM = 'SUM';

	/**
	 * 聚合类型，最大值
	 * @var string
	 */
	const AGGREGATE_TYPE_MAX = 'MAX';

	/**
	 * 聚合类型，最小值
	 * @var string
	 */
	const AGGREGATE_TYPE_MIN = 'MIN';

    /**
     * redis
     * @var Redis
     */
    private static $_instance = null;

    /**
     * redis链接
     * @var \Redis
     */
    private $_redis = null;

    private $_conf = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'timeout'    => 3,
        'persistent' => false,
    ];

    public function __construct($options = [])
    {
        $this->_conf = Config::getInstance()->get('redis', 'config');//redis配置
        if (!empty($options)) {
            $this->_conf = array_merge($this->_conf, $options);
        }

        if (empty($this->_conf) || !isset($this->_conf['host']) || !isset($this->_conf['port'])) {
            throw new \Exception("redis config invalid.");
        }
        try{
            $isOk = 0;
            $count = 3;
            while ($count--) {
                if (!$this->_connect()) {
                    //尝试短连接
                    usleep(333 * 1000);
                    continue;
                }
                $isOk = 1;
                break;
            }

            if (!$isOk) {
                throw new \Exception("connect to redis failure");
            }
        } catch (\RedisException $e) {
            throw new \Exception("connect to redis failure");
        }
    }

    /**
     * 连接redis
     * @param array $options 连接参数
     */
    private function _connect()
    {
        if (null === $this->_redis) {
            $this->_redis = new \Redis();
        }

        $select = isset($this->_conf['select']) ? $this->_conf['select'] : 0;
        $ret = false;
        if (true == $this->_conf['persistent']) {
            $ret = $this->_redis->pconnect($this->_conf['host'], $this->_conf['port'], $this->_conf['timeout'], 'persistent_id_' . $select);
        } else {
            $ret = $this->_redis->connect($this->_conf['host'], $this->_conf['port'], $this->_conf['timeout']);
        }

        //密码不为空, 则需要密码验证
        if (!empty($this->_conf['password'])) {
            $ret = $this->_redis->auth($this->_conf['password']);
        }

        $ret = $this->_redis->select($select);

        //序列化
//        $this->_redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        return $ret;
    }

    /**
     * 获取redis实例
     * @param array $options 连接参数
     * @param bool $isFlush 强制重新连接
     * @return Redis
     */
    public static function getInstance($options = [], $isFlush = false)
    {
        if (true == $isFlush || null === self::$_instance) {
            if ($isFlush) {
               return new Redis($options);
            }
            self::$_instance = new Redis($options);
        }

        if (PHP_SAPI == 'cli') {
            try{
                self::$_instance->getOriginInstance()->ping();//测试一下连接是否失效
            }catch (\RedisException $e) {
                self::$_instance->_redis = null;
                self::$_instance->_connect();
            }
        }
        return self::$_instance;
    }

    /**
     * 清除连接实例
     * @access public
     * @return void
     */
    public static function clearInstance()
    {
        self::$_instance = null;
    }

    /**
     * 返回redis原始实例
     * @return \Redis
     */
    public function getOriginInstance()
    {
        return $this->_redis;
    }

    /**
     * 建立一个新的redis实例
     * @author fukaiyao
     * @return Redis
     */
    public static function buildNewRedis($db_table = 0)
    {
        //对相应的redis库进行单列处理
        static $redis_table_instance = array();
        if (isset($redis_table_instance[$db_table])) {
            return $redis_table_instance[$db_table];
        }
        $redis = new \Redis();
        $conf = Config::getInstance()->get('redis', 'config');//redis配置

        $ret = $redis->connect($conf['host'], $conf['port'], $conf['timeout']);

        //密码不为空, 则需要密码验证
        if (!empty($conf['password'])) {
            $ret = $redis->auth($conf['password']);
        }

        //序列化
//        $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        $redis->select($db_table);

        $redis_table_instance[$db_table] = $redis;
        return $redis_table_instance[$db_table];
    }

    /**
     * Get the value related to the specified key
     * @param string $key
     * @return String or Bool: If key didn't exist, FALSE is returned. Otherwise, the value related to this key is returned.
     */
    public function get($key)
    {
        return $this->_redis->get($key);
    }


    /**
     * Get the values of all the specified keys. If one or more keys dont exist, the array will contain FALSE at the position of the key.
     * @param array $keys       - key数组
     * @return Array: Array containing the values related to keys in argument
     */
    public function mget($keys)
    {
        return $this->_redis->mget($keys);
    }

    /**
     * Set the string value in argument as value of the key
     * @param string $key
     * @param mixed $value        -   任意值
     * @param int $expire         -   过期时间，单位秒
     * @return bool 成功返回true
     */
    public function set($key, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = 0;
        }

        if ($expire > 0 || is_array($expire)) {
            return $this->_redis->set($key, $value, $expire);
        }
        return $this->_redis->set($key, $value);
    }

    /**
     * 设置key值，如果key存在则失败
     * @param string $key
     * @param mixed $value        -   任意值
     * @param int $expire         -   过期时间，单位秒
     * @return bool 成功返回true, key存在返回false
     */
    public function setNx($key, $value, $expire = 0)
    {
        $ret = $this->_redis->setNx($key, $value);
        if (true === $ret && $expire > 0) {
            $this->_redis->setTimeout($key, $expire);
        }
        return $ret;
    }

    /**
     * Verify if the specified key exists.
     * @param string $key
     * @return BOOL: If the key exists, return TRUE, otherwise return FALSE.
     */
    public function exists($key)
    {
        return $this->_redis->exists($key);
    }

    /**
     * Sets a value and returns the previous entry at that key.
     * @param string $key
     * @param mixed $value        -   任意值
     * @param int $expire         -   过期时间，单位秒
     */
    public function getSet($key, $value, $expire = 0)
    {
        $prev = $this->_redis->getSet($key, $value);
        if ($expire > 0) {
            $this->_redis->setTimeout($key, $expire);
        }
        return $prev;
    }

    /**
     *  增加key 的值
     * @param string $key
     * @param number $step      - 增加步长，每次加多少
     * @param int $expire            - 超时时间
     * @return int   最新值
     */
    public function incr($key, $step = 1, $expire = 0)
    {
        $ret = false;
        if ($step > 1) {
            $ret =  $this->_redis->incrBy($key, $step);
        }
        else {
            $ret = $this->_redis->incr($key);
        }

        if (false !== $ret && $expire > 0) {
            $this->_redis->setTimeout($key, $expire);
        }
        return $ret;
    }

    /**
     *  减少key 的值
     * @param string $key
     * @param number $step      - 减少步长，每次减多少
     * @param int $expire            - 超时时间
     * @return int   最新值
     */
    public function decr($key, $step = 1, $expire = 0)
    {
        $ret = false;
        if ($step > 1) {
            $ret =  $this->_redis->decrBy($key, $step);
        }
        else {
            $ret = $this->_redis->decr($key);
        }

        if (false !== $ret && $expire > 0) {
            $this->_redis->setTimeout($key, $expire);
        }
        return $ret;
    }



    /**
     * Remove specified keys.
     * @param string $key           - 批量删除，传递key数组
     * @return mixed - Long Number of keys deleted.
     */
    public function delete($key)
    {
        return $this->_redis->delete($key);
    }

    /**
     * 重命名key
     * @param $srcKey       - 原key
     * @param $dstKey       - 改名后的key
     * @return bool     - 成功返回true
     */
    public function rename($srcKey, $dstKey)
    {
        return $this->_redis->rename($srcKey, $dstKey);
    }

    /**
     * 设置key超时时间，单位秒
     * @param string $key			- 设置key超时时间
     * @param int $expire			- 不限制设置-1
     * @return bool 成功返回true
     */
    public function setTimeout($key, $expire)
    {
    	return $this->_redis->setTimeout($key, $expire);
    }

    /**
     * 获取该key在redis里的剩余存活时间,单位秒
     * @param string $key     -key
     * @return int|false      -成功返回时间
     */
    public function ttl($key)
    {
        $restTime=$this->_redis->ttl($key);
        if($restTime>=0)
        {
            return $restTime;
        }
        else
        {
            return false;
        }
    }

    /**
     * hash表操作 - 删除hash字段
     * @param string  $key
     * @param string $field  - hash字段
     * @return LONG the number of deleted keys, 0 if the key doesn't exist, FALSE if the key isn't a hash.
     */
    public function hDel($key, $field)
    {
        return $this->_redis->hDel($key, $field);
    }

    /**
     * hash表操作 - 检测hash 字段是否存在
     * @param string  $key
     * @param string $field   - hash字段
     * @return BOOL: If the member exists in the hash table, return TRUE, otherwise return FALSE.
     */
    public function hExists($key, $field)
    {
        return $this->_redis->hExists($key, $field);
    }

    /**
     * hash表操作 - 获取hash字段的值
     * @param string  $key
     * @param string $field	- 字段
     * @return mixed | false	- 成功返回值，失败返回false
     */
    public function hGet($key, $field)
    {
        return $this->_redis->hGet($key, $field);
    }

    /**
     * hash表操作 - 获取hash表中所有字段值
     * @param string  $key
     * @return array
     */
    public function hGetAll($key)
    {
        return $this->_redis->hGetAll($key);
    }

    /**
     * hash表操作 - 增加hash字段的数值
     * @param string  $key
     * @param string $field   - hash字段
     * @param number $step      - 增加步长，每次加多少
     * @param int $expire            - 超时时间
     * @return LONG 返回新值
     */
    public function hIncrBy($key, $field, $step = 1, $expire = 0)
    {
        $ret = $this->_redis->hIncrBy($key, $field, $step);
        if (false !== $ret && $expire > 0) {
            $this->_redis->setTimeout($key, $expire);
        }
        return $ret;
    }

    /**
     * hash表操作 - 返回全部hash字段
     * @param string $key
     * @return array
     */
    public function hKeys($key)
    {
        return $this->_redis->hKeys($key);
    }

    /**
     * hash表操作 - 返回hash的字段数
     * @param string $key
     * @return int key不存在返回false
     */
    public function hLen($key)
    {
        return $this->_redis->hLen($key);
    }

    /**
     * hash表操作 - 批量获取hash的字段值
     * @param string $key
     * @param array $fields       -    hash字段
     * @return boolean|mixed
     */
    public function hMGet($key, array $fields)
    {
        if (empty($fields)) {
            return false;
        }
        return $this->_redis->hMget($key, $fields);
    }

    /**
     * hash表操作 - 批量设置hash字段值
     * @param string $key
     * @param array $fields         -   hash字段数组， 例：array('uid' => 1, 'name' => 'lewaimai')
     * @param int $expire             -   过期时间，单位秒
     * @return bool 成功返回true
     */
    public function hMSet($key, array $fields, $expire = 0)
    {
        if (empty($fields)) {
            return false;
        }
        $ret = $this->_redis->hMset($key, $fields);
        if (false !== $ret && $expire > 0) {
            $this->_redis->setTimeout($key, $expire);
        }
        return $ret;
    }

    /**
     * hash表操作 - 设置hash字段值
     * @param string $key
     * @param string $field         -   hash字段
     * @param mixed $value                 - 任意值
     * @param int $expire                 -   过期时间，单位秒
     * @return bool 成功返回true
     */
    public function hSet($key, $field, $value, $expire = 0)
    {
        $ret = $this->_redis->hSet($key, $field, $value);
        if (false !== $ret && $expire > 0) {
            $this->_redis->setTimeout($key, $expire);
        }
        return $ret;
    }

    /**
     * hash表操作 - 设置hash字段值, 如果字段存在则失败。
     * @param string $key
     * @param string $field         -   hash字段
     * @param mixed $value                 - 任意值
     * @param int $expire                 -   过期时间，单位秒
     * @return bool 成功返回true， hash字段已经存在
     */
    public function hSetNx($key, $field, $value, $expire = 0)
    {
        $ret = $this->_redis->hSetNx($key, $field, $value);
        if (true === $ret && $expire > 0) {
            $this->_redis->setTimeout($key, $expire);
        }
        return $ret;
    }

    /**
     * list操作 - 获取指定位置的元素
     * @param string $key
     * @param int $index			- 索引位置从0开始, -1代表最后一个
     * @return mixed | false 	-成功返回结果，失败返回false
     */
    public function lGet($key, $index)
    {
    	return $this->_redis->lGet($key, $index);
    }

    /**
     * list操作 - 在指定元素之前或之后插入元素
     * @param string $key
     * @param mixed $pivot			- 参考元素，在该元素之前或者之后插入， 如果该元素不存在，则放弃插入
     * @param mixed $value			- 待插入元素
     * @param int $posType	- 插入类型，\Redis::BEFORE 或则 \Redis::AFTER
     * @return int  成功返回list元素个数，失败返回-1表示$pivot不存在。
     */
    public function lInsert($key,  $pivot, $value, $posType = \Redis::AFTER)
    {
    	return $this->_redis->lInsert($key, $posType, $pivot, $value);
    }

    /**
     * list操作 - 根据范围 [$start, $end] 获取元素
     * 例如：获取全部数据的范围条件 [0, -1]
     * @param string $key
     * @param int $start		- 开始位置
     * @param int $end		- 结束位置
     * @return array
     */
    public function lRange($key, $start, $end)
    {
    	return $this->_redis->lRange($key, $start, $end);
    }

    /**
     * list操作 - 返回list元素个数
     * @param string $key
     * @return int | false 成功返回元素个数，失败返回false
     */
    public function lSize($key)
    {
    	return $this->_redis->lSize($key);
    }

    /**
     * list操作 - 从head弹出一个元素
     * @param string $key
     * @return mixed | false 成功返回元素，失败或则为空返回false
     */
    public function lPop($key)
    {
    	return $this->_redis->lPop($key);
    }

    /**
     * list操作 - 从head插入一个元素， list不存在则创建
     * @param string $key
     * @param mixed $value		- 元素
     * @param int $expire		- 超时时间，0不限制
     * @return int | false 成功返回list元素个数
     */
    public function lPush($key, $value, $expire = 0)
    {
    	$ret = $this->_redis->lPush($key, $value);
    	if (false !== $ret && $expire > 0) {
    		$this->_redis->setTimeout($key, $expire);
    	}
    	return $ret;
    }

    /**
     * list操作 - 从head插入一个元素， list不存在则放弃写入
     * @param string $key
     * @param mixed $value		- 元素
     * @param int $expire		- 超时时间，0不限制
     * @return int | false 成功返回list元素个数
     */
    public function lPushx($key, $value)
    {
    	return $this->_redis->lPushx($key, $value);
    }

    /**
     * list操作 - 从list中删除前面$count个$value元素
     * @param string $key
     * @param string $value		- 待删除元素， 从head开始查找
     * @param int $count				- 需要删除几个元素, 0则全部删除
     * @return int | false	成功返回删除元素个数
     */
    public function lRem($key, $value, $count)
    {
    	return $this->_redis->lRem($key, $value, $count);
    }

    /**
     * list操作 - 设置list指定位置的值
     * @param string $key
     * @param int $index			- 索引，从0开始
     * @param mixed $value
     * @param int $expire		- 超时时间，0不限制
     * @return bool 成功返回true
     */
    public function lSet($key, $index, $value, $expire = 0)
    {
    	$ret =  $this->_redis->lSet($key, $index, $value);
    	if (false !== $ret && $expire > 0) {
    		$this->_redis->setTimeout($key, $expire);
    	}
    	return $ret;
    }

    /**
     * list操作 - 根据范围缩小list数据
     * @param string $key
     * @param int $start			- 开始索引
     * @param int $stop			- 结束索引
     * @return bool 成功返回true
     */
    public function lTrim($key, $start, $stop)
    {
    	return $this->_redis->lTrim($key, $start, $stop);
    }

    /**
     * list操作 - 从表尾弹出元素
     * @param string $key
     * @return mixed | false 成功返回元素，失败或则为空返回false
     */
    public function rPop($key)
    {
    	return $this->_redis->rPop($key);
    }

    /**
     * list操作 - 从表尾插入一个元素， list不存在则创建
     * @param string $key
     * @param mixed $value		- 元素
     * @param int $expire		- 超时时间，0不限制
     * @return int | false 成功返回list元素个数
     */
    public function rPush($key, $value, $expire = 0)
    {
    	$ret = $this->_redis->rPush($key, $value);
    	if (false !== $ret && $expire > 0) {
    		$this->_redis->setTimeout($key, $expire);
    	}
    	return $ret;
    }

    /**
     * list操作 - 从表尾插入一个元素， list不存在则放弃写入
     * @param string $key
     * @param mixed $value		- 元素
     * @return int | false 成功返回list元素个数
     */
    public function rPushX($key, $value)
    {
    	return $this->_redis->rPushX($key, $value);
    }

    /**
     * list操作 - 把$key1列表的最后一个元素移动到$key2列表的头部
     * @param string $key1
     * @param string $key2
     * @return mixed | false 成功返回移动的元素，失败则返回false
     */
    public function rpoplpush($key1, $key2)
    {
    	return $this->_redis->rpoplpush($key1, $key2);
    }

    /**
     * list操作 - 阻塞地把$key1列表的最后一个元素移动到$key2列表的头部
     * @param string $key1 出队列1右
     * @param string $key2 进入队列2左
     * @param int $timeout 阻塞等待时间，秒，0永久阻塞
     * @return mixed | false 成功返回移动的元素，失败则返回false
     */
    public function brpoplpush($key1, $key2, $timeout=1)
    {
    	return $this->_redis->brpoplpush($key1, $key2, $timeout);
    }

    /**
     * 集合操作 - 添加元素, 如果元素已经存在返回false
     * @param string $key
     * @param string $value		- 允许批量写入，传入数组即可，批量写入，只要有一个元素插入失败则返回false
     * @param int $expire			- 超时时间, 0不限制
     * @return int | false 成功返回集合元素个数，失败返回false
     */
    public function sAdd($key, $value, $expire = 0)
    {
        $ret = false;
        if (!empty($value) && is_array($value)) {
            $params = [$key];
            $params = array_merge($params, $value);
    		$ret = call_user_func_array(array($this->_redis, 'sAdd'), $params);
    	} else {
    		$ret = $this->_redis->sAdd($key, $value);
    	}

    	if ($ret && $expire > 0) {
    		$this->_redis->setTimeout($key, $expire);
    	}
    	return $ret ? true : false;
    }

    /**
     * 集合操作 - 返回所有集合元素
     * @param string $key
     * @return array
     */
    public function sMembers($key)
    {
    	return $this->_redis->sMembers($key);
    }

    /**
     * 集合操作 - 检测集合是否包含指定元素
     * @param string $key
     * @param mixed $value
     * @return bool	存在返回true
     */
    public function sIsMember($key, $value)
    {
    	return $this->_redis->sIsMember($key, $value);
    }

    /**
     *  集合操作 - 返回集合元素个数, key不存在返回0
     * @param string $key
     * @return int
     */
    public function sSize($key)
    {
    	return $this->_redis->sSize($key);
    }

    /**
     * 集合操作 - 将一个元素从一个集合移动到另外一个集合
     * @param string $srcKey		- 源集合
     * @param string $dstKey		- 目标集合
     * @param mixed $member		- 待移动元素
     * @return bool 成功返回true
     */
    public function sMove($srcKey, $dstKey, $member)
    {
    	return $this->_redis->sMove($srcKey, $dstKey, $member);
    }

    /**
     * 集合操作 - 随机弹出一个集合元素
     * @param string $key
     * @return mixed | false		- 成功返回集合元素，失败返回false
     */
    public function sPop($key)
    {
    	return $this->_redis->sPop($key);
    }

    /**
     * 集合操作 - 随机获取一个元素，不删除该元素
     * @param string $key
     * @param int $count		- 随机返回多少个元素
     * @return mixed |false		- 如果$count=1则返回一个元素，$count > 1则返回一个元素数组
     */
    public function sRandMember($key, $count = 1)
    {
    	$ret = false;
    	if ($count > 1) {
    		$ret = $this->_redis->sRandMember($key, $count);
    		//bug处理，srandmember没有反序列化数据
    		if (!empty($ret)) {
    			foreach ($ret as $k => $v) {
    				$ret[$k] = unserialize($v);
    			}
    		}
    	}
    	else {
    		$ret = unserialize($this->_redis->sRandMember($key));
    	}
    	return $ret;
    }

    /**
     * 集合操作 - 计算key1的差集, 允许传递N个参数，最少传递2个参数
     * @param string $key1
     * @param string $key2
     * @return boolean|array 成功返回array, 失败返回false
     */
    public function sDiff($key1, $key2)
    {
    	if (func_num_args() < 2) {
    		return false;
    	}
    	return call_user_func_array([$this->_redis, "sDiff"], func_get_args());
    }

    /**
     * 集合操作 - 计算key1的差集, 允许传递N个参数，最少传递3个参数, 结果保存在$dstKey
     * @param string $dstKey   - 结果保存在该key
     * @param string $key1
     * @param string $key2
     * @return boolean|int 成功返回$dstKey集合元素个数, 失败返回false
     */
    public function sDiffStore($dstKey, $key1, $key2)
    {
    	if (func_num_args() < 3) {
    		return false;
    	}
    	return call_user_func_array([$this->_redis, "sDiffStore"], func_get_args());
    }

    /**
     * 集合操作 - 计算交集, 允许传递N个参数，最少传递2个参数，只要参与交集运算其中的一个集合key不存在，则返回false
     * @param string $key1
     * @param string $key2
     * @return boolean|array 成功返回array, 失败返回false
     */
    public function sInter($key1, $key2)
    {
    	if (func_num_args() < 2) {
    		return false;
    	}
    	return call_user_func_array([$this->_redis, "sInter"], func_get_args());
    }

    /**
     * 集合操作 - 计算交集, 允许传递N个参数，最少传递3个参数，只要参与交集运算其中的一个集合key不存在，则返回false
     * @param string $dstKey   - 结果保存在该key
     * @param string $key1
     * @param string $key2
     * @return boolean|int 成功返回$dstKey集合元素个数, 失败返回false
     */
    public function sInterStore($dstKey, $key1, $key2)
    {
    	if (func_num_args() < 3) {
    		return false;
    	}
    	return call_user_func_array([$this->_redis, "sInterStore"], func_get_args());
    }

    /**
     * 集合操作 - 删除集合元素
     * @param string $key
     * @param mixed $member		- 集合元素， 支持批量删除，传递数组即可
     * @return int 返回删除元素个数
     */
    public function sRem($key, $member)
    {
    	if (!empty($member) && is_array($member)) {
    		$params = [$key];
    		$params = array_merge($params, $member);
			return call_user_func_array([$this->_redis, 'sRem'], $params);
    	}
		return $this->_redis->sRem($key, $member);
    }

    /**
     * 集合操作 - 计算并集, 允许传递N个参数，最少传递2个参数
     * @param string $key1
     * @param string $key2
     * @return boolean|array 成功返回array, 失败返回false
     */
    public function sUnion($key1, $key2)
    {
    	if (func_num_args() < 2) {
    		return false;
    	}
    	return call_user_func_array([$this->_redis, "sUnion"], func_get_args());
    }

    /**
     * 集合操作 - 计算并集, 允许传递N个参数，最少传递3个参数
     * @param string $dstKey   - 结果保存在该key
     * @param string $key1
     * @param string $key2
     * @return boolean|int 成功返回$dstKey集合元素个数, 失败返回false
     */
    public function sUnionStore($dstkey, $key1, $key2)
    {
    	if (func_num_args() < 2) {
    		return false;
    	}
    	return call_user_func_array([$this->_redis, "sUnionStore"], func_get_args());
    }

    /**
     * 有序集合操作	- 添加元素
     * @param string $key
     * @param mixed $value
     * @param double $score		- 元素权值
     * @param int $expire			- 超时时间, 0不限制
     * @return bool 成功返回true
     */
    public function zAdd($key, $value, $score, $expire = 0)
    {
    	$ret = $this->_redis->zAdd($key, $score, $value);
    	if ($ret && $expire > 0) {
    		$this->setTimeout($key, $expire);
    	}
    	return $ret ? true : false;
    }

/**
     * 有序集合操作	- 批量添加元素
     * @param string $key
     * @param array $value		-	 集合元素数组，格式: ["元素1" => "权值", "元素2" => "权值"]
     * @param int $expire			- 超时时间, 0不限制
     * @return bool 成功返回true
     */
    public function zAdds($key, array $values, $expire = 0)
    {
    	if (empty($values) || !is_array($values)) {
    		return false;
    	}
    	$params = [$key];
    	foreach ($values as $k => $score) {
    		$params[] = $score;
    		$params[] = $k;
    	}
    	$ret = call_user_func_array([$this->_redis, 'zAdd'], $params);
    	if ($ret && $expire > 0) {
    		$this->setTimeout($key, $expire);
    	}
     	return $ret ? true : false;
    }

    /**
     * 有序集合操作	- 返回集合元素个数
     * @param string $key
     * @return int
     */
    public function zSize($key)
    {
    	return $this->_redis->zSize($key);
    }

    /**
     * 有序集合操作 - 统计权值在[$start, $end]之间的元素个数
     * @param string $key
     * @param string $start			- 权值最小值,  支持无穷小， Redis::MIN_VALUE
     * @param string $end			- 权值最大值, 支持无穷大, Redis::MAX_VALUE
     * @return int
     */
    public function zCount($key, $start, $end)
    {
    	return $this->_redis->zCount($key, $start, $end);
    }

    /**
     * 有序集合操作 - 指定元素的增加权值
     * @param string $key
     * @param mixed $member		- 集合元素
     * @param double $score		- 需要增加的权值
     * @param int $expire			- 超时时间, 0不限制
     * @return int  成功返回新的权值
     */
    public function zIncrBy($key, $member, $score, $expire = 0)
    {
    	$ret = $this->_redis->zIncrBy($key, $score, $member);
    	if (false !== $ret && $expire > 0) {
    		$this->setTimeout($key, $expire);
    	}
    	return $ret;
    }

    /**
     * 有序集合操作 - 返回[start, end]索引区间的元素, 升序排序
     * @param string $key
     * @param int $start		- 开始索引
     * @param int $end		- 结束索引, -1代表最后一个
     * @return array | false 成功返回集合元素数组,格式: ["元素1" => 权值, "元素2" => 权值 ...]
     */
    public function zRange($key, $start, $end)
    {
    	return $this->_redis->zRange($key, $start, $end, true);
    }

    /**
     * 有序集合操作 - 返回[start, end]索引区间的元素， 反序排序
     * @param string $key
     * @param int $start		- 开始索引
     * @param int $end		- 结束索引, -1代表最后一个
     * @return array | false 成功返回集合元素数组,格式: ["元素1" => 权值, "元素2" => 权值 ...]
     */
    public function zRevRange($key, $start, $end)
    {
    	return $this->_redis->zRevRange($key, $start, $end, true);
    }



    /**
     * 有序集合操作 - 交集运算，合并相同元素权值时采用如下策略：
     * 1. $weights为空， 则权值相加，$aggregateType设置无效
     * 2. $weights不为空，且$weights数组大小必须与$keys数组大小一致，否则失败。
     * 	这种情况下，首先$keys数组中的每一个集合的元素权值分别乘于$weights对应的权值，然后根据$aggregateType确定相加，取最大值或则最小值.
     *		例子:
     *			//创建有序集合，zuser_list1
     *			Redis::getInstance()->zAdds("zuser_list1", [
     *																					//元素 => 权值
	 *																					1 => 10,
	 *																					2 => 5,
	 *																					3 => 13,
	 *																					6 => 1,
	 *																					9 => 2,
	 *																					10 => 1,
	 *																				]);
	 *			//创建有序集合，zuser_list2
	 *			Redis::getInstance()->zAdds("zuser_list2", [
	 *																					1 => 10,
	 *																					2 => 5,
	 *																					3 => 13,
	 *																					60 => 1,
	 *																			]);
	 *			//	集合zuser_list1和zuser_list2做交集运算, 结果保存在zuser_dst中
	 *			Redis::getInstance()->zInter("zuser_dst", array("zuser_list1", "zuser_list2"), [1,10], Redis::AGGREGATE_TYPE_SUM);
	 *			//结果为：[
	 *			 					//元素 => 权值
	 *			  					2 => 55,
	 *			  					1 => 110,
	 *			  					3 => 143,
	 *			  				   ]
	 *			 //计算过程：
	 *			 首先 zuser_list1和zuser_list2直接做交集运算，遇到相同的元素按下面规则处理
	 *			 因为$weights权值数组为: [1, 10], 因此zuser_list1集合每个元素的权值乘于 1,  zuser_list2集合每个元素的权值乘于 10
	 *			 因为$aggregateType = Redis::AGGREGATE_TYPE_SUM， 所以相同元素权值相加。
	 *
     * @param string $dstKey								- 结果保存在该key
     * @param array $keys									- 需要做交集运算的集合key数组
     * @param array $weights							- 权值数组，必须与keys数组的大小一致，要么就为空
     * @param string $aggregateType				- 相同元素的权值聚合类型，Redis::AGGREGATE_TYPE_SUM 求和
     * 																											  Redis::AGGREGATE_TYPE_MAX 取最大值
     * 																										      Redis::AGGREGATE_TYPE_MIN  取最小值
     * @return int 成功返回$dstKey结果元素个数
     */
    public function zInter($dstKey, array $keys, array $weights = [], $aggregateType = self::AGGREGATE_TYPE_SUM)
    {
    	if (empty($weights)) {
    		return $this->_redis->zInter($dstKey, $keys);
    	}
    	return $this->_redis->zInter($dstKey, $keys, $weights, $aggregateType);
    }

    /**
     * 有序集合操作 - 返回权值区间 [start,end]的集合元素, 权值按小到大排序
     * @param string $key
     * @param string $start			- 权值最小值,  支持无穷小， Redis::MIN_VALUE
     * @param string $end			- 权值最大值, 支持无穷大, Redis::MAX_VALUE
     * @param int $offset				- 开始偏移
     * @param int $count				- 分页大小
     * @return array | false   格式：[
     * 															"元素1" => 权值,
     * 															"元素2" => 权值,
     * 																....
     * 													 	]
     */
    public function zRangeByScore($key, $start, $end, $offset = 0, $count = 0)
    {
    	if ($offset >= 0 && $count > 0) {
    		return $this->_redis->zRangeByScore($key, $start, $end, ['withscores' => true, 'limit' => [$offset, $count]]);
    	}
    	return $this->_redis->zRangeByScore($key, $start, $end, ['withscores' => true]);
    }

    /**
     * 有序集合操作 - 返回权值区间 [start,end]的集合元素, 权值按大到小排序
     * @param string $key
     * @param string $start			- 权值最小值,  支持无穷小， Redis::MIN_VALUE
     * @param string $end			- 权值最大值, 支持无穷大, Redis::MAX_VALUE
     * @param int $offset				- 开始偏移
     * @param int $count				- 分页大小
     * @return array | false   格式：[
     * 															"元素1" => 权值,
     * 															"元素2" => 权值,
     * 																....
     * 													 	]
     */
    public function zRevRangeByScore($key, $start, $end, $offset = 0, $count = 0)
    {
    	if ($offset >= 0 && $count > 0) {
    		return $this->_redis->zRevRangeByScore($key, $end, $start, ['withscores' => true, 'limit' => [$offset, $count]]);
    	}
    	return $this->_redis->zRevRangeByScore($key, $end, $start, ['withscores' => true]);
    }

    /**
     * 有序集合操作 - 假定集合元素权值相等的情况下，集合元素按字典顺序查找
     * 例子：
     * 		//查找小于等于y的集合元素
     * 		Redis::getInstance()->zRangeByLex("key1", "-", "[y")
     * 		//查找大于c的元素
     * 		Redis::getInstance()->zRangeByLex("key1", "(c", "+")
     *
     * @param string $key
     * @param string $min			- 字母最小值，- 表示负无穷，( 表示大于，[ 表示大于等于
     * @param string $max			- 字母最大值， + 表示正无穷，( 表示大于，[ 表示大于等于
     * @param int $offset				- 元素开始偏移
     * @param int $count				- 分页大小
     * @return
     */
    public function zRangeByLex($key, $min, $max, $offset = 0, $count = 0)
    {
    	if ($offset >= 0 && $count > 0) {
    		return $this->_redis->zRangeByLex($key, $min, $max, $offset, $count);
    	}
    	return $this->_redis->zRangeByLex($key, $min, $max);
    }

    /**
     * 有序集合操作 - 查找正序排名，从0开始计算名次
     * @param string $key
     * @param mixed $member		- 集合元素
     * @return int | false 成功返回名次
     */
    public function zRank($key, $member)
    {
    	return $this->_redis->zRank($key,$member);
    }

    /**
     * 有序集合操作 - 查找反序排名，从0开始计算名次
     * @param string $key
     * @param mixed $member		- 集合元素
     * @return int | false 成功返回名次
     */
    public function zRevRank($key, $member)
    {
    	return $this->_redis->zRevRank($key,$member);
    }

    /**
     * 有序集合操作 - 删除集合元素
     * @param string $key
     * @param mixed $member		- 集合元素， 支持批量删除，传递数组即可
     * @return int 返回删除元素个数
     */
    public function zRem($key, $member)
    {
    	if (!empty($member) && is_array($member)) {
    		$params = [$key];
    		$params = array_merge($params, $member);
    		return call_user_func_array([$this->_redis, 'zRem'], $params);
    	}
    	return $this->_redis->zRem($key, $member);
    }

    /**
     * 有序集合操作 - 删除这个名次区间[start,end]的元素， 按升序排序
     * @param string $key
     * @param int $start
     * @param int $end
     * @return int | false 成功返回删除元素个数
     */
    public function zRemRangeByRank($key, $start, $end)
    {
    	return $this->_redis->zRemRangeByRank($key, $start, $end);
    }

    /**
     * 有序集合操作 - 删除这个权值区间[start,end]的元素
     * @param string $key
     * @param string $start			- 权值最小值,  支持无穷小， Redis::MIN_VALUE
     * @param string $end			- 权值最大值, 支持无穷大, Redis::MAX_VALUE
     * @return int | false 成功返回删除元素个数
     */
    public function zRemRangeByScore($key, $start, $end)
    {
    	return $this->_redis->zRemRangeByScore($key, $start, $end);
    }

    /**
     * 有序集合操作 - 获取指定元素的权值
     * @param string $key
     * @param string $member			- 元素
     * @return double | false 成功返回权值，失败返回false
     */
    public function zScore($key, $member)
    {
    	return $this->_redis->zScore($key, $member);
    }

    /**
     * 有序集合操作 - 并集运算，遇到相同元素合并权值策略，请参考zInter交集权值合并策略。
     * @param string $dstKey								- 结果保存在该key
     * @param array $keys									- 需要做并集运算的集合key数组
     * @param array $weights							- 权值数组，必须与keys数组的大小一致，要么就为空
     * @param string $aggregateType				- 相同元素的权值聚合类型，Redis::AGGREGATE_TYPE_SUM 求和
     * 																											  Redis::AGGREGATE_TYPE_MAX 取最大值
     * 																										      Redis::AGGREGATE_TYPE_MIN  取最小值
     * @return int 成功返回$dstKey结果元素个数
     */
    public function zUnion($dstKey, array $keys, array $weights = [], $aggregateType = self::AGGREGATE_TYPE_SUM)
    {
    	if (empty($weights)) {
    		return $this->_redis->zUnion($dstKey, $keys);
    	}
    	return $this->_redis->zUnion($dstKey, $keys, $weights, $aggregateType);
    }

    /**
     * 事务开始
     * @return \Redis
     */
    public function multi()
    {
        return $this->_redis->multi();
    }

    /**
     * 事务执行
     * @return \Redis
     */
    public function exec()
    {
        return $this->_redis->exec();
    }

    /**
     * 事务取消
     * @return \Redis
     */
    public function discard()
    {
        return $this->_redis->discard();
    }

    /**
     * 监听键值(用于事务辅助)
     * @param string $key
     * @author fukaiyao 2019-6-5 09:29:35
     * @return \Redis
     */
    public function watch($key)
    {
        return $this->_redis->watch($key);
    }

    /**
     * 取消监听(用于事务辅助)
     * @author fukaiyao 2019-6-5 09:29:35
     * @return \Redis
     */
    public function unwatch()
    {
        return $this->_redis->unwatch();
    }

    /**
     * 将数据同步保存到磁盘
     * @param bool $isBg -后台执行
     * @return bool
     */
    public function save($isBg = false)
    {
        if ($isBg) {
            $this->_redis->bgsave();
        } else {
            $this->_redis->save();
        }

        return true;
    }

    public function close()
    {
        $this->_redis->close();
    }

    //在对象所有引用被注销时，关闭连接，否则长连接不会被注销而继续复用
    public function __destruct()
    {
        $this->close();
    }

//    public function __call($method, $args = array()) {
//        return call_user_func_array(array($this->_redis, $method), $args);
//    }

}