<?php
namespace workerbase\classs;
/**
 * 配置管理
 * @method static read($item = "", $type = "config", $isFlush = false, $appConfigPathName = null, $configPathName = 'config') 获取配置
 * @author fukaiyao
 */
class Config
{
    //项目根目录
    private $_pPath = WORKER_PROJECT_PATH;

    private static $_instance;

    /**
     * 配置缓存
     */
    private $_configCache = array();

    /**
     * 环境参数缓存
     */
    private $_envCache = null;

    private $_envHasMerge = array();

    /**
     * 获取单例
     * @return Config
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Config();
        }
        return self::$_instance;
    }

    /**
     * 获取配置
     * @param string $item            -  配置项key, 多级数组key之间用 . 分隔, 例:  components.db.username
     * @param string $type            -  配置类型, 即: 配置文件名
     * @param bool $isFlush           - 是否强制刷新缓存
     * @param string $appConfigPathName   - 配置文件夹下的应用配置文件夹名称
     * @param string $configPathName   - 配置文件夹名称
     * @throws \Exception
     * @return mixed
     */
    public function get($item = "", $type = "config", $isFlush = false, $appConfigPathName = null, $configPathName = 'config')
    {
        if (defined('WK_APP_ID') && is_null($appConfigPathName)) {
            $appConfigPathName = WK_APP_ID;
        }

        $this->_load($type, $isFlush, $appConfigPathName, $configPathName);
        $this->_loadEnv($type, $isFlush, $configPathName);

        if (empty($item)) {
            return $this->_configCache[$type];
        }
        $keys = explode(".", $item);
        if (empty($keys)) {
            throw new \Exception("config item invalid. item={$item}");
        }
        $configItem= $this->_configCache[$type];

        foreach ($keys as $key) {
            if (isset($configItem[$key])) {
                $configItem = $configItem[$key];
            }
            else {
                throw new \Exception("config item not found. item={$item}");
            }
        }
        return $configItem;
    }

    /**
     * 根据类型加载配置文件
     * @param string $type  - 配置文件类型
     * @param bool $isFlush - 是否刷新缓存，从新加载配置
     */
    private function _load($type, $isFlush = false, $appConfigPathName = null, $configPathName = 'config')
    {
        $type = strtolower($type);
        //检测缓存
        if (!$isFlush && isset($this->_configCache[$type])) {
            return ;
        }

        $pPath = $this->_pPath;

        //加载应用配置
        $appConfig = array();
        if (!empty($appConfigPathName)) {
            $file = "{$pPath}/{$configPathName}/{$appConfigPathName}/{$type}.php";
            if (file_exists($file)) {
                $appConfig = require $file;
            }
        }

        //加载默认配置
        $file ="{$pPath}/{$configPathName}/{$type}.php";
        if (!file_exists($file)) {
            throw new \Exception("config file not found. file={$file}");
        }
        $this->_configCache[$type] = require $file;

        if (!empty($appConfig)) {
            //应用配置覆盖默认配置
            $this->_configCache[$type] = $this->_mergeArray($this->_configCache[$type], $appConfig);
        }
    }

    /**
     * 加载环境参数
     * @param string $type  - 配置文件类型
     * @param bool $isFlush - 是否刷新缓存，从新加载配置
     */
    private function _loadEnv($type, $isFlush = false, $configPathName = 'config')
    {
        if (isset($this->_envHasMerge[$type]) && $this->_envHasMerge[$type]) {
            //已经合并过了
            return ;
        }

        if (!isset($this->_configCache[$type])) {
            return '';
        }

        if (!$isFlush && ! empty($this->_envCache)) {
            if (!isset($this->_envCache[$type])) {
                //没有环境参数
                return ;
            }
            $this->_configCache[$type] = $this->_mergeArray($this->_configCache[$type], $this->_envCache[$type]);
            $this->_envHasMerge[$type] = true;
            return;
        }

        $pPath = $this->_pPath;
        $file = "{$pPath}/{$configPathName}/env.php";
        if (!file_exists($file) || isset($this->_envHasMerge[$type])) {
            return ;
        }

        $envs = require $file;
        if (empty($envs) || !is_array($envs)) {
            throw new \Exception("env config invalid.");
        }

        //格式化环境参数
        $retEnv = array();
        foreach ($envs as $key => $value) {
            $keys  = explode(".", $key);
            if (empty($keys)) {
                throw new \Exception("env config invalid, item={$key}");
            }
            $ret = null;
            try {
                $ret = $this->_createArrayByKey($keys, $value);
            } catch (\Exception $e) {
                throw new \Exception("env config invalid, item={$key}");
            }

            //递归合并数组
            $retEnv = array_merge_recursive($retEnv, $ret);
        }

        //cache 缓存参数
        $this->_envCache = $retEnv;

        //覆盖默认参数
        $this->_configCache = $this->_mergeArray($this->_configCache, $retEnv);
        $this->_envHasMerge[$type] = true;
    }

    private function _createArrayByKey($keys, $value)
    {
        if (empty($keys)) {
            return null;
        }
        $key = array_shift($keys);
        if (empty($key)) {
            throw new \Exception("env config item invalid");
        }

        $ret = array();
        if (empty($keys)) {
            $ret[$key] = $value;
        }
        else {
            $ret[$key] = $this->_createArrayByKey($keys, $value);
        }
        return $ret;
    }

    /**
     * 合并多个数组
     * ps:
     *1. 如果数组key为数值, 则直接合并数组值，不会覆盖
     *2. 如果key同名，则后一个数组覆盖前一个数组的值
     *3. 如果数组值存在特殊key  "c_overwrite" ， 则直接覆盖前一个数组的值
     *
     * @param array $a
     * @param array $b
     * @return array
     */
    private  function _mergeArray($a, $b)
    {
        $args = func_get_args();
        $isMergeTheSameKey = true;
        if (count($args) > 2) {
            //弹出最后一个特殊参数
            $isMergeTheSameKey = array_pop($args);
        }

        $res = array_shift($args);//默认的配置
        while (! empty($args)) {
            $next = array_shift($args);//应用的配置
            foreach ($next as $k => $v) {
                //只能合并同名key的值
                if ($isMergeTheSameKey && !is_integer($k) && !isset($res[$k])) { //应用配置必须在默认配置中存在
                    continue;
                }

                //特殊值处理，只要值是数组，并且包含 c_overwrite, 则直接覆盖
                if (is_array($v) && isset($res[$k]) && isset($v['c_overwrite'])) {
                    unset($v['c_overwrite']);
                    $res[$k] = $v;
                    continue;
                }

                if (is_integer($k)) {
                    isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
                }
                elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = $this->_mergeArray($res[$k], $v);
                }
                else {
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }

    /**
     * 静态方法调用
     * @access public
     * @param  string $method 调用方法
     * @param  mixed  $args   参数
     * @return void
     */
    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::getInstance(), 'get'], $args);
    }

}