<?php
/**
 * 缓存类统一接口，所有的缓存类必须实现这一接口。
 * cache operation class common interface.
 * 缓存的分类：对于文件缓存，其格式是这样的
 * 1. 有baseKey：baseKey/fname/factor/filename
 * 如：article/list/100/article-list-100.html
 * article/detail/90/article-detail-190.html
 * 2. 有key : common/{hash($key)}/{$key}.html
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\cache\interfaces;

interface ICache {

	/**
	 * 初始化配置信息
	 * @return mixed
	 */
	public function initConfigs();
	
	/**
	 * 获取缓存内容
	 * 
	 * @param string $key 缓存的key值,如果设置为null则自动生成key
     * @return mixed
	 */
	public function get( $key );
	
	/**
	 * 添加|更新缓存
	 * @param string $key 缓存的key值, 如果设置为null则自动生成key
     * @param string $content 缓存内容
     * @param string $expire  缓存有效期,如果等于0表示永不过期
     * @param boolean
	 */
	public function set( $key, $content, $expire=0 );
	
	/**
	 * 删除缓存 
	 * @param string $key 缓存的key值。
     * @return boolean
	 */
	public function delete( $key );
		
}
