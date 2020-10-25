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
use Tang\ThirdParty\ThirdPartyService;

/**
 * 又拍云存储
 * Class UpYun
 * @package Tang\Storage\Drivers
 */
class UpYun extends Base implements IStorage
{
    protected $name = 'upYun';
    public function __construct()
    {
        ThirdPartyService::getService()->import('UpYun.upyun','.class.php');
    }

    /**
     * 上传文件
     * @param $localFile
     * @param $remoteFile
     * @throws \Tang\Exception\SystemException
     * @return array
     */
    public function putFile($localFile,$remoteFile)
    {
        $localFp = $this->getFileHandle($localFile);
        $result = $this->write($remoteFile,$localFp);
        fclose($localFp);
        return $result;
    }

    /**
     * 写入文件
     * @param $file
     * @param $content
     * @return array
     * @throws \Tang\Exception\SystemException
     */
    public function write($file,&$content)
    {
        $file = '/'.$this->trim($file);
        try
        {
            $this->getUpYunInstance()->writeFile('/'.$this->trim($file),$content,true);
        } catch(\Exception $e)
        {
            throw new SystemException($e->getMessage(),null,$e->getCode());
            //log
        }
        return $this->getFileStruct($file);
    }

    /**
     * 读取文件
     * @param $file
     * @return string
     * @throws \Tang\Exception\SystemException
     */
    public function read($file)
    {
        try
        {
            return $this->getUpYunInstance()->readFile('/'.$this->trim($file));
        } catch(\Exception $e)
        {
            throw new SystemException($e->getMessage(),null,$e->getCode());
        }
    }

    /**
     * 移动文件
     * @param $srcFile
     * @param $destFile
     * @return array
     */
    public function move($srcFile,$destFile)
    {
        $struct = $this->copy($srcFile,$destFile);
        $this->delete($srcFile);
        return $struct;
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
        $content = $this->read($srcFile);
        $this->write($destFile,$content);
        return $this->getFileStruct($destFile);
    }

    /**
     * 删除文件
     * @param $file
     * @throws \Tang\Exception\SystemException
     */
    public function delete($file)
    {
        try
        {
            $this->getUpYunInstance()->delete('/'.$this->trim($file));
        } catch(\Exception $e)
        {
            throw new SystemException($e->getMessage(),null,$e->getCode());
        }
    }

    public function getUrl($file)
    {
        return 'http://'.$this->bucket['domain'].'/'.$this->trim($file);
    }
    /**
     * 获取又拍云SDK实例
     * @return \UpYun
     */
    public function getUpYunInstance()
    {
        if(!isset($this->bucket['instance']) || !$this->bucket['instance'] instanceof \UpYun)
        {
            $this->bucket['instance'] = new \UpYun($this->bucket['bucket'],$this->bucket['user'],$this->bucket['password']);
        }
        return $this->bucket['instance'];
    }
    protected function checkBucket($name,$bucket)
    {
        if(!isset($bucket['domain'])||!$bucket['domain'])
        {
            $bucket['domain'] = $name.'b0.upaiyun.com';
        }
        !isset($bucket['user']) || !$bucket['user'] ? $bucket['user'] = (!isset($this->config['user']) || !$this->config['user'] ? '':$this->config['user']):'';
        !isset($bucket['password']) || !$bucket['password'] ? $bucket['password'] = (!isset($this->config['password']) || !$this->config['password'] ? '':$this->config['password']):'';
        if(!$bucket['user'] || !$bucket['password'])
        {
            throw new SystemException('UpYun driver [%s] Bucket no user or password!',$name);
        }
        return $bucket;
    }
}