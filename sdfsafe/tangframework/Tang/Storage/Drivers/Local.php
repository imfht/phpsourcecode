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
use Tang\IO\Interfaces\IFile;
use Tang\TangApplication;

/**
 * 本地文件存储
 * Class Local
 * @package Tang\Storage\Drivers
 */
class Local extends Base implements IStorage
{
    protected $name = 'local';
    /**
     * @var IFile
     */
    protected $fileInstance;
    public function __construct(IFile $fileInstance)
    {
        $this->fileInstance = $fileInstance;
    }
    /**
     * 上传文件
     * @param $localFile
     * @param $remoteFile
     * @return array
     */
    public function putFile($localFile,$remoteFile)
    {
        $this->fileInstance->copy($localFile,$this->getAbsolutePath($remoteFile));
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
        $this->fileInstance->write($this->getAbsolutePath($file),$content);
        return $this->getFileStruct($file);
    }

    /**
     * 读取文件
     * @param $file
     * @return string
     */
    public function read($file)
    {
        $content = '';
        $this->fileInstance->read($this->getAbsolutePath($file),$content);
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
        $fileStruct = $this->putFile($srcFile,$destFile);
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
        return $this->putFile($srcFile,$destFile);
    }

    /**
     * 删除文件
     * @param $file
     */
    public function delete($file)
    {
        $this->fileInstance->delete($this->getAbsolutePath($file));
    }

    /**
     * 获取外部访问地址
     * @param $file
     * @return string
     */
    public function getUrl($file)
    {
        return $this->bucket['path'].$this->trim($file);
    }

    protected function checkBucket($name,$bucket)
    {
        if(!isset($bucket['directory']))
        {
            $bucket['directory'] = $name;
        } else
        {
            $bucket['directory'] = $this->trim($bucket['directory']);
        }
        $bucket['path'] = '/'.$bucket['directory'].'/';
        $bucket['directory'] = TangApplication::getApplicationPath().$bucket['directory'].DIRECTORY_SEPARATOR;
        return $bucket;
    }
}