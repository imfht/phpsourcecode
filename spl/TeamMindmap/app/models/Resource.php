<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-28
 * Time: 下午4:47
 */

use \Symfony\Component\HttpFoundation\BinaryFileResponse;
use \Symfony\Component\HttpFoundation\ResponseHeaderBag;
/**
 * 分享附带的资源模型
 * Class SharingResource
 */
class Resource extends Eloquent
{
    /**
     * 返回资源在服务器上存放的真实路径.
     *
     * @param Resource $res
     * @return string
     */
    public static function getResourceRealPath(Resource $res)
    {
        return static::getResourceDirectory(). $res['filename'];
    }

    /**
     * 返回资源在服务器上存放的真实文件夹路径.
     * @return string
     */
    public static function getResourceDirectory()
    {
        return public_path(). '/resources/';
    }

    /**
     * 生成相应资源的下载响应.
     *
     * @param Resource $res
     * @param string $disposition
     * @return BinaryFileResponse
     */
    public static function makeDownloadResponse(Resource $res, $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT)
    {
        $fileSrc = static::getResourceRealPath($res);

        $resp = new BinaryFileResponse($fileSrc);
        $resp->setContentDisposition($disposition, $res['origin_name'], 'download.'. $res['ext_name']);

        return $resp;

    }

    protected $guarded = ['id'];

    protected $table = 'resources';
}