<?php

namespace App\Models;

use App\Uploaders\NiupicUploader;
use App\Uploaders\SinaUploader;
use App\Uploaders\SmmsUploader;
use Illuminate\Database\Eloquent\Model;

class ImageStorage extends Model
{
    const SMMS = 1;
    const SINA = 2;
    const NIUPIC = 3;

    protected static $uploaders = [
        self::SINA => SinaUploader::class,
        self::SMMS => SmmsUploader::class,
        self::NIUPIC => NiupicUploader::class,
    ];

    public static function preferOrder()
    {
        return config('app.storage_order', implode(', ', array_keys(static::$uploaders)));
    }

    public static function count()
    {
        return count(static::$uploaders);
    }

    public static function getUploaders()
    {
        return static::$uploaders;
    }

    public static function getUploader($id)
    {
        return isset(static::$uploaders[$id]) ? new static::$uploaders[$id]() : false;
    }

    public static function upload($file, $storage = self::SMMS)
    {
        if ($uploader = static::getUploader($storage)) {
            try {
                return $uploader->upload($file);
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }
}
