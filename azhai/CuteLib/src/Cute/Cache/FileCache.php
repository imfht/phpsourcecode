<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Cache;

/**
 * 文件缓存
 */
class FileCache extends BaseCache
{

    protected $filename = ''; //完整文件路径
    protected $ext = '.php';

    public function __construct($name, $dir = false)
    {
        if (empty($dir)) {
            $dir = sys_get_temp_dir();
        } else {
            $dir = rtrim($dir, DIRECTORY_SEPARATOR);
            @mkdir($dir, 0755, true);
        }
        $this->filename = $dir . DIRECTORY_SEPARATOR . $name . $this->ext;
    }

    public function prepare()
    {
        if (!is_readable($this->filename)) {
            touch($this->filename);
        }
        return $this;
    }

    public function readData()
    {
        $this->prepare();
        $bytes = filesize($this->filename);
        if ($bytes > 0) {
            return $this->readFile();
        }
    }

    public function writeData($data, $timeout = 0)
    {
        try {
            $this->prepare();
            $succ = $this->writeFile($data, $timeout);
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
        }
        return $succ;
    }

    public function removeData()
    {
        if (file_exists($this->filename)) {
            return unlink($this->filename);
        }
    }

    protected function readFile()
    {
        return (include $this->filename);
    }

    protected function writeFile($data, $timeout = 0)
    {
        $content = "<?php \nreturn " . var_export($data, true) . ";\n";
        $bytes = file_put_contents($this->filename, $content);
        return $bytes && $bytes > 0;
    }

}
