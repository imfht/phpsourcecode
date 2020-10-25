<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Storage\Drivers;
use Tang\Exception\SystemException;

/**
 * Ftp存储
 * Class Ftp
 * @package Tang\Storage\Drivers
 */
class Ftp extends Base implements IStorage
{
    protected $name = 'ftp';
    /**
     * 上传文件
     * @param $localFile
     * @param $remoteFile
     * @throws \Tang\Exception\SystemException
     * @return array
     */
    public function putFile($localFile,$remoteFile)
    {
        $this->delete($remoteFile);
        $connect = $this->getConnect();
        $remoteAbsolutePath = $this->getAbsolutePath($remoteFile);
        $remoteAbsolutePath = $this->trim($remoteAbsolutePath);
        $this->mkDirectory(dirname($remoteAbsolutePath));
        if(!is_resource($localFile))
        {
            $localFile = fopen($localFile,'r');
        }
        $ret = ftp_nb_fput($connect,$remoteAbsolutePath,$localFile,FTP_BINARY);
        while ($ret == FTP_MOREDATA)
        {
            $ret = ftp_nb_continue($connect);
        }
        if ($ret != FTP_FINISHED)
        {
            throw new SystemException('Error');
        }
        fclose($localFile);
        return $this->getFileStruct($remoteFile);
    }

    /**
     * 写入文件
     * @param $file
     * @param $content
     * @return array
     */
    public function write($file,&$content)
    {
        $fp = fopen('php://temp','w');
        fwrite($fp,$content);
        rewind($fp);
        return $this->putFile($fp,$file);
    }
    /**
     * 读取文件
     * @param $file
     * @return string
     */
    public function read($file)
    {
        $tempFp = $this->createTempHandle($file);
        $content = stream_get_contents($tempFp);
        fclose($tempFp);
        return $content;
    }

    /**
     * 移动文件
     * @param $srcFile
     * @param $destFile
     * @return array
     */
    public function move($srcFile,$destFile)
    {
        $fileStruct = $this->copy($srcFile,$destFile);
        $this->delete($srcFile);
        return $fileStruct;
    }

    /**
     * 复制文件
     * @param $srcFile
     * @param $destFile
     * @return array
     */
    public function copy($srcFile,$destFile)
    {
        //先下载文件
        $tempFp = $this->createTempHandle($srcFile);
        return $this->putFile($tempFp,$destFile);
    }

    /**
     * 删除文件
     * @param $file
     */
    public function delete($file)
    {
        @ftp_delete($this->getConnect(),$this->trim($this->getAbsolutePath($file)));
    }

    /**
     * 创建文件夹
     * @param $directory
     * @return bool
     */
    public function mkDirectory($directory)
    {
        $connect = $this->getConnect();
        if(@ftp_chdir($connect,$directory))
        {
            ftp_chdir($connect,$this->bucket['home']);
            return true;
        }
        $parts = explode('/',$directory);
        $ret = true;
        foreach($parts as $part)
        {
            if(!@ftp_chdir($connect,$part))
            {
                ftp_mkdir($connect,$part);
                ftp_chdir($connect,$part);
            }
        }
        ftp_chdir($connect,$this->bucket['home']);
        return $ret;
    }

    /**
     * 获取外部访问地址
     * @param $file
     * @return string
     */
    public function getUrl($file)
    {
        return 'http://'.$this->bucket['domain'].$this->bucket['path'].$this->trim($file);
    }

    /**
     * 创建临时文件句柄
     * @param $file
     * @return resource
     * @throws \Tang\Exception\SystemException
     */
    protected function createTempHandle($file)
    {
        $connect = $this->getConnect();
        $tempFp = fopen('php://temp', 'r+');
        $ret = @ftp_nb_fget($connect,$tempFp,$this->trim($this->getAbsolutePath($file)),FTP_BINARY);
        while ($ret == FTP_MOREDATA)
        {
            $ret = ftp_nb_continue($connect);
        }
        if ($ret != FTP_FINISHED)
        {
            throw new SystemException('FTP read [%s] file failed, there may be a file does not exist',array($file));
        }
        rewind($tempFp);
        return $tempFp;
    }

    /**
     * 判断bucket
     * @param $name
     * @param $bucket
     * @return array
     * @throws \Tang\Exception\SystemException
     */
    protected function checkBucket($name,$bucket)
    {
        $array = array($name);
        $bucket = array_replace_recursive(array('user'=>'root','password' => '','port' => 21,'path' => ''),$bucket);
        if(!isset($bucket['domain']) || !$bucket['domain'])
        {
            throw new SystemException('FTP driver [%s] bucket is not set access domain!',$array);
        }
        if(!isset($bucket['user']) || !$bucket['user'])
        {
            throw new SystemException('FTP driver [%s] bucket is not set access domain!',$array);
        }
        if(!isset($bucket['directory']) || !$bucket['directory'])
        {
            throw new SystemException('FTP drive the root directory [%s] bucket is not set to upload!',$array);
        }
        $bucket['directory'] = $this->trim($bucket['directory']);
        $bucket['directory'] = '/'.$bucket['directory'].'/';
        if(!$bucket['path'])
        {
            $bucket['path'] = '/';
        } else
        {
            $bucket['path'] = $this->trim($bucket['path']);
            $bucket['path'] = '/'.$bucket['path'].'/';
        }
        return $bucket;
    }

    protected function getConnect()
    {
        if(!isset($this->bucket['connect']) || !is_resource($this->bucket['connect']))
        {
            $connect = ftp_connect($this->bucket['domain'],$this->bucket['port']);
            if(!$connect)
            {
                throw new SystemException('Couldn\'t connect to [%s:%d]',array($this->bucket['domain'],$this->bucket['port']));
            }
            if (ftp_login($connect,$this->bucket['user'],$this->bucket['password']))
            {
                $this->bucket['connect'] = $connect;
            } else
            {
                throw new SystemException('Couldn\'t connect as [%s]@[%s:%d]',array($this->bucket['user'],$this->bucket['domain'],$this->bucket['port']));
            }
            ftp_pasv($connect,true);
            $this->bucket['home'] = ftp_pwd($connect);
        }
        return $this->bucket['connect'];
    }
    public function __destruct()
    {
        foreach($this->buckets as $bucket)
        {
            if(isset($bucket['connect']) && $bucket['connect'])
            {
                ftp_close($bucket['connect']);
            }
        }
    }
}