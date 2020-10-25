<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/11
 * Time: 下午11:01
 */

namespace inhere\gearman\tools;

/**
 * Class FileLogger
 *
 * @usage create job logger in entry file
 *
 * ```
 * FileLogger::create(__DIR__ . '/logs/jobs', FileLogger::SPLIT_DAY);
 *
 * ... in logic file
 *
 * FileLogger::info('message', ['data'], 'test_job');
 * FileLogger::err('message', ['data'], 'test_job');
 * ```
 *
 * @package inhere\gearman\tools
 *
 * @method static debug($msg, array $data = [], $filename = 'default.log')
 * @method static info($msg, array $data = [], $filename = 'default.log')
 * @method static notice($msg, array $data = [], $filename = 'default.log')
 * @method static warning($msg, array $data = [], $filename = 'default.log')
 * @method static error($msg, array $data = [], $filename = 'default.log')
 */
class FileLogger
{
    /**
     * Log file save type.
     */
    const SPLIT_NO = '';
    const SPLIT_DAY = 'day';
    const SPLIT_HOUR = 'hour';

    /**
     * @var static
     */
    private static $instance;

    /**
     * @var array
     */
    protected static $allow = ['debug', 'info', 'notice', 'warning', 'error'];

    /**
     * @param string $basePath
     * @param string $splitType
     * @return FileLogger|static
     */
    public static function create($basePath, $splitType = '')
    {
        if (!self::$instance) {
            self::$instance = new static($basePath, $splitType);
        }

        return self::$instance;
    }

    /**
     * @return FileLogger
     */
    public static function instance()
    {
        return self::$instance;
    }

    /**
     * @param string $method
     * @param array $args
     * @return bool|int
     */
    public static function __callStatic($method, array $args)
    {
        if (!self::$instance) {
            throw new \RuntimeException('Please init logger instance on before usage.');
        }

        if (in_array($method, static::$allow)) {
            $data = isset($args[1]) ? $args[1] : [];
            $filename = isset($args[2]) ? $args[2] : 'default.log';

            return self::$instance->log($args[0], $data, $filename, $method);
        }

        throw new \RuntimeException("Call unknown static method: $method.");
    }

    /**
     * alias of `warning()`
     * @param string $msg
     * @param array $data
     * @param string $filename
     * @return bool|int
     */
    public static function warn($msg, array $data = [], $filename = 'default.log')
    {
        return self::$instance->log($msg, $data, $filename, 'warning');
    }

    /**
     * alias of `error()`
     * @param string $msg
     * @param array $data
     * @param string $filename
     * @return bool|int
     */
    public static function err($msg, array $data = [], $filename = 'default.log')
    {
        return self::$instance->log($msg, $data, $filename, 'error');
    }

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string
     */
    private $splitType = '';

    /**
     * FileLogger constructor.
     * @param string $basePath
     * @param string $splitType
     */
    public function __construct($basePath, $splitType = '')
    {
        $this->basePath = $basePath;

        if ($splitType && !in_array($splitType, [self::SPLIT_DAY, self::SPLIT_HOUR])) {
            $splitType = self::SPLIT_DAY;
        }

        $this->splitType = $splitType;
    }

    /**
     * log data to file
     * @param $msg
     * @param array $data
     * @param string $filename
     * @param string $type
     * @return bool|int
     */
    public function log($msg, array $data = [], $filename = 'default.log', $type = 'info')
    {
        $file = $this->genLogFile($filename, true);

        $log = sprintf(
            '[%s] [%s] %s %s' . PHP_EOL,
            date('Y-m-d H:i:s'), strtoupper($type), $msg, $data ? json_encode($data) : ''
        );

        $fileHandle = @fopen($file, 'a');

        $len = fwrite($fileHandle, $log);

        fclose($fileHandle);

        return $len;
    }

    /**
     * gen real LogFile
     * @param string $filename
     * @param bool $createDir
     * @return string
     */
    public function genLogFile($filename, $createDir = false)
    {
        $file = $this->basePath . '/' . $filename;

        // log split type
        if (!$type = $this->splitType) {
            return $file;
        }

        $info = pathinfo($file);
        $dir = $info['dirname'];
        $name = isset($info['filename']) ? $info['filename'] : 'default';
        $ext = isset($info['extension']) ? $info['extension'] : 'log';

        if ($type === self::SPLIT_DAY) {
            $str = date('Ymd');
        } else {
            $str = date('Ymd_H');
        }

        if ($createDir && !is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        return "{$dir}/{$name}_{$str}.{$ext}";
    }
}
