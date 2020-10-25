<?php
namespace SgIoc\Cache;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9
 * Time: 10:16
 */
interface StoreInterface
{
    /**
     * 判断键是否存在
     * @param $key
     * @return mixed
     */
    public function has($key);

    /**
     * add 方法只会在缓存项不存在的情况下添加数据到缓存，如果数据被成功添加到缓存返回 true，否则，返回false：
     * @param $key
     * @param $value
     * @param $minutes
     * @return mixed
     */
    public function add($key, $value, $minutes);

    /**
     * 设置缓存,存在则覆盖,不存在则创建,成功返回true
     * @param $key
     * @param $value
     * @param $minutes
     * @return mixed
     */
    public function put($key, $value, $minutes);

    /**
     * 获取缓存
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * 获取缓存&删除
     * @param $key
     * @return mixed
     */
    public function pull($key);

    /**
     * 永久缓存
     * @param $key
     * @param $value
     * @return mixed
     */
    public function forever($key, $value);

    /**
     * 清理所有缓存
     * @return mixed
     */
    public function flush();

    /**
     * 删除缓存
     * @param $key
     * @return mixed
     */
    public function forget($key);

    /**
     * 递增
     * @param $key
     * @param int $value
     * @return mixed
     */
    public function increment($key, $value = 1);

    /**
     * 递减
     * @param $key
     * @param int $value
     * @return mixed
     */
    public function decrement($key, $value = 1);

    /**
     * 如果缓存项不存在，传递给 remember 方法的闭包被执行并且将结果存放到缓存中
     * @param $key
     * @param $minutes
     * @param mixed $callback
     * @return mixed
     */
    public function remember($key, $minutes, $callback);

    /**
     * 永久缓存,如果缓存项不存在，传递给 remember 方法的闭包被执行并且将结果存放到缓存中
     * @param $key
     * @param mixed $callback
     * @return mixed
     */
    public function rememberForever($key, $callback);

    /**
     * 获取带前缀的键
     * @param $key
     * @return mixed
     */
    public function getKey($key);

    /**
     * 获取详情
     * @return mixed
     */
    public function info();

    /**
     * 关闭连接
     * @return mixed
     */
    public function close();
}