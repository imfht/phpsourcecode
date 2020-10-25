<?php
namespace Core;

class Config
{

    protected static $_systemDir = __DIR__ . '/config/';

    protected static $_moduleDir = MODULES_DIR;

    protected static $_themeDir = THEMES_DIR;

    protected static $_configCacheDir = CACHE_DIR.'config/';

    protected static $_webModulesDir = CONFIG_DIR.'modules/';

    protected static $_webThemesDir = CONFIG_DIR . 'themes/';

    protected static $_configDir = CONFIG_DIR;

    /**
     *
     * @param
     *            $string
     */
    public static function cache($string,$default = array())
    {
        // echo "string";
        if (self::cacheExists($string)) {
            $filePath = self::$_configCacheDir . $string . '.php';
            if (file_exists($filePath)) {
                include $filePath;
                if (isset($settings)) {
                    return $settings;
                }
            }
        } else {
            self::createCache($string);
            return self::cache($string);
        }
        return $default;
    }

    public static function tag($name, $key = null)
    {
        $config = self::get($name);
        if ($key == null) {
            return $config;
        }
        if ($key != null && is_array($config) && isset($config[$key])) {
            return $config[$key];
        }
        return false;
    }

    /**
     *
     * @param
     *            $name
     */
    public static function cacheExists($name)
    {
        if (file_exists(self::$_configCacheDir . $name . '.php')) {
            return true;
        }
        return false;
    }

