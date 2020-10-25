<?php
/**
 * 文件缓存抽象类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\cache;

use herosphp\cache\interfaces\ICache;
use herosphp\utils\HashUtils;

Abstract class ACache implements ICache {

    /**
     * 每个缓存文件夹的文件容量
     * @var int
     */
    protected static $_FILE_OPACITY = 1000;

    /**
     * 缓存配置参数
     * @var array
     */
    protected $configs = array();

    /**
     * 缓存的基础路径,最好有语义,推荐使用action名称,  如article
     * @var string
     */
    protected $baseKey = 'default';

    /**
     * 缓存分类目录， 推荐使用当前调用的method操作,如 index,list,detail等
     * @var string
     */
    protected $ftype = null;

    /**
     * 缓存的分类因子,一般来说
     * 1. 如果是列表页,推荐使用页码$page
     * 2. 如果是详情页，推荐使用$id
     * @var int
     */
    protected $factor = null;

    /**
     * @param string $baseKey
     * @return $this
     */
    public function baseKey( $baseKey = null ) {
        if ( $baseKey ) $this->baseKey = $baseKey;
        return $this;

    }

    /**
     * @param int $factor
     * @return $this
     */
    public function factor( $factor = null ) {
        if ( $factor )  $this->factor = $factor;
        return $this;

    }

    /**
     * @param string $ftype
     * @return $this
     */
    public function ftype( $ftype = null ) {
        if ( $ftype ) $this->ftype = $ftype;
        return $this;
    }

    /**
     * 获取缓存文件路径
     * @param string $key
     * @param string $extension 缓存后缀
     * @return string
     */
    public function getCacheFile( $key = null, $extension='.cache' )
    {
        $cacheDir = $this->configs['cache_dir'];
        /**
         * 1. 如果有传入了缓存key,则默认为公共缓存，缓存文件全部放入公共缓存中
         * 2. 如果key=null,则认为是模块的特殊缓存，按照模块将缓存分类
         */
        if ( $key ) {
            $dir = getHashCode($key) % self::$_FILE_OPACITY;
            $cacheDir .= "common/{$dir}/";
            return $cacheDir.md5($key);
        } else {
            $cacheDir .= $this->baseKey.'/';
            $filename = $this->baseKey;
            if ( $this->ftype ) {
                $cacheDir .= $this->ftype .'/';
                $filename .= '_'.$this->ftype;
            }
            if ( $this->factor ) {
                if ( is_numeric($this->factor) ) {
                    $cacheDir .= ($this->factor % self::$_FILE_OPACITY).'/';
                } else {
                    $factor = HashUtils::JSHash($this->factor);
                    $cacheDir .= ($factor % self::$_FILE_OPACITY).'/';
                }
                $filename .= '_'.$this->factor;
            }
            return $cacheDir.$filename.$extension;
        }

    }


}
