<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo;


use Timo\Core\Request;
use Timo\Exception\CoreException;

/**
 * 加载类
 *
 * Class Loader
 * @package Timo
 */
class Loader
{
    /**
     * @var array 单例容器
     */
    protected static $instances = [];

    /**
     * 获取文件路径
     *
     * @param string $file_str
     * @example Loader::getFilePath('::config/site.config.php');
     * @return string
     */
    public static function getFilePath($file_str)
    {
        $_path = [
            'project' => ROOT_PATH,
            'app' => APP_DIR_PATH,
            'static' => Request::getInstance()->getScriptFilePath() . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR
        ];

        list($path, $file) = explode('::', $file_str);

        if (!$path) {
            $path = 'project';
        }

        return $_path[$path] . str_replace('/', DIRECTORY_SEPARATOR, $file);
    }

    /**
     * 读取文件
     *
     * @param string $file
     * @param bool $return_content 是否返回文件内容
     * @return mixed
     * @throws CoreException
     */
    public static function read($file, $return_content = false)
    {
        if (!file_exists($file)) {
            throw new CoreException("{$file} 文件不存在");
        }

        static $cache = null;
        $flag = (int)$return_content;
        if (isset($cache[$file][$flag])) {
            return $cache[$file][$flag];
        }

        if (is_readable($file)) {
            if ($return_content) {
                $file_content = file_get_contents($file);
                $cache[$file][$flag] = $file_content;
                return $file_content;
            }

            switch (Helper::getFileExt($file)) {
                case 'php' :
                    $data = require $file;
                    $cache[$file][$flag] = $data;
                    break;

                case 'json' :
                    $data = json_decode(file_get_contents($file), true);
                    $cache[$file][$flag] = $data;
                    break;

                case 'ini':
                    $data = parse_ini_file($file, true);
                    $cache[$file][$flag] = $data;
                    break;

                default :
                    throw new CoreException('不支持的解析格式：' . $file);
            }

            return $data;
        } else {
            throw new CoreException("文件:{$file}不可读");
        }
    }

    /**
     * 获取模型实例
     *
     * @param string $model 模型名称
     * @param array $params 参数
     * @return mixed
     */
    public static function model($model, ...$params)
    {
        return self::singleton($model, $params);
    }

    /**
     * 获取层实例
     *
     * @param $name
     * @param array $params
     * @return mixed
     */
    public static function layer($name, ...$params)
    {
        return self::singleton($name, $params);
    }

    /**
     * 获取类的单例
     *
     * @param $className
     * @param array $params
     * @return mixed
     */
    public static function singleton($className, $params = [])
    {
        $params = !is_array($params) ? [$params] : $params;
        $alias = $className . implode('', $params);
        if (isset(self::$instances[$alias])) {
            return self::$instances[$alias];
        }

        $class = new \ReflectionClass($className);
        self::$instances[$alias] = $class->newInstanceArgs($params);

        return self::$instances[$alias];
    }

    /**
     * 清空单例容器
     */
    public static function destroy()
    {
        self::$instances = [];
    }
}
