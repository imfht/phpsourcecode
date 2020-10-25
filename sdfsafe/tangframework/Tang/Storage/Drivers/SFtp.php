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
 * SFTP存储
 * Class SFtp
 * @package Tang\Storage\Drivers
 */
class SFtp extends Base implements IStorage
{
    protected $name = 'sFtp';
    /**
     * 上传文件
     * 本来想读取本地文件后然后使用write方法写入的
     * 后来考虑到大文件 就放弃了。改重写方法
     * @param $localFile
     * @param $remoteFile
     * @throws \Tang\Exception\SystemException
     * @return array
     */
    public function putFile($localFile,$remoteFile)
    {
        if(!file_exists($localFile))
        {
            throw new SystemException('Could not find local file: [%s]',array($localFile));
        }
        $sftp = $this->getSftp();
        $remoteAbsolutePath = $this->getAbsolutePath($remoteFile);
        ssh2_sftp_mkdir ($sftp,dirname($remoteAbsolutePath),0777,true);
        $remoteFp = $this->createFileHandle($sftp,$remoteAbsolutePath);
        $localFp = fopen($localFile,'r');
        $buffer = 1024;
        $content = '';
        while(!feof($localFp))
        {
            $content = fread($localFp,$buffer);
            fwrite($remoteFp,$content);
        }
        fclose($localFp);
        fclose($remoteFp);
        return $this->getFileStruct($remoteFile);
    }

    /**
     * 删除文件
     * @param $file
     */
    public function delete($file)
    {
        $sftp = $this->getSftp();
        ssh2_sftp_unlink($sftp,$this->bucket['directory'].$file);
    }
    /**
     * 移动文件
     * @param $srcFile
     * @param $destFile
     * @return array
     */
    public function move($srcFile,$destFile)
    {
        return $this->copyOrMove($srcFile,$destFile,false);
    }

    /**
     * 复制文件
     * @param $srcFile
     * @param $destFile
     * @return array
     */
    public function copy($srcFile,$destFile)
    {
        return $this->copyOrMove($srcFile,$destFile);
    }

    /**
     * 读取文件
     * @param $file
     * @return string
     */
    public function read($file)
    {
        $sftp = $this->getSftp();
        $file = $this->getAbsolutePath($file);
        $fp = $this->createFileHandle($sftp,$file);
        $buffer = 1024;
        $content = '';
        while(!feof($fp))
        {
            $content .= fread($fp,$buffer);
        }
        fclose($fp);
        return $content;
    }

    /**
     * 写入文件
     * @param $file
     * @param $content
     * @throws Exception
     * @return array
     */
    public function write($file,&$content)
    {
        $sftp = $this->getSftp();
        $fileAbsolutePath = $this->getAbsolutePath($file);
        ssh2_sftp_mkdir ($sftp,dirname($fileAbsolutePath),0777,true);
        $fp = $this->createFileHandle($sftp,$fileAbsolutePath);
        if(fwrite($fp,$content) === false)
        {
            fclose($fp);
            throw new Exception("Could not send data from file: $content.");
        }
        fclose($fp);
        return $this->getFileStruct($file);
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
     * 复制或移动文件
     * @param $srcFile
     * @param $destFile
     * @param bool $isCopy
     * @return array
     */
    protected function copyOrMove($srcFile,$destFile,$isCopy=true)
    {
        $sftp = $this->getSftp();
        $srcFile = $this->getAbsolutePath($srcFile);
        $destAbsolutePath = $this->getAbsolutePath($destFile);
        $cmd = $isCopy ? 'cp':'mv';
        ssh2_exec($this->bucket['connection'],$cmd.' '.$srcFile.' '.$destAbsolutePath);
        return $this->getFileStruct($destFile);
    }
    /**
     * 创建文件句柄
     * @param $sftp
     * @param $file
     * @return resource
     * @throws \Tang\Exception\SystemException
     */
    protected function createFileHandle($sftp,$file)
    {
        $fp = @fopen('ssh2.sftp://'.$sftp.$file, 'w');
        if (!$fp)
        {
            throw new SystemException('Could not open file: [%s]',array($file));
        }
        return $fp;
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
        $bucket = array_replace_recursive(array('user'=>'root','password' => '','port' => 22,'path' => ''),$bucket);
        if(!isset($bucket['domain']) || !$bucket['domain'])
        {
            throw new SystemException('SFTP driver [%s] bucket is not set access domain!',$array);
        }
        if(!isset($bucket['user']) || !$bucket['user'])
        {
            throw new SystemException('SFTP driver [%s] bucket is not set access domain!',$array);
        }
        if(!isset($bucket['directory']) || !$bucket['directory'])
        {
            throw new SystemException('SFTP drive the root directory [%s] bucket is not set to upload!',$array);
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

    /**
     * 获取sftp链接
     * @return mixed
     * @throws \Tang\Exception\SystemException
     */
    protected function getSftp()
    {
        if(!isset($this->bucket['sftp']))
        {
            $connection = ssh2_connect($this->bucket['domain'],$this->bucket['port']);
            if (!$connection)
            {
                throw new SystemException('Could not connect to [%s] on port [%d].',array($this->bucket['domain'],$this->bucket['port']));
            }
            if (!ssh2_auth_password($connection,$this->bucket['user'],$this->bucket['password']))
            {
                throw new SystemException('Could not authenticate with username [%s],domain [%s]',array($this->bucket['user'],$this->bucket['domain']));
            }
            $sftp = ssh2_sftp($connection);
            if (!$sftp)
            {
                throw new SystemException('Could not initialize SFTP subsystem');
            }
            $this->bucket['sftp'] = $sftp;
            $this->bucket['connection'] = $connection;
        }
        return $this->bucket['sftp'];
    }
}