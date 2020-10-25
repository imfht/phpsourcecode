<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 16/9/22
 * Time: 上午12:33
 */

namespace inhere\redis;

/**
 * Class SupportedCommandsTrait
 * @package inhere\redis
 */
trait SupportedCommandsTrait
{

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '3.2';
    }

    /**
     * @link https://github.com/nrk/predis
     * @from predis/predis/src/Profile/RedisVersion320.php
     */
    public function getSupportedCommands()
    {
        // command => status(command enable status) // True enabled, False disabled
        return [
            /* ---------------- Redis 1.2 ---------------- */

            /* commands operating on the key space */
            'EXISTS' => true,
            'DEL' => true,
            'TYPE' => true,
            'KEYS' => true,
            'RANDOMKEY' => true,
            'RENAME' => true,
            'RENAMENX' => true,
            'EXPIRE' => true,
            'EXPIREAT' => true,
            'TTL' => true,
            'MOVE' => true,
            'SORT' => true,
            'DUMP' => true,
            'RESTORE' => true,

            /* commands operating on string values */
            'SET' => true,
            'SETNX' => true,
            'MSET' => true,
            'MSETNX' => true,
            'GET' => true,
            'MGET' => true,
            'GETSET' => true,
            'INCR' => true,
            'INCRBY' => true,
            'DECR' => true,
            'DECRBY' => true,

            /* commands operating on lists */
            'RPUSH' => true,
            'LPUSH' => true,
            'LLEN' => true,
            'LRANGE' => true,
            'LTRIM' => true,
            'LINDEX' => true,
            'LSET' => true,
            'LREM' => true,
            'LPOP' => true,
            'RPOP' => true,
            'RPOPLPUSH' => true,

            /* commands operating on sets */
            'SADD' => true,
            'SREM' => true,
            'SPOP' => true,
            'SMOVE' => true,
            'SCARD' => true,
            'SISMEMBER' => true,
            'SINTER' => true,
            'SINTERSTORE' => true,
            'SUNION' => true,
            'SUNIONSTORE' => true,
            'SDIFF' => true,
            'SDIFFSTORE' => true,
            'SMEMBERS' => true,
            'SRANDMEMBER' => true,

            /* commands operating on sorted sets */
            'ZADD' => true,
            'ZINCRBY' => true,
            'ZREM' => true,
            'ZRANGE' => true,
            'ZREVRANGE' => true,
            'ZRANGEBYSCORE' => true,
            'ZCARD' => true,
            'ZSCORE' => true,
            'ZREMRANGEBYSCORE' => true,

            /* connection related commands */
            'PING' => true,
            'AUTH' => true,
            'SELECT' => true,
            'ECHO' => true,
            'QUIT' => true,

            /* remote server control commands */
            'INFO' => true,
            'SLAVEOF' => true,
            'MONITOR' => true,
            'DBSIZE' => true,
            'FLUSHDB' => true,
            'FLUSHALL' => true,
            'SAVE' => true,
            'BGSAVE' => true,
            'LASTSAVE' => true,
            'SHUTDOWN' => true,
            'BGREWRITEAOF' => true,

            /* ---------------- Redis 2.0 ---------------- */

            /* commands operating on string values */
            'SETEX' => true,
            'APPEND' => true,
            'SUBSTR' => true,

            /* commands operating on lists */
            'BLPOP' => true,
            'BRPOP' => true,

            /* commands operating on sorted sets */
            'ZUNIONSTORE' => true,
            'ZINTERSTORE' => true,
            'ZCOUNT' => true,
            'ZRANK' => true,
            'ZREVRANK' => true,
            'ZREMRANGEBYRANK' => true,

            /* commands operating on hashes */
            'HSET' => true,
            'HSETNX' => true,
            'HMSET' => true,
            'HINCRBY' => true,
            'HGET' => true,
            'HMGET' => true,
            'HDEL' => true,
            'HEXISTS' => true,
            'HLEN' => true,
            'HKEYS' => true,
            'HVALS' => true,
            'HGETALL' => true,

            /* transactions */
            'MULTI' => true,
            'EXEC' => true,
            'DISCARD' => true,

            /* publish - subscribe */
            'SUBSCRIBE' => true,
            'UNSUBSCRIBE' => true,
            'PSUBSCRIBE' => true,
            'PUNSUBSCRIBE' => true,
            'PUBLISH' => true,

            /* remote server control commands */
            'CONFIG' => true,

            /* ---------------- Redis 2.2 ---------------- */

            /* commands operating on the key space */
            'PERSIST' => true,

            /* commands operating on string values */
            'STRLEN' => true,
            'SETRANGE' => true,
            'GETRANGE' => true,
            'SETBIT' => true,
            'GETBIT' => true,

            /* commands operating on lists */
            'RPUSHX' => true,
            'LPUSHX' => true,
            'LINSERT' => true,
            'BRPOPLPUSH' => true,

            /* commands operating on sorted sets */
            'ZREVRANGEBYSCORE' => true,

            /* transactions */
            'WATCH' => true,
            'UNWATCH' => true,

            /* remote server control commands */
            'OBJECT' => true,
            'SLOWLOG' => true,

            /* ---------------- Redis 2.4 ---------------- */

            /* remote server control commands */
            'CLIENT' => true,

            /* ---------------- Redis 2.6 ---------------- */

            /* commands operating on the key space */
            'PTTL' => true,
            'PEXPIRE' => true,
            'PEXPIREAT' => true,
            'MIGRATE' => true,

            /* commands operating on string values */
            'PSETEX' => true,
            'INCRBYFLOAT' => true,
            'BITOP' => true,
            'BITCOUNT' => true,

            /* commands operating on hashes */
            'HINCRBYFLOAT' => true,

            /* scripting */
            'EVAL' => true,
            'EVALSHA' => true,
            'SCRIPT' => true,

            /* remote server control commands */
            'TIME' => true,
            'SENTINEL' => true,

            /* ---------------- Redis 2.8 ---------------- */

            /* commands operating on the key space */
            'SCAN' => true,

            /* commands operating on string values */
            'BITPOS' => true,

            /* commands operating on sets */
            'SSCAN' => true,

            /* commands operating on sorted sets */
            'ZSCAN' => true,
            'ZLEXCOUNT' => true,
            'ZRANGEBYLEX' => true,
            'ZREMRANGEBYLEX' => true,
            'ZREVRANGEBYLEX' => true,

            /* commands operating on hashes */
            'HSCAN' => true,

            /* publish - subscribe */
            'PUBSUB' => true,

            /* commands operating on HyperLogLog */
            'PFADD' => true,
            'PFCOUNT' => true,
            'PFMERGE' => true,

            /* remote server control commands */
            'COMMAND' => true,

            /* ---------------- Redis 3.2 ---------------- */

            /* commands operating on hashes */
            'HSTRLEN' => true,
            'BITFIELD' => true,

            /* commands performing geospatial operations */
            'GEOADD' => true,
            'GEOHASH' => true,
            'GEOPOS' => true,
            'GEODIST' => true,
            'GEORADIUS' => true,
            'GEORADIUSBYMEMBER' => true,
        ];
    }

}
