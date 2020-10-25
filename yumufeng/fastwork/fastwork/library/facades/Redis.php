<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/2
 * Time: 10:27
 */

namespace fastwork\facades;


use fastwork\Facade;

/**
 * Class Redis
 * @see \fastwork\cache\Redis
 *
 * @method Redis instance(); static 获取一个连接池
 * @method Redis push(object $redis) static 往连接池放入一个连接
 * @method Redis pop() static 获取一个连接池，没有就创建一个
 * @method Redis setDefer(bool $bool=true) static 是否立刻返回结果
 *
 * //base
 * @method Redis expire(string $key, int $ttl) static
 * @method Redis keys(string $key); static
 *
 *
 * //string
 * @method Redis get(string $key) static
 * @method Redis set(string $key, string $value, int $timeout = 0) static
 * @method Redis setex(string $key, int $ttl, string $value) static
 * @method Redis psetex(string $key, int $expire, string $value) static
 * @method Redis setnx(string $key, string $value) static
 * @method Redis del(string ... $key) static
 * @method Redis delete(string ... $key) static
 * @method Redis getSet(string $key, string $value) static
 * @method Redis exists(string $key) static
 * @method Redis incr(string $key) static
 * @method Redis incrBy(string $key, int $increment) static
 * @method Redis incrByFloat(string $key, float $increment) static
 * @method Redis decr(string $key) static
 * @method Redis decrBy(string $key, int $increment) static
 * @method Redis mget(array ... $keys) static
 * @method Redis append(string $key, string $value) static
 * @method Redis getRange(string $key, int $start, int $end) static
 * @method Redis setRange(string $key, int $offset, string $value) static
 * @method Redis strlen(string $key) static
 * @method Redis getBit(string $key, int $offset) static
 * @method Redis setBit(string $key, int $offset, bool $bool) static
 * @method Redis mset(array $keyValue) static
 *
 * //list
 * @method Redis lPush(string $key, string $value) static
 * @method Redis rPush(string $key, string $value) static
 * @method Redis lPushx(string $key, string $value) static
 * @method Redis rPushx(string $key, string $value) static
 * @method Redis lPop(string $key) static
 * @method Redis rPop(string $key) static
 * @method Redis blpop(array $keys, int $timeout) static
 * @method Redis brpop(array $keys, int $timeout) static
 * @method Redis lSize(string $key) static
 * @method Redis lGet(string $key, int $index) static
 * @method Redis lSet(string $key, int $index, string $value) static
 * @method Redis IRange(string $key, int $start, int $end) static
 * @method Redis lTrim(string $key, int $start, int $end) static
 * @method Redis lRem(string $key, string $value, int $count) static
 * @method Redis rpoplpush(string $srcKey, string $dstKey) static
 * @method Redis brpoplpush(string $srcKey, string $detKey, int $timeout) static
 *
 * //set
 * @method Redis sAdd(string $key, string $value) static
 * @method Redis sRem(string $key, string $value) static
 * @method Redis sMove(string $srcKey, string $dstKey, string $value) static
 * @method Redis sIsMember(string $key, string $value) static
 * @method Redis sCard(string $key) static
 * @method Redis sPop(string $key) static
 * @method Redis sRandMember(string $key) static
 * @method Redis sInter(string ... $keys) static
 * @method Redis sInterStore(string $dstKey, string ... $srcKey) static
 * @method Redis sUnion(string ... $keys) static
 * @method Redis sUnionStore(string $dstKey, string ... $srcKey) static
 * @method Redis sDiff(string ... $keys) static
 * @method Redis sDiffStore(string $dstKey, string ... $srcKey) static
 * @method Redis sMembers(string $key) static
 *
 * //zset
 * @method Redis zAdd(string $key, double $score, string $value) static
 * @method Redis zRange(string $key, int $start, int $end) static
 * @method Redis zDelete(string $key, string $value) static
 * @method Redis zRevRange(string $key, int $start, int $end) static
 * @method Redis zRangeByScore(string $key, int $start, int $end, array $options = []) static
 * @method Redis zCount(string $key, int $start, int $end) static
 * @method Redis zRemRangeByScore(string $key, int $start, int $end) static
 * @method Redis zRemRangeByRank(string $key, int $start, int $end) static
 * @method Redis zSize(string $key) static
 * @method Redis zScore(string $key, string $value) static
 * @method Redis zRank(string $key, string $value) static
 * @method Redis zRevRank(string $key, string $value) static
 * @method Redis zIncrBy(string $key, double $score, string $value) static
 *
 * //Hash（哈希表）
 * @method Redis hSet(string $key, string $hashKey, string $value) static
 * @method Redis hSetNx(string $key, string $hashKey, string $value) static
 * @method Redis hGet(string $key, string $hashKey) static
 * @method Redis hLen(string $key) static
 * @method Redis hDel(string $key, string $hashKey) static
 * @method Redis hKeys(string $key) static
 * @method Redis hVals(string $key) static
 * @method Redis hGetAll(string $key) static
 * @method Redis hExists(string $key, string $hashKey) static
 * @method Redis hIncrBy(string $key, string $hashKey, int $value) static
 * @method Redis hIncrByFloat(string $key, string $hashKey, float $value) static
 * @method Redis hMset(string $key, array $keyValue) static
 * @method Redis hMGet(string $key, array $hashKeys) static
 *
 */

class Redis extends Facade
{

    protected static function getFacadeClass()
    {
        return 'redis';
    }
}