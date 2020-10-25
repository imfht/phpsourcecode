<?php

require_once 'vendor/autoload.php';
require_once 'OldLite.php';

use OSS\OssClient;
use OSS\Core\OssException;

class OSS_Lite{

    private $ossClient;
    private $runmode;

    function __construct() {
        $accessKeyId = DI()->config->get('sys.OSS_ACCESS_ID');
        $accessKeySecret = DI()->config->get('sys.OSS_ACCESS_KEY');
        $endpoint = DI()->config->get('sys.OSS_URL');
        $this->ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $this->runmode = DI()->config->getrunmode();
    }

    /**
     * 上传本地文件
     * @param      $bucket
     * @param      $object oss存放地址
     * @param      $file   本地地址
     * @param null $options
     */
    function upload_file_by_file($bucket, $object, $file, $options = NULL){
            $obj = new StdClass();
            try {
                $this->ossClient->uploadFile($bucket, $object, $file, $options);
                $obj->status = 200;
            } catch (OssException $e) {
                $obj->status = 100;
            }
            return $obj;
    }

    /**
     * 纯粹为不用改动老的调用代码而加的方法
     * @param $flag boolean
     */
    function set_debug_mode($flag){}
}

