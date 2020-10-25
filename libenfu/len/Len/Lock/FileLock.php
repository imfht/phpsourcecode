<?php

namespace Lock;

class FileLock
{
    /**
     * @var string
     */
    private $path = '/dev/shm';

    /**
     * @var string
     */
    private $fp = '';

    /**
     * @var string
     */
    private $lockFile = '';

    /**
     * fileLock constructor.
     * @param $uniqid
     */
    public function __construct($uniqid)
    {
        $this->lockFile = $this->path . md5($uniqid) . '.lock';

        return $this->lock();
    }

    /**
     * @return bool
     */
    public function lock()
    {
        $this->fp = fopen($this->lockFile, 'a+');
        if ($this->fp === false) {
            return false;
        }
        register_shutdown_function(array($this, 'unlock'));

        return flock($this->fp, LOCK_EX);//获取独占锁
    }

    /**
     * 解锁 (尽量手动解锁)
     */
    public function unlock()
    {
        if ($this->fp !== false) {
            @flock($this->fp, LOCK_UN);
            clearstatcache();
        }
        @fclose($this->fp);
        @unlink($this->lockFile);
    }
}