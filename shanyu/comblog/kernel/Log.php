<?php
namespace Kernel;

class Log
{
    // 日志类型
    protected $type = ['log', 'error', 'info', 'sql', 'notice', 'alert'];
    // 日志信息
    protected $log = [];
    // 配置参数
    protected $path;
    // 实例
    protected static $instance;
    /**
     * 日志初始化
     * @param array $config
     */
    public function __construct()
    {
        $this->path = RUNTIME_PATH.'logs/';
    }
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * 获取日志信息
     * @param string $type 信息类型
     * @return array
     */
    public function getLog($type = '')
    {
        return $type ? $this->log[$type] : $this->log;
    }

    /**
     * 记录调试信息
     * @param mixed  $msg  调试信息
     * @param string $type 信息类型
     * @return void
     */
    public function record($text, $type = 'log')
    {
        $this->log[] = ['type'=>$type,'text'=>$text];
    }

    /**
     * 清空日志信息
     * @return void
     */
    public function clear()
    {
        $this->log = [];
    }

    /**
     * 保存调试信息
     * @return bool
     */
    public function save()
    {
        $message = '';
        foreach ($this->log as $msg) {
            if(!is_string($msg['text'])){
                $msg['text'] = json_encode($msg['text']);
            }
            $message .='[' . $msg['type'] . '] ' . $msg['text'] . "\r\n";
        }
        $this->write($message);
    }


    protected function getServerParam()
    {
        $time   = date('H:i:s',time());
        $remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'CLI';
        $uri    = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        $serverParam="[{$time}] {$remote} {$method} {$uri}\r\n";
        return $serverParam;
    }
    protected function getFileName()
    {
        $fileName = $this->path . date('Ymd',time()) . '.log';
        $filePath = dirname($fileName);
        !is_dir($filePath) && mkdir($filePath, 0755, true);
        return $fileName;
    }

    public function write($message)
    {
        $fileName=$this->getFileName();
        $serverParam=$this->getServerParam();

        error_log($serverParam.$message."\r\n", 3, $fileName);
    }

}