<?php
namespace Framework;

/**
 * Config load class
 * @package Framework
 * @author kelezyb
 * @version 0.9.0.1
 */
class SZConfig {
    /**
     * @var SZConfig
     */
    private static $instance = null;

    /**
     * 获得配置加载实例
     * @return SZConfig
     */
    public static function Instance() {
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @var array
     */
    private $configs;

    private function __construct() {
        $this->configs = array();
    }

    public function get($key = null, $default = null, $module = 'main') {
        if (!isset($this->configs[$module])) {  //未加载的模块先加载
            if (!$this->_load($module)) {
                throw new \Exception('Config load module {$module} not found.');
            }
        }

        if (null === $key) {
            $result = $this->configs[$module];
        } else {
            if (isset($this->configs[$module][$key])) {
                $result = $this->configs[$module][$key];
            } else {
                $result = $default;
            }
        }

        return $result;
    }

    /**
     * 配置模块加载
     *
     * @param string $module
     * @return bool
     */
    private function _load($module) {
        $path = ROOT_PATH . DS . 'App' . DS . 'Configs' . DS . $module . '.php';
        if (is_readable($path)) {
            $this->configs[$module] = include($path);

            return true;
        } else {
            return false;
        }
    }
}