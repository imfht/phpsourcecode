<?php

namespace SgIoc\Cache;
/**
 * memcache 引擎
 * User: freelife2020@163.com
 * Date: 2018/4/9
 * Time: 15:07
 */
class MemcacheConnector
{
    protected static $link;

    /**
     * 单例
     * @param $config
     * @return \Memcache
     */
    public static function getInstance($config)
    {
        if (!self::$link instanceof \Memcache) {
            self::$link = self::connect($config);
        }
        return self::$link;
    }

    /**
     * 连接
     * @param $config
     * @return \Memcache
     * @throws \Exception
     */
    protected static function connect($config)
    {
        if (!isset($config['hosts'])) {
            throw new \Exception('The ' . __METHOD__ . ' engine configure item does not have a hosts or port node.');
        }
        if (!extension_loaded('memcache')) {
            throw new \Exception('memcache extension is not installed.');
        }
        if (isset($config['open']) && !$config['open']) {
            throw new \Exception('memcache switch does not set true');
        }
        $link = new \Memcache();
        if (!is_array($config['hosts'])) {
            throw new \Exception('hosts item does not array.');
        }
        foreach ($config['hosts'] as $item) {
            $bool = $link->addServer($item[0], $item[1], true, $item[2]);
        }
        return $link;
    }
}