<?php
/**
 * 动态文件缓存, 可用于缓存数据库的查询结果, 或者是对页面的局部缓存, 实现ICache接口
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\cache;

use herosphp\cache\interfaces\ICache;
use herosphp\core\Loader;
use herosphp\files\FileUtils;
use herosphp\string\StringUtils;

class FileCache extends ACache implements ICache {


	//加载缓存配置
	public function initConfigs() {
		$this->configs = Loader::config('file', 'cache');
	}

    /**
     * @see ICache::get()
     * @param string $key
     * @param null $expire
     * @return bool|mixed|string
     */
	public function get( $key ) {

	    $cacheFile = $this->getCacheFile($key);

        //缓存文件不存在
		if ( !file_exists($cacheFile) ) return false;

        $text = file_get_contents($cacheFile);
        $content = StringUtils::jsonDecode($text);
		//判断缓存是否过期
		if ( $content['expire'] > 0 && time() > (filemtime($cacheFile) + $content['expire']) ) {
			return false;
		} else {
            return $content['data'];
		}
	}


    /**
     * @see   ICache::set();
     * @param string $key
     * @param string $content
     * @param null $expire
     * @return int
     */
	public function set( $key, $content, $expire=0 ) {

        $cacheFile = $this->getCacheFile($key);
        $dirname = dirname($cacheFile);
        if ( !file_exists($dirname) ) {
            FileUtils::makeFileDirs($dirname);
        }
        $data['expire'] = $expire;
        $data['data'] = $content;
		return file_put_contents($cacheFile, StringUtils::jsonEncode($data), LOCK_EX);
	}

    /**
     * @see        ICache::delete()
     * @param string $key
     * @return bool
     */
	public function delete( $key ) {
        $cacheFile = $this->getCacheFile($key);
		return @unlink($cacheFile);
	}

}
