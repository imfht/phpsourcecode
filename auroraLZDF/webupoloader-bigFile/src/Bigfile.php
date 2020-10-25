<?php

namespace AuroraLZDF\Bigfile;

use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Log;

class Bigfile
{
    /**
     * 文件上传路径
     * @var
     */
    public $path;
    /**
     * 允许上传文件类型
     * @var
     */
    private $allowType;
    /**
     * 允许上传文件最大尺寸
     * @var
     */
    private $maxSize;
    /**
     * 随机文件名
     * @var
     */
    private $isRandName;
    /**
     * 真实文件名
     * @var
     */
    private $originName;
    /**
     * 临时文件名
     * @var
     */
    private $tmpFileName;
    /**
     * 文件类型
     * @var
     */
    private $fileType;
    /**
     * 文件尺寸
     * @var
     */
    private $fileSize;
    /**
     * 新文件名
     * @var
     */
    private $newFileName;
    /**
     * 错误等级
     * @var
     */
    private $errorNum = 0;
    /**
     * @var
     */
    private $errorMess = "";
    /**
     * 是否分片
     * @var
     */
    private $isChunk = false;
    /**
     * 分片文件索引
     * @var
     */
    private $indexOfChunk = 0;

    /**
     * Bigfile constructor.
     */
    public function __construct()
    {
        $this->path = config('bigfile.tmp_path');
        $this->allowType = config('bigfile.allow_type');
        $this->maxSize = config('bigfile.max_size');
        $this->isRandName = config('rand_name');
    }

    /**
     * 获取上传后的文件名称
     *
     * @return mixed
     */
    public function getFileName()
    {
        return $this->newFileName;
    }

    /**
     * 上传失败后，调用该方法则返回，上传出错信息
     *
     * @return mixed
     */
    public function getErrorMsg()
    {
        return $this->errorMess;
    }


    /**
     * 设置上传出错信息
     *
     * @return string
     */
    public function getError()
    {
        $str = "上传文件<span color='red'>{$this->originName}</span>时出错：";
        switch ($this->errorNum) {
            case 4:
                $str .= "没有文件被上传";
                break;
            case 3:
                $str .= "文件只有部分被上传";
                break;
            case 2:
                $str .= "上传文件的大小超过了HTML表单中MAX_FILE_SIZE选项指定的值";
                break;
            case 1:
                $str .= "上传的文件超过了php.ini中upload_max_filesize选项限制的值";
                break;
            case -1:
                $str .= "未允许的类型";
                break;
            case -2:
                $str .= "文件过大， 上传的文件夹不能超过{$this->maxSize}个字节";
                break;
            case -3:
                $str .= "上传失败";
                break;
            case -4:
                $str .= "建立存放上传文件目录失败，请重新指定上传目录";
                break;
            case -5:
                $str .= "必须指定上传文件的路径";
                break;

            default:
                $str .= "未知错误";
        }
        return $str . "<br>";
    }

    /** 调用该方法上传文件
     *
     * @param UploadedFile $uploadFile
     * @param $info
     * @return bool
     * @throws Exception
     */
    public function upload(UploadedFile $uploadFile, $info)
    {
        //判断是否为分块上传
        $this->checkChunk($info);

        if (!$this->checkFilePath($this->path)) {
            $this->errorMess = $this->getError();
            return false;
        }

        //设置文件信息
        if (!$this->setFiles($uploadFile)) {
            $this->errorMess = $this->getError();
            return false;
        }

        //如果是分块，则创建一个唯一名称的文件夹用来保存该文件的所有分块
        if ($this->isChunk) {
            $uploadDir = $this->path;
            $tmpName = $this->setDirNameForChunks($info);
            if (!$this->checkFilePath($uploadDir . '/' . $tmpName)) {
                $this->errorMess = $this->getError();
                return false;
            }

            //创建一个对应的文件，用来记录上传分块文件的修改时间，用于清理长期未完成的垃圾分块
            touch($uploadDir . '/' . $tmpName . '.tmp');
        }

        if ($this->checkFileSize() && $this->checkFileType()) {
            $this->setNewFileName();
            if ($this->copyFile()) {
                return $this->path . $this->newFileName;
            }
        }
    }

