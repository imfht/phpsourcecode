<?php
/**
 * 存储接口
 */
interface IStore{
	
	/**
	 * 设置一个缓存值，若键值已经存在，新值会覆盖旧值
	 * @param $value Mixed
	 * @param $ttl Int, 缓存存在时间，0表示不会自动删除
	 * return Boolen, TRUE/FALSE
	 */
	public function set($value, $ttl = 0);
	
	/**
	 * 设置一个缓存值，若键值已经存在，则直接返回 true
	 * @see set
	 */
	public function setnx($value, $ttl = 0);
	
	/**
	 * 获取缓存
	 * return Mixed, 获取成功返回相应值，失败返回null
	 */
	public function get();
	
	/**
	 * 删除缓存
	 * return Boolen, 删除成功返回true，失败false
	 */
	public function delete();
	
	/**
	 * 检测缓存是否存在
	 */
	public function has();
}