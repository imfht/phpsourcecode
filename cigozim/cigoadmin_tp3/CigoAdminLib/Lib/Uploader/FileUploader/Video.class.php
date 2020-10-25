<?php

namespace CigoAdminLib\Lib\Uploader\FileUploader;

use CigoAdminLib\Lib\Uploader\FileUploader;
use CigoAdminLib\Lib\Uploader\Uploader;


/**
 * 视频上传接口
 */
class Video extends FileUploader
{
    protected function getConfigFileLimit($configs)
    {
        return $configs['fileLimit']['video'];
    }

    protected function getFileType()
    {
        return Uploader::FILE_TYPE_VIDEO;
    }
}

