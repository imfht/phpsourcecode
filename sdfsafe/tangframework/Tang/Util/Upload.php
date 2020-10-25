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
namespace Tang\Util;
use Tang\Exception\SystemException;
use Tang\Services\FileService;
use Tang\Storage\Drivers\IStorage;

/**
 * 文件上传组件
 * Class Upload
 * @package Tang\Util
 */
class Upload
{
    /**
     * 允许上传的文件后缀
     * @var array
     */
    private $allowExts= array('jpg','jpeg','gif','png','bmp');
    /**
     * 是否安全类型检查
     * @var boolean
     */
    private $allowSafeCheck = true;
    /**
     * 文件后缀名
     * @var string
     */
    private $ext = '';
    /**
     * 上传最大大小 (K为单位 1M=1024K)
     * @var int
     */
    private $maxSize = 1024;
    /**
     * 存储引擎
     * @var IStorage
     */
    protected $storage;

    /**
     * @param IStorage $storage 存储引擎
     * @param int $maxSize 最大尺寸
     * @param bool $allowSafeCheck 是否允许安全检查
     * @param array $exts 允许上传的后缀
     */
    public function __construct(IStorage $storage,$maxSize = 0,$allowSafeCheck = true,array $exts=array())
    {
        $this->setStorage($storage)
               ->setMaxSize($maxSize)->setAllowSafe($allowSafeCheck);
        if($exts)
        {
            $this->setAllowExts($exts);
        }
    }
    /**
     * 设置上传的最大KB
     * 为0的时候则设置为无限大小
     * @param int $maxSize
     * @return $this
     */
    public function setMaxSize($maxSize = 0)
    {
        $maxSize = (int)$maxSize;
        $this->maxSize = $maxSize > 0 ? $maxSize : 0;
        return $this;
    }
    /**
     * 设置允许上传的文件后缀数组
     * @param array $exts
     * @return $this
     */
    public function setAllowExts(array $exts)
    {
        $this->allowExts = $exts;
        return $this;
    }
    /**
     * 设置是否允许安全类型检查
     * @param boolean $allowSafeCheck
     * @return $this
     */
    public function setAllowSafe($allowSafeCheck = true)
    {
        $this->allowSafeCheck = $allowSafeCheck;
        return $this;
    }
    /**
     * 设置存储引擎
     * @param IStorage $storage
     * @return $this
     */
    public function setStorage(IStorage $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * 如果file控件里面类似于file[]这样的数组信息时候，如果需要用自己定义的地址的时候。
     * $fileName则需要传递一个数组对应上传的文件
     * @param string $filed 上传的字段名 file控件里面的name
     * @param mixed $fileName 生成的文件名 不填写的话为系统自动生成文件地址
     * @return array
     * @throws NotFoundFileUploadException
     * @throws UploadErrorException
     * @throws UploadSafeCheckException
     */
    public function move($filed,$fileName=null)
    {
        if(!isset($_FILES[$filed]))
        {
            throw new NotFoundFileUploadException($filed);
        }
        $uploadFile = $_FILES[$filed];
        if(is_array($uploadFile['name']))
        {
            $fileStructs = array();
            if(!is_array($fileName))
            {
                $fileName = array();
            }
            foreach($uploadFile['name'] as $key => $value)
            {
                $fileStructs[$key] = $this->uploadHandle(array('name' => $value,'type' => $uploadFile['type'][$key],'tmp_name' => $uploadFile['tmp_name'][$key],'error' => $uploadFile['error'][$key],'size' => $uploadFile['size'][$key]),isset($fileName[$key]) && $fileName[$key] ? $fileName[$key]:'');
            }
            return $fileStructs;
        } else
        {
            return $this->uploadHandle($uploadFile,$fileName);
        }
    }
    private function uploadHandle($uploadFile,$fileName='')
    {
        if($uploadFile['error'] > 0)
        {
            throw new UploadErrorException('File upload error '.$uploadFile['error'],null,50001+$uploadFile['error']);
        } else if(!is_uploaded_file($uploadFile['tmp_name']))
        {
            throw new UploadErrorException('Illegal file upload!',null,50010);
        }
        //判断后缀
        $this->ext = FileService::getService()->getExtension($uploadFile['name']);
        if($this->allowExts && !in_array($this->ext, $this->allowExts))
        {

            throw new UploadSafeCheckException('Only allowed to upload the [%s] file!',array(implode(',', $this->allowExts)),50001);
        }
        if($this->maxSize > 0)
        {
            $maxSize = 1024 * $this->maxSize;
            if($uploadFile['size'] > $maxSize )
            {
                throw new UploadErrorException('Upload file size is [%d byte], is greater than the set of [%d byte]!',array($uploadFile['size'],$maxSize),50000);
            }
        }
        //安全类型检查
        if($this->allowExts && in_array($this->ext, array('jpeg','jpg','gif','png','bmp')) && !$this->picScan($uploadFile['tmp_name']))
        {
            throw new UploadSafeCheckException('The uploaded file is not a [%s] file!',array($this->ext),50011);
        }
        !$fileName && $fileName = $this->createFilePath();
        $fileStruct = $this->storage->putFile($uploadFile['tmp_name'],$fileName);
        $fileStruct['name'] = $uploadFile['name'];
        return $fileStruct;
    }
    /**
     * 检查文件是否是一个图片
     * @param string $fileName
     * @return boolean
     */
    private function picScan($fileName)
    {
        $tempImageSize = @getimagesize($fileName);
        list($tempWidth, $tempHeight, $tempType) = (array)$tempImageSize;
        $tempSize = $tempWidth * $tempHeight;
        if($tempSize > 16777216 || $tempSize < 4 || empty($tempType) || strpos($tempImageSize['mime'], 'flash') > 0)
        {
            return false;
        }
        return true;
    }
    /**
     * 构建上传路径
     */
    private function createFilePath()
    {
        return date('ym/d/').sha1(uniqid(mt_rand(),1)).'.'.$this->ext;
    }
}

class NotFoundFileUploadException extends SystemException
{
    public function __construct($field)
    {
        parent::__construct('No [%s] file upload!',$field,635);
    }
}
class UploadErrorException extends SystemException
{
}
class UploadSafeCheckException extends SystemException
{

}