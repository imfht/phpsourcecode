<?php

/**
 * Compiler
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\View;

class Compiler
{
    public $data = array();
    
    public $defaultEngineName = 'litwit';

    public $compilerRoot;

    public $engineName = null;

    public function __construct($engineName = null)
    {
        if (!empty($engineName)) {
            $this->engineName = $engineName;
        } else {
            $this->engineName = $this->defaultEngineName;
        }

        if (defined('STORAGE_PATH')) {
            $this->compilerRoot = STORAGE_PATH;
        } else {
            $this->compilerRoot = dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR;
        }
    }

    public function set($key, $val)
    {
        $this->data[$key] = $val;
    }

    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return $default;
    }

    /**
     * 设置引擎
     */
    public function setCompiler($compilerEngineName = null)
    {
        $this->engineName = strval($compilerEngineName);
    }
    
    /**
     * 获取引擎
     */
    public function getCompiler()
    {
        return $this->engineName;
    }
    
    /**
     * 获取解析后的内容
     */
    public function parse($str)
    {
        $engineName = __NAMESPACE__."\\Compiler\\".ucfirst($this->engineName);
        $engine = new $engineName;
        return $engine->parse($str);
    }
    
}
