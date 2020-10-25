<?php
namespace workerbase\classs;

use workerbase\classs\datalevels\Redis;

/**
 *  Redis锁操作类,防止并发访问
 * @author fukaiyao
 */
class RedisLock
{
    private static $_instance = null;

    //每次取锁最大延迟毫秒数，最小延迟为此值的一半
    private $_retryDelay = 48;

    //有效时间偏差因子(返回的锁有效期默认不能低于设置时间的百分之一)
    private $_clockDriftFactor = 0.01;

    private $_redis = null;

    //key前缀
    private $_lockPrefix = "wk_common_base_lock:";

    /**
     * RedLock constructor.
     */
    function __construct()
    {
        $this->_redis = Redis::getInstance();
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new RedisLock();
        }
        return self::$_instance;
    }

    /**
     * @param string $key 锁名
     * @param int $wait -锁等待时间,秒
     * @param int $ttl 有效时间(秒)
     * @param bool $isChoke 是否阻塞取锁，false则直接返回取锁结果
     * @return array|bool
     */
    public function lock($key, $wait = 1, $ttl = 5, $isChoke = true)
    {
        $token = uniqid();

        $hasWait = 0;//已经等待的时间:毫秒
        do {
            $startTime = microtime(true) * 1000;

            //通过setNx获取锁,只有一个能拿到
            $result = $this->_redis->set($this->_getKeyName($key), $token, ['NX', 'PX' => $ttl * 1000]);
            if (!$result) { //没拿到锁
                if (!$isChoke) {
                    return false;
                }

                if ($hasWait > $wait * 1000)//等待超时
                {
                    return false;
                }
            }

            # 对生存时间的偏差中增加2毫秒来计算Redis的到期时间
            #防止有效期过低的返回
            $drift = ($ttl * 1000 * $this->_clockDriftFactor) + 2;

            $validityTime = ($ttl * 1000) - (microtime(true) * 1000 - $startTime) - $drift;

            $lockToken = [
                'validity' => $validityTime,
                'key' => $key,
                'token' => $token,
            ];

            if ($result && $validityTime > 0) {
                return $lockToken;
            } else {
                if ($validityTime <= 0) {
                    $this->unlock($lockToken);
                }
                if (!$isChoke) {
                    return false;
                }
            }

            // 在重试之前等待一个随机延迟，为的是分散取锁的时间，避免集中抢锁
            $delay = mt_rand(floor($this->_retryDelay / 2), $this->_retryDelay);
            usleep($delay * 1000);//毫秒
            $hasWait += $delay;
        } while (true);
    }

    public function unlock(array $lock)
    {
        $key = $lock['key'];
        $token = $lock['token']; //用请求的唯一token来解锁，避免锁过期，导致的交叉请求相互解锁的问题
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        ';
        return $this->_redis->getOriginInstance()->eval($script, [$this->_getKeyName($key), $token], 1);

    }

    private function _getKeyName($key)
    {
        return $this->_lockPrefix . $key;
    }
}