    public static function isExists($name)
    {
        if (self::get($name)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param
     *            $name
     */
    public static function createCache($name)
    {
        $modules = self::get('modules', array());
        $themes = self::get('themes', array());
        // print_r($di);
        $config = array();
        foreach ($modules as $module) {
            $mConfig = self::getModule($module . '.' . $name);
            $config = self::mergeHook($config, $mConfig);
            $cConfig = self::getWebModule($module . '.' . $name);
            $config = self::mergeHook($config, $cConfig);
        }
        foreach ($themes as $module) {
            $mConfig = self::getTheme($module . '.' . $name);
            $config = self::mergeHook($config, $mConfig);
            $cConfig = self::getWebTheme($module . '.' . $name);
            $config = self::mergeHook($config, $cConfig);
        }
        $filePath = self::$_configCacheDir . $name . '.php';
        $settings = '<?php $settings = ' . var_export($config, true) . ';';
        $output = file_put_contents($filePath, $settings);
        if ($output) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param
     *            $hook
     * @param
     *            $add
     * @return mixed
     */
    public static function mergeHook($hook, $add)
    {
        if (is_array($add)) {
            foreach ($add as $key => $value) {
                if (isset($hook[$key])) {
                    $hook[$key] += $value;
                } else {
                    $hook[$key] = $value;
                }
            }
        }
        return $hook;
    }

    /**
     *
     * @param
     *            $name
     */
    public static function deleteCache($name)
    {
        if (is_array($name)) {
            foreach ($name as $n) {
                $filePath = self::$_configCacheDir . $n . '.php';
                File::rmFile($filePath);
            }
        } elseif (is_string($name)) {
            File::rmFile(self::$_configCacheDir . $name . '.php');
        }
        return true;
    }

    public static function clearCache()
    {
        return File::clearDir(self::$_configCacheDir);
    }

    public static function delete($config){
        $exConfig = explode('.', $config);
        switch (count($exConfig)) {
            case 1:
                $path = self::$_configDir . $exConfig[0] . '.php';
                break;
            case 2:
                $path = self::$_moduleDir . ucfirst($exConfig[0]) . '/config/' . $exConfig[1] . '.php';
                break;
            case 3:
                switch ($exConfig[0]) {
                    case 't':
                        $path = self::$_webThemesDir . $exConfig[1] . '/' . $exConfig[2] . '.php';
                        break;
                    case 'm':
                        $path = self::$_webModulesDir . $exConfig[1] . '/' . $exConfig[2] . '.php';
                        break;
                    default:
                        $path = self::$_themeDir . $exConfig[1] . '/config/' . $exConfig[2] . '.php';
                        break;
                }
                break;
            default:
                return false;
                break;
        }
        if(file_exists($path) && unlink($path)){
            return true;
        }elseif(!file_exists($path)){
            return true;
        }
        return false;
    }
    /**
     *
     * @param
     *            $config
     * @param null $default            
     */
    public static function get($config = false, $default = array())
    {
        if (! $config) {
            return self::get('config');
        }
        $exConfig = explode('.', $config);
        switch (count($exConfig)) {
            case 1:
                $path = self::$_configDir . $exConfig[0] . '.php';
                break;
            case 2:
                $path = self::$_moduleDir . ucfirst($exConfig[0]) . '/config/' . $exConfig[1] . '.php';
                break;
            case 3:
                switch ($exConfig[0]) {
                    case 't':
                        $path = self::$_webThemesDir . $exConfig[1] . '/' . $exConfig[2] . '.php';
                        break;
                    case 'm':
                        $path = self::$_webModulesDir . $exConfig[1] . '/' . $exConfig[2] . '.php';
                        break;
                    default:
                        $path = self::$_themeDir . $exConfig[1] . '/config/' . $exConfig[2] . '.php';
                        break;
                }
                break;
            default:
                return $default;
                break;
        }
        if (file_exists($path)) {
            include $path;
        } else {
            return $default;
        }
        if (isset($settings)) {
            return $settings;
        }
        return $default;
    }

    public static function getModule($config = fale, $default = false)
    {
        return self::get($config, $default);
    }

    public static function getWebModule($config = false, $default = false)
    {
        return self::get('m.' . $config, $default);
    }

    public static function getTheme($config = fale, $default = false)
    {
        return self::get('t.' . $config, $default);
    }

    public static function getWebTheme($config = false, $default = false)
    {
        return self::get('t.' . $config, $default);
    }

    public static function setModule($config = fale, $data = false)
    {
        return self::set($config, $data);
    }

    public static function setWebModule($config = false, $data = false)
    {
        return self::set('m.' . $config, $data);
    }

    public static function setTheme($config = fale, $data = false)
    {
        return self::set('t.' . $config, $data);
    }

    public static function setWebTheme($config = false, $data = false)
    {
        return self::set('t.' . $config, $data);
    }

    /**
     *
     * @param
     *            $config
     * @param
     *            $data
     */
    public static function set($config, $data = false)
    {
        if ($data === false) {
            throw new \Exception('保存错误，数据不合法');
        }
        $exConfig = explode('.', $config);
        switch (count($exConfig)) {
            case 1:
                $path = self::$_configDir . $exConfig[0] . '.php';
                break;
            case 2:
                $path = self::$_moduleDir . ucfirst($exConfig[0]) . '/config/' . $exConfig[1] . '.php';
                break;
            case 3:
                switch ($exConfig[0]) {
                    case 't':
                        $path = self::$_webThemesDir . $exConfig[1] . '/' . $exConfig[2] . '.php';
                        break;
                    case 'm':
                        $path = self::$_webModulesDir . $exConfig[1] . '/' . $exConfig[2] . '.php';
                        break;
                    default:
                        $path = self::$_themeDir . $exConfig[1] . '/config/' . $exConfig[2] . '.php';
                        break;
                }
                break;
            default:
                return $default;
                break;
        }
        $settings = '<?php $settings = ' . var_export($data, true) . ';';
        $output = file_put_contents($path, $settings);
        if ($output) {
            return true;
        } else {
            return false;
        }
    }

    public static function printCode($code,$output = TRUE)
    {
        $codeString = '<pre><h3>打印变量</h3>';
        $codeString .= print_r($code,true);
        $codeString .= '</pre>';
        if($output){
            echo $codeString;
        }else{
            return $codeString;
        }
    }
}
