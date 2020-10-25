<?php

namespace CIPlus;
/**
 * 类模板
 * Class CIClass
 * @package CIPlus
 */
abstract class CIClass {
    protected $CI;

    public function __construct(array $config = array()) {
        $this->CI =& get_instance();
        if (!empty($config)) {
            $this->initConf($config);
        }
    }

    /**
     * 根据参数初始化类
     * @param $config
     */
    private function initConf($config) {
        foreach ($config as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }

    /**
     * 加载CI config配置文件
     * @param $name : 配置文件名称
     * @return array
     */
    protected function loadConf($name) {
        $this->CI->config->load($name, true, true);
        $config = $this->CI->config->item($name);
        if (is_array($config)) {
            foreach ($config as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        }
        return $config;
    }

    /**
     * 加载插件库
     * @param $plugins
     * @return bool
     */
    protected function loadPlugins($plugins) {
        if (is_array($plugins)) {
            foreach ($plugins as $item) {
                $this->loadPlugin($item);
            }
            return true;
        } else {
            $this->loadPlugin($plugins);
        }
        return false;
    }

    protected function loadPlugin($plugin) {
        if (is_string($plugin)) {
            $this->CI->load->add_package_path(FCPATH . 'plugins' . DIRECTORY_SEPARATOR . $plugin);
            return true;
        }
        return false;
    }
}