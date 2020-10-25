<?php
class UploadPolicy{
	/*如下属性是必须的[The following attributes are required]*/
    public $namespace;                              // 多媒体服务的空间名[media namespace name]
    public $bucket;                                 // OSS的空间名[media bucket name]
    public $insertOnly;                             // 是否可覆盖[upload mode. it's not allowd uploading the same name files]
    public $expiration;                             // 过期时间[expiration time, unix time, in milliseconds]

    /*如下属性是可选的[The following attributes are optional]*/
    public $detectMime;                             // 是否进行类型检测[is auto detecte media file mime type, default is true]
    public $dir;                                    // 路径[media file dir, magic vars and custom vars are supported]
    public $name;                                   // 上传到服务端的文件名[media file name, magic vars and custom vars are supported]
    public $sizeLimit;                              // 文件大小限制[upload size limited, in bytes]
    public $mimeLimit;                              // 文件类型限制[upload mime type limited]
    public $callbackUrl;                            // 回调URL [callback urls, ip address is recommended]
    public $callbackHost;                           // 回调时Host [callback host]
    public $callbackBody;                           // 回调时Body [callback body, magic vars and custom vars are supported]
    public $callbackBodyType;                       // 回调时Body类型 [callback body type, default is 'application/x-www-form-urlencoded; charset=utf-8']
    public $returnUrl;                              // 上传完成之后,303跳转的Url [return url, when return code is 303]
    public $returnBody;                             // 上传完成返回体 [return body, magic vars and custom vars are supported]
    /**UploadPolicy的构造函数，必须设置空间名namespace才能创建对象*/
    public function __construct( $namespace ) {
    	$this->namespace = $namespace;
    	$this->insertOnly = Conf::INSERT_ONLY_NONE;
    	$this->expiration = -1;
    	$this->detectMime = Conf::DETECT_MIME_TRUE;
    }
}
