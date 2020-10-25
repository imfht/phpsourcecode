<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/4/28
 * Time: 下午9:08
 */

namespace inhere\gearman\client;

/*
 usage:

Logger::make()->debug('A debug message');
Logger::make()->log('A info message');
Logger::make()->warn('A warning');
Logger::make()->error('A serious problem');
Logger::make()->error('A error', array('an array?', 'interesting...', 'structured log messages!'));
 */

/**
 * Class Logger
 * @package inhere\gearman\client
 */
class Logger extends JobClient
{
    /**
     * @var array
     */
    private static $instances = [];

    /**
     * Fetch (and make if needed) an instance of this logger.
     *
     * @param string $servers
     * @param string $jobQueue
     * @return Logger
     */
    public static function make($servers = '127.0.0.1:4730', $jobQueue = 'app_log')
    {
        $hash = $jobQueue . substr(md5(serialize($servers)), 0, 7);

        if (!array_key_exists($hash, self::$instances)) {
            self::$instances[$hash] = new self($jobQueue, $servers);
        }

        return self::$instances[$hash];
    }

    /**
     * @var string
     */
    private $queue;

    /**
     * GMLogger constructor.
     * @param string $queue
     * @param string|array $servers
     */
    public function __construct($queue, $servers = '127.0.0.1:4730')
    {
        $this->queue = $queue;

        parent::__construct([
            'servers' => $servers
        ]);
    }

    /**
     * Log a message
     *
     * @param mixed $message
     * @param array $data
     * @param string $level
     */
    public function log($message, array $data = [], $level = 'INFO')
    {
        $this->addJob($this->queue, [
            'level' => strtoupper($level),
            'message' => $message,
            'data' => $data,
            'time' => time(),
            'host' => gethostname(),
        ], null, 'doBackground');
    }

    /**
     * Log a warning
     * @param mixed $message
     * @param array $data
     */
    public function debug($message, array $data = [])
    {
        $this->log($message, $data, 'DEBUG');
    }

    /**
     * Log a warning
     * @param mixed $message
     * @param array $data
     */
    public function warn($message, array $data = [])
    {
        $this->log($message, $data, 'WARN');
    }

    /**
     * Log an error
     * @param mixed $message
     * @param array $data
     */
    public function error($message, array $data = [])
    {
        $this->log($message, $data, 'ERROR');
    }

}