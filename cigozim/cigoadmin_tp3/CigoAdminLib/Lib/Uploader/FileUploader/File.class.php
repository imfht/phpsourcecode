<?php

namespace CigoAdminLib\Lib\Uploader\FileUploader;

use CigoAdminLib\Lib\Uploader\FileUploader;
use CigoAdminLib\Lib\Uploader\Uploader;

/**
 * 文件上传接口
 */
class File extends FileUploader
{

    protected function getConfigFileLimit($configs)
    {
        return $configs['fileLimit']['file'];
    }

    protected function getFileType()
    {
        return Uploader::FILE_TYPE_FILE;
    }
}

