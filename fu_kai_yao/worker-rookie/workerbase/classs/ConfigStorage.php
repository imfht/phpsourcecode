<?php
namespace workerbase\classs;

use workerbase\classs\datalevels\Redis;

/**
 * 配置暂存
 * Class ConfigStorage
 * @author fukaiyao
 */
class ConfigStorage
{
    const WK_CONFIG_STORAGE_KEY = 'wk_config_storage_key';

    private $_redis;

    public function __construct()
    {
        $this->_redis = Redis::getInstance([], true);
    }

    /**
     * 获取配置
     * @param $type -配置类型
     * @return String
     */
    public function getConfig($type)
    {
        $configJson = $this->_redis->get(self::WK_CONFIG_STORAGE_KEY . ':' . $type);
        $this->_redis->delete(self::WK_CONFIG_STORAGE_KEY . ':' . $type);
        if (empty($configJson)) {
            return '';
        }
        return json_decode($configJson, true);
    }

    /**
     * 设置配置
     * @param $type -配置类型
     * @param $value -配置值
     * @param $expire -有效期（秒）
     * @return bool
     */
    public function setConfig($type, $value, $expire = 20)
    {
        return $this->_redis->set(self::WK_CONFIG_STORAGE_KEY . ':' . $type, json_encode($value), $expire);
    }

    public function __destruct()
    {
        $this->_redis->close();
        unset($this->_redis);
    }

}