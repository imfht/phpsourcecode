<?php

namespace common\helpers;

/**
 * 缓存辅助类库
 *
 * @author ken <vb2005xu@qq.com>
 */
class Cache
{

	/**
	 * 用户缓存key，根据手机号码查找用户信息
	 */
	const KEY_USER_FINDBYMOBILENUMBER = 'user_findbymobilenumber_%s';

	/**
	 * 用户缓存key，根据用户名查找用户信息
	 */
	const KEY_USER_FINDBYUSERNAME = 'user_findbyusername_%s';

	/**
	 * 用户缓存key，根据auth_key查找用户信息
	 */
	const KEY_USER_FINDBYAUTHKEY = 'user_findbyauthkey_%s';

	/**
	 * 用户缓存key，根据uid查找用户信息
	 */
	const KEY_USER_FINDIDENTITY = 'user_findidentity_%s';

	/**
	 * 获取key
	 * @param type $key
	 * @param type $args
	 * @return type
	 */
	public static function getKey($key)
	{
		$args = func_get_args();
		unset($args[0]);
		$key = vsprintf($key, $args);
		return $key;
	}

	/**
	 * 获取缓存
	 * @param string $key
	 * @return string|array
	 */
	public static function get($key)
	{
		$ret = \Yii::$app->cache->get($key);
		return $ret;
	}

	/**
	 * 设置缓存
	 * @param type $key
	 * @param type $val
	 * @return type
	 */
	public static function set($key, $val)
	{
		$ret = \Yii::$app->cache->set($key, $val);
		return $ret;
	}

	/**
	 * 删除缓存
	 * @param type $key
	 * @return type
	 */
	public static function del($key)
	{
		$ret = \Yii::$app->cache->delete($key);
		return $ret;
	}

}

?>
