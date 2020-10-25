<?php
namespace App\Service\Uploader;

/**
 * 上传类接口
 * @package App\Service\Uploader
 */
interface UploaderInterface
{
    /**
     * getToken
     * @param $key
     * @return mixed
     */
    public function getToken($key, $type);
    /**
     * 上传文件
     * @return mixed
     */
    public function upFile($fileField);

    /**
     * 上传base64编码图片文件
     * @return mixed
     */
    public function upBase64($fileField);

    /**
     * 拉取远程图片
     * @return mixed
     */
    public function saveRemote($fileField);

    /**
     * 上传本地文件到远程
     * @param $filePath
     * @return mixed
     */
    public function upByPath($filePath);

    /**
     * 文件列表
     * @return mixed
     */
    public function listFile($type);

}