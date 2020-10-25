<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-05-31
 * Time: 17:44
 */

namespace inhere\gearman\traits;

/**
 * Class ProcessMessageTrait - IPC
 * @package inhere\gearman\traits
 */
trait ProcessMessageTrait
{
    /**
     * pipe Handle
     * @var resource
     */
    protected $pipe;

    /**
     * @return bool
     */
    protected function createPipe()
    {
        if (!$this->config['enablePipe']) {
            return false;
        }

        //创建管道
        $pipeFile = "/tmp/{$this->name}.pipe";

        if(!file_exists($pipeFile) && !posix_mkfifo($pipeFile, 0666)){
            $this->stderr("Create the pipe failed! PATH: $pipeFile");
        }

        $this->pipe = fopen($pipeFile, 'wr');
        stream_set_blocking($this->pipe, false);  //设置成读取非阻塞

        return true;
    }

    /**
     * @param int $bufferSize
     * @return bool
     */
    protected function readMessage($bufferSize = 2048)
    {
        if (!$this->pipe) {
            return false;
        }

        // 读取管道数据
        $string = fread($this->pipe, $bufferSize);
        $data = json_decode($string);

        return $data;
    }

    /**
     * @param string $command
     * @param string|array $data
     * @return bool|int
     */
    protected function sendMessage($command, $data)
    {
        if (!$this->pipe) {
            return false;
        }

        // 写入数据到管道
        $len = fwrite($this->pipe, json_encode([
            'command' => $command,
            'data' => $data,
        ]));

        return $len;
    }
}
