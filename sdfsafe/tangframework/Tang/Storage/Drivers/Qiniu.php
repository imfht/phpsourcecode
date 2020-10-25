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
use Tang\Util\WebClient;

/**
 * 七牛云存储驱动
 * Class Qiniu
 * @package Tang\Storage\Drivers
 */
class Qiniu extends Base implements IStorage
{
    protected $name = 'qiniu';
    protected $httpClient;
    public function __construct()
    {
        ThirdPartyService::getService()->import('Qiniu.rs');
        ThirdPartyService::getService()->import('Qiniu.io');
    }

    public function setBucket($bucket)
    {
        parent::setBucket($bucket);
        Qiniu_SetKeys($this->bucket['accessKey'],$this->bucket['secretKey']);
    }

    public function delete($file)
    {
        $err = Qiniu_RS_Delete($this->getHttpClient(),$this->bucket['bucket'],$file);
        if ($err !== null) {
            var_dump($err);
        } else {
            echo "Success!";
        }
    }
    public function move($srcFile,$destFile)
    {
        $destFile = $this->trim($destFile);
        $srcFile = $this->trim($srcFile);
        $err = Qiniu_RS_Move($this->getHttpClient(),$this->bucket['bucket'],'/'.$srcFile,$this->bucket['bucket'],'/'.$destFile);
        if ($err !== null)
        {
            var_dump($err);
        } else
        {
            return $this->getFileStruct($destFile);
        }
    }
    public function copy($srcFile,$destFile)
    {
        $destFile = $this->trim($destFile);
        $srcFile = $this->trim($srcFile);
        $err = Qiniu_RS_Copy($this->getHttpClient(),$this->bucket['bucket'],'/'.$srcFile,$this->bucket['bucket'],'/'.$destFile);
        if ($err !== null)
        {
            var_dump($err);
        } else
        {
            return $this->getFileStruct($destFile);
        }
    }

    /**
     * @param $file
     * @return string
     */
    public function read($file)
    {
        $url = $this->getUrl($file);
        $webClient = new WebClient();
        return $webClient->downloadString($url);
    }
    public function write($file,&$content)
    {
        $file = $this->trim($file);
        $putPolicy = new \Qiniu_RS_PutPolicy($this->bucket['bucket']);
        $upToken = $putPolicy->Token(null);
        list($ret, $err) = Qiniu_Put($upToken,'/'.$file,$content, null);
        if ($err !== null) {
            var_dump($err);
        } else
        {
            return $this->getFileStruct($file);
        }
    }
    public function putFile($localFile,$remoteFile)
    {
        $remoteFile = $this->trim($remoteFile);
        $putPolicy = new \Qiniu_RS_PutPolicy($this->bucket['bucket']);
        $upToken = $putPolicy->Token(null);
        $putExtra = new \Qiniu_PutExtra();
        $putExtra->Crc32 = 1;
        list($ret, $err) = \Qiniu_PutFile($upToken,'/'.$remoteFile,$localFile, $putExtra);
        if ($err !== null)
        {
            var_dump($err);
        } else
        {
            return $this->getFileStruct($remoteFile);
        }
    }
    public function getUrl($file)
    {
        $file = $this->trim($file);
        $url = Qiniu_RS_MakeBaseUrl($this->bucket['domain'],'/'.$file);
        if($this->bucket['isPrivate'])
        {
            $getPolicy = new \Qiniu_RS_GetPolicy();
            $url = $getPolicy->MakeRequest($url, null);
        }
        return $url;
    }
    public function getHttpClient()
    {
        !$this->httpClient && $this->httpClient = new \Qiniu_MacHttpClient(null);
        return $this->httpClient;
    }
    protected function checkBucket($name,$bucket)
    {
        if(!isset($bucket['domain'])||!$bucket['domain'])
        {
            $bucket['domain'] = $name.'.qiniudn.com';
        }
        if(!isset($bucket['isPrivate']))
        {
            $bucket['isPrivate'] = false;
        }
        !isset($bucket['accessKey']) || !$bucket['accessKey'] ? $bucket['accessKey'] = (!isset($this->config['accessKey']) || !$this->config['accessKey'] ? '':$this->config['accessKey']):'';
        !isset($bucket['secretKey']) || !$bucket['secretKey'] ? $bucket['secretKey'] = (!isset($this->config['secretKey']) || !$this->config['secretKey'] ? '':$this->config['secretKey']):'';
        if(!$bucket['accessKey'] || !$bucket['secretKey'])
        {
            throw new SystemException('QiNiu driver [%s] Bucket no accessKey or secretKey!',$name);
        }
        return $bucket;
    }
}