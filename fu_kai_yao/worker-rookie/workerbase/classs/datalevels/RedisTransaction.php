<?php
namespace workerbase\classs\datalevels;

use workerbase\classs\AttachEvent;

/**
 * redis事务
 * @author fukaiyao
 */

class RedisTransaction {

    private static $_instance = null;

	/** @var Redis */
	protected $redis;

	/** @var Redis|null */
	protected $redisTransactionInstance = null;
	protected $redisTransactionLevel = 0;

	protected $isMultiActive;

	/**
	 * [__construct description]
	 */
	public function __construct() {

		if (empty($server)) {
			throw new \Exception('redis config failed');
		}

		$this->redis = Redis::getInstance();
        AttachEvent::attachEventHandler('onEndRequest', [$this, 'catchException']);
    }

    public static function getInstance()
    {
        if(self::$_instance == null)
        {
            self::$_instance = new RedisTransaction();
        }
        return self::$_instance;
    }

	public function begin() {
		if(!$this->redisTransactionInstance) {
			$this->redisTransactionInstance = $this->redis;
		}
		if(!$this->redisTransactionLevel) {
			$this->redis = $this->redisTransactionInstance->multi();
		}
		$this->redisTransactionLevel++;
		return $this;
	}

	public function commit() {
		if(!$this->redisTransactionLevel) {
			throw new \Exception('There is no active Redis transaction');
		}
		$this->redisTransactionLevel--;
		$result = null;
		if(!$this->redisTransactionLevel) {
			$result = $this->redis->exec();
			$this->redis = $this->redisTransactionInstance;
            $this->redisTransactionInstance = null;
		}

		return $result;
	}

	public function rollback() {
		if(!$this->redisTransactionLevel) {
			throw new \Exception('There is no active Redis transaction');
		}
		$this->redis->discard();
		$this->redisTransactionLevel = 0;
		$this->redis = $this->redisTransactionInstance;
        $this->redisTransactionInstance = null;
    }

    /**
     * 监听键值(用于事务辅助，必须在begin()方法执行之前执行)
     * @param string $key
     * @author fukaiyao 2019-6-5 09:29:35
     * @return \Redis
     */
    public function watch($key)
    {
        return $this->redis->watch($key);
    }

    /**
     * 取消监听(用于事务辅助，必须在commit()返回false之后执行)
     * @author fukaiyao 2019-6-5 09:29:35
     * @return \Redis
     */
    public function unwatch()
    {
        return $this->redis->unwatch();
    }

	protected static function replaceFalseToNull($data) {
		return $data === false ? null : $data;
	}

	public function __call($method, $args = array()) {
        $result = call_user_func_array(array($this->redis, $method), $args);
        return $result;
	}

	public function __destruct() {
		if($this->redisTransactionLevel) {
			$this->rollback();
		}
	}

    /**
     * 处理异常
     * @throws \Exception
     */
    public function catchException()
    {
        if ($this->redisTransactionLevel > 0) {
            $this->redisTransactionInstance != null && $this->rollback();
            throw new \Exception("loss commit/rollback transaction.");
        }
    }
}


