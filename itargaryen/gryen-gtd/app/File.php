<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\File.
 *
 * @mixin \Eloquent
 */
class File extends Model
{
    protected $fillable = [
        'table',
        'id_in_table',
        'url',
        'status',
    ];

    private static $UPLOAD_PATH = '/uploads';

    /* 允许上传的文件类型 */
    private static $ALLOW_FILE_TYPE = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];

    /* 图片类型 */
    private static $IMAGES = [
        '.jpg',
        '.jpeg',
        '.png',
        '.gif',
    ];

    /* 服务器端的文件路径 */
    private static $FILE_RETURN_PATH = [
        'local' => 'filesystems.disks.local.root',
        'public' => 'filesystems.disks.public.root',
        's3' => 'filesystems.disks.s3.region'.'/'.'filesystems.disks.s3.bucket',
        'qiniu' => 'filesystems.disks.qiniu.domains.https',
    ];

    /**
     * 是否是图片.
     * @param $filePath
     * @return bool
     * @internal param $file
     */
    private static function isImage($filePath)
    {
        $fileType = strtolower(strstr($filePath, '.'));
        if (in_array($fileType, self::$IMAGES)) {
            return $filePath;
        } else {
            return false;
        }
    }

    /**
     * 是否是 Upload 目录下的文件.
     * @param $filePath
     * @return bool
     */
    private static function isUploadsDirectory($filePath)
    {
        return substr($filePath, 0, 7) === substr(self::$UPLOAD_PATH, 1, 7);
    }

    /**
     * 处理返回信息，兼容 simditor.
     *
     * @param string $file_path
     * @param string $msg
     * @param bool $success
     * @return array
     */
    private static function returnResults($file_path = '', $msg = 'success!', $success = true)
    {
        return [
            'file_path' => $file_path,
            'msg' => $msg,
            'success' => $success,
        ];
    }

    /**
     * 上传图片.
     * @param \CURLFile $File
     * @return array
     * @internal param bool $fromServer
     * @internal param Request $request
     */
    public static function upload($File = null)
    {
        $Disk = \Storage::disk(env('DISK'));

        if (! $File || ! $File->getMimeType() || ! in_array($File->getMimeType(), self::$ALLOW_FILE_TYPE)) {
            return self::returnResults(null, 'error! no file or file type error', false);
        }

        $fileType = strtolower($File->getClientOriginalExtension());
        $filePath = self::$UPLOAD_PATH.'/'.Carbon::now()->toDateString().'/'.md5(Carbon::now()->timestamp).'.'.$fileType;

        $results = $Disk->put($filePath, file_get_contents($File));

        if ($results) {
            return self::returnResults(env('STATIC_URL').$filePath);
        } else {
            return self::returnResults(null, 'error!', false);
        }
    }

    /**
     * 服务端已存在的文件上传到云存储.
     * @param $fileSavePath
     * @param $fileName
     * @return array
     */
    public static function uploadSrvFile($fileSavePath, $fileName)
    {
        $filePath = self::$UPLOAD_PATH.'/'.Carbon::now()->toDateString().'/'.md5(Carbon::now()->timestamp).'/'.$fileName;
        $Disk = \Storage::disk(env('DISK'));
        $results = $Disk->put($filePath, file_get_contents($fileSavePath));

        if ($results) {
            return self::returnResults('//'.\Config::get(self::$FILE_RETURN_PATH[env('DISK')]).$filePath);
        } else {
            return self::returnResults(null, 'error!', false);
        }
    }

    /**
     * 取得上传目录下所有图片文件.
     * @param string $directory
     * @param int $page
     * @return array
     * @internal param bool $recursive
     */
    public function listImages($directory = '', $page = 1)
    {
        $perPageNum = 12;
        $from = ($page - 1) * $perPageNum;

        $Disk = \Storage::disk(env('DISK'));
        $files = $Disk->files($directory, true);

        foreach ($files as $key => &$file) {
            if (self::isImage($file) && self::isUploadsDirectory($file)) {
                $file = '//'.\Config::get(self::$FILE_RETURN_PATH[env('DISK')]).'/'.$file;
            } else {
                unset($files[$key]);
            }
        }

        return array_slice(array_reverse($files), $from, $perPageNum);
    }
}