    /**
     * ！！ 合并切片文件 ！！
     *
     * @param $uniqueFileName
     * @param $chunksTotal
     * @param $fileExt
     * @return bool
     */
    public function chunksMerge($uniqueFileName, $chunksTotal, $fileExt)
    {
        $targetDir = $this->path . '/' . $uniqueFileName;

        // 检查对应文件夹中的分块文件数量是否和总数保持一致
        if ($chunksTotal > 1 && (count(scandir($targetDir)) - 2) == $chunksTotal) {
            // 同步锁机制
            $lockFd = fopen($targetDir . '.lock', "w");
            if (!flock($lockFd, LOCK_EX | LOCK_NB)) {
                fclose($lockFd);
                return false;
            }

            // 进行合并
            $this->fileType = $fileExt;
            $finalName = $this->path . '/' . ($this->setOption('newFileName', $this->proRandName()));
            $file = fopen($finalName, 'wb');

            for ($index = 0; $index < $chunksTotal; $index++) {
                $tmpFile = $targetDir . '/' . $index;

                $chunkFile = fopen($tmpFile, 'rb');
                $content = fread($chunkFile, filesize($tmpFile));
                fclose($chunkFile);

                fwrite($file, $content);
                //删除chunk文件
                unlink($tmpFile);
            }

            fclose($file);

            //删除chunk文件夹
            rmdir($targetDir);
            unlink($targetDir . '.tmp');

            //解锁
            flock($lockFd, LOCK_UN);
            fclose($lockFd);
            unlink($targetDir . '.lock');

            return $this->path . '/' . $this->newFileName;
        }
        return false;
    }

    /**
     * 为单个成员属性设置值
     *
     * @param $key
     * @param $val
     * @return mixed
     */
    private function setOption($key, $val)
    {
        $this->$key = $val;
        return $val;
    }

    /**
     * 根据文件的相关信息为分块数据创建文件夹
     * md5(当前登录用户的数据库id + 文件原始名称 + 文件类型 + 文件最后修改时间 + 文件总大小)
     *
     * @param $info
     * @return string
     */
    private function setDirNameForChunks($info)
    {
        $str = $info['userId'] . $info['name'] . $info['type'] . $info['lastModifiedDate'] . $info['size'];
        return md5($str);
    }

    /**
     * 设置和$_FILES有关的内容
     *
     * @param UploadedFile $uploadFile
     * @return bool
     */
    private function setFiles( UploadedFile $uploadFile)
    {
        //将文件上传的信息取出赋给变量
        $name = $uploadFile->getClientOriginalName();
        $tmp_name = $uploadFile->getRealPath();
        $size = $uploadFile->getSize();
        $error = $uploadFile->getError();

        $this->setOption('errorNum', $error);
        if ($error) {
            return false;
        }
        $this->setOption('originName', $name);
        $this->setOption('tmpFileName', $tmp_name);
        $aryStr = explode(".", $name);
        $this->setOption("fileType", strtolower($aryStr[count($aryStr) - 1]));
        $this->setOption("fileSize", $size);
        return true;
    }

    /**
     * 判断是否为分片上传
     *
     * @param $info
     * @return bool
     * @throws Exception
     */
    private function checkChunk($info)
    {
        if (isset($info['chunks']) && $info['chunks'] > 0) {
            $this->setOption("isChunk", true);

            if (isset($info['chunk']) && $info['chunk'] >= 0) {
                $this->setOption("indexOfChunk", $info['chunk']);
                return true;
            }

            throw new Exception('分块索引不合法');
        }

        return false;
    }

    /**
     * 设置上传后的文件名称
     */
    private function setNewFileName()
    {
        if ($this->isChunk) {        //如果是分块，则以分块的索引作为文件名称保存
            $this->setOption('newFileName', $this->indexOfChunk);
        } elseif ($this->isRandName) {
            $this->setOption('newFileName', $this->proRandName());
        } else {
            $this->setOption('newFileName', $this->originName);
        }
    }

    /**
     * 检查上传的文件是否是合法的类型
     *
     * @return bool
     */
    private function checkFileType()
    {
        if (in_array(strtolower($this->fileType), $this->allowType)) {
            return true;
        } else {
            $this->setOption('errorNum', -1);
            return false;
        }
    }

    /**
     * 检查上传的文件是否是允许的大小
     *
     * @return bool
     */
    private function checkFileSize()
    {
        if ($this->fileSize > $this->maxSize) {
            $this->setOption('errorNum', -5);
            return false;
        } else {
            return true;
        }
    }

    /**
     * 检查是否有存放上传文件的目录
     *
     * @param $target
     * @return bool
     */
    private function checkFilePath($target)
    {
        if (empty($target)) {
            $this->setOption('errorNum', -5);
            return false;
        }

        if (!file_exists($target) || !is_writable($target)) {
            if (!@mkdir($target, 0755)) {
                $this->setOption('errorNum', -4);
                return false;
            }
        }

        $this->path = $target;
        return true;
    }

    /**
     * 设置随机文件名
     *
     * @return string
     */
    private function proRandName()
    {
        $fileName = date('YmdHis') . "_" . rand(100, 999);
        return $fileName . '.' . $this->fileType;
    }

    /**
     * 复制上传文件到指定的位置
     *
     * @return bool
     */
    private function copyFile()
    {
        if (!$this->errorNum) {
            $path = rtrim($this->path, '/') . '/';
            $path .= $this->newFileName;
            //if (@move_uploaded_file($this->tmpFileName, $path)) {
            if (move_uploaded_file($this->tmpFileName, $path)) {
                return true;
            } else {
                $this->setOption('errorNum', -3);
                return false;
            }
        } else {
            return false;
        }
    }
}