<?php
namespace Core;

use Phalcon\Cache\Frontend\Data as FrontendData;
use Phalcon\Cache\Frontend\Output as FrontendOutput;
use Phalcon\Cache\Backend\File as CacheFile;
use Phalcon\Cache\Backend\Memory as CacheMemory;
use Phalcon\Cache\Backend\Mongo as CacheMongo;
use Phalcon\Cache\Backend\Apc as CacheApc;
use Phalcon\Cache\Backend\Xcache as CacheXcache;
use Phalcon\Cache\Backend\Redis as CacheRedis;

class Cache
{

    public function getCache($config, $type){
        switch ($type) {
            case 'data':
                return $this->createCache($config,'data');
                break;
            case 'output':
                return $this->createCache($config,'output');
                break;
            default:
                return false;
        }
    }

    public function createCache($config,$type){
        $defaultConfig = [
            'adapters' => 'file',
            'frontendOptions' => [
                "lifetime" => 86400,
            ],
            'config' => [
                'cacheDir' => CACHE_DIR . 'data/'
            ]
        ];
        $cache = false;
        $config = array_merge($defaultConfig,$config);

        if($type == 'data'){
            $frontend = new FrontendData($config['frontendOptions']);
        }else{
            $frontend = new FrontendOutput($config['frontendOptions']);
        }
        switch ($config['adapters']){
            case 'file':
                $cache = new CacheFile(
                    $frontend,
                    array(
                        'cacheDir' => CACHE_DIR . 'data/',
                    )
                );
                break;
            case 'memcache':
                $cache = new CacheMemory(
                    $frontend,
                    array(
                        'cacheDir' => CACHE_DIR . 'data/',
                    )
                );
                break;
            case 'apc':
                $cache = new CacheApc(
                    $frontend,
                    array(
                        'cacheDir' => CACHE_DIR . 'data/',
                    )
                );
                break;
            case 'mongo':
                $cache = new CacheMongo(
                    $frontend,
                    array(
                        'cacheDir' => CACHE_DIR . 'data/',
                    )
                );
                break;
            case 'xcache':
                $cache = new CacheXcache(
                    $frontend,
                    array(
                        'cacheDir' => CACHE_DIR . 'data/',
                    )
                );
                break;
            case 'redis':
                $cache = new CacheRedis(
                    $frontend,
                    array(
                        'cacheDir' => CACHE_DIR . 'data/',
                    )
                );
                break;
        }

        return $cache;
    }
}