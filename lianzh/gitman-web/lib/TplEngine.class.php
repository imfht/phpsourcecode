<?php
/**
 * TplEngine 实现了一个简单的、使用 PHP 自身作为模版语言，
 * 带有缓存功能的模版引擎
 */
class TplEngine
{
    private $templateDir; //模板文件所在路径

    private $cacheLifetime = 900; //缓存过期时间

    private $enableCache = false; //指示是否使用 cache

    private $cacheDir; //缓存文件保存位置

    private $vars = array(); //模板变量

    private $cacheState = array(); //保存各个缓存内容的缓存状态
        
    /**
     * @param array $params
     */
    function __construct(array $params) {
    	$keys = array(
            'templateDir', 'cacheDir', 'cacheLifeTime', 'enableCache',
        );
    	foreach ($keys as $key) {
        	if (array_key_exists($key,$params))
        		$this->{$key} = $params[$key];
        }
        if (!is_dir($this->templateDir))
    		throw new Exception('templateDir 不是有效的目录');
    		
    	if ($this->enableCache){
    		if (!is_dir($this->cacheDir))
    			throw new Exception('cacheDir 不是有效的目录');
    	}
    }
	
    /**
     * 重设 模版变量
     *
     */
    function resetVars(){ $this->vars = array();}
    
    /**
     * 设置模板变量
     *
     * @param mixed $name 模板变量名称
     * @param mixed $value 变量内容
     */
    function assign($name, $value = null) {
        if (is_array($name) && is_null($value)) {
            $this->vars = array_merge($this->vars, $name);
        } else {
            $this->vars[$name] = $value;
        }
    }

    /**
     * 构造模板输出内容
     *
     * @param string $file 模板文件名
     * @param string $cacheId 缓存 ID，如果指定该值则会使用该内容的缓存输出
     *
     * @return string
     */
    function fetch($file, $cacheId = null) {
        if ($this->enableCache) {
            $cacheFile = $this->_getCacheFile($file, $cacheId);
            if ($this->isCached($file, $cacheId)) {
                return file_get_contents($cacheFile);
            }
        }

        // 生成输出内容并缓存
        extract($this->vars);
        ob_start();

        include($this->templateDir . '/' . $file);
        $contents = ob_get_contents();
        ob_end_clean();

        if ($this->enableCache) {
            // 缓存输出内容，并保存缓存状态
            $this->cacheState[$cacheFile] = file_put_contents($cacheFile, $contents) > 0;
        }

        return $contents;
    }

    /**
     * 显示指定模版的内容
     *
     * @param string $file 模板文件名
     * @param string $cacheId 缓存 ID，如果指定该值则会使用该内容的缓存输出
     */
    function display($file, $cacheId = null) {
        echo $this->fetch($file, $cacheId);
    }

    /**
     * 检查内容是否已经被缓存
     *
     * @param string $file 模板文件名
     * @param string $cacheId 缓存 ID
     *
     * @return boolean
     */
    function isCached($file, $cacheId = null) {
        // 如果禁用缓存则返回 false
        if (!$this->enableCache) { return false; }

        // 如果缓存标志有效返回 true
        $cacheFile = $this->_getCacheFile($file, $cacheId);
        if (isset($this->cacheState[$cacheFile]) && $this->cacheState[$cacheFile]) {
            return true;
        }

        // 检查缓存文件是否存在
        if (!is_readable($cacheFile)) { return false; }

        // 检查缓存文件是否已经过期
        $mtime = filemtime($cacheFile);
        if ($mtime == false) { return false; }
        if (($mtime + $this->cacheLifetime) < time()) {
            $this->cacheState[$cacheFile] = false;
            @unlink($cacheFile);
            return false;
        }

        $this->cacheState[$cacheFile] = true;
        return true;
    }

    /**
     * 清除指定的缓存
     *
     * @param string $file 模板资源名
     * @param string $cacheId 缓存 ID
     */
    function cleanCache($file, $cacheId = null) {
        @unlink($this->_getCacheFile($file, $cacheId));
    }

    /**
     * 清除所有缓存
     */
    function cleanAllCache() {
        foreach (glob($this->cacheDir . '/' . "*.html") as $filename) {
            @unlink($filename);
        }
    }

    /**
     * 返回缓存文件名
     *
     * @param string $file
     * @param string $cacheId
     *
     * @return string
     */
    private function _getCacheFile($file, $cacheId) {        
        return $this->cacheDir . '/' . rawurlencode($file . '-' . $cacheId) . '.html';
    }
}
