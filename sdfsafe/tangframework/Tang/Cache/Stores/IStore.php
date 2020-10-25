<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Cache\Stores;
/**
 * 缓存存储接口
 * 开发者需要开发自己的缓存对象时，必须要实现该接口
 * Interface IStore
 * @package Tang\Cache\Stores
 */
interface IStore
{
    /**
     * 根据$key获取缓存内容
     * 如果没有缓存则调用$callback进行设置缓存
     * $callback会接受IStore 和$key两个参数
     * 需要$callback返回缓存值
     * <code>
     * //采用默认的缓存设置
     * CacheService::getService()->get('name',function($instance,$key)
     * {
     *  $content = 'cache';
     *  $instance->set($key,$content,86400);//缓存内容一天
     *  return $content;
     * });
     * </code>
     * @param $key
     * @param callable $callback
     * @return mixed
     */
    public function get($key,callable $callback = null);

    /**
     * 设置缓存值
     * <code>
     * CacheService::getService()->set('name','cache',86400);//缓存name一天
     * </code>
     * @param $key
     * @param $value
     * @param int $expire
     * @return mixed
     */
    public function set($key,$value,$expire=0);

    /**
     * 删除$key缓存
     * <code>
     * CacheService::getService()->delete('name');//删除name缓存
     * </code>
     * @param $key
     * @return mixed
     */
    public function delete($key);

    /**
     * 清空缓存
     * <code>
     * CacheService::getService()->clean(');
     * </code>
     * @return mixed
     */
    public function clean();
}