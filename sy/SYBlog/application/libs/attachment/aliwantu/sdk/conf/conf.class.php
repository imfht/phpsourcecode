<?php

class Conf{
    const CHARSET = "UTF-8";
	const SDK_VERSION = '2.2.0';
	
    const UPLOAD_HOST_MEDIA = "http://upload.media.aliyun.com";		//文件上传的地址
    const MANAGE_HOST_MEDIA = "http://rs.media.aliyun.com";			//服务管理的地址
    const MANAGE_API_VERSION = "3.0";		//资源管理接口版本
    const SCAN_PORN_VERSION = "3.1";		//黄图扫描接口版本
    const MEDIA_ENCODE_VERSION = "3.0";		//媒体转码接口版本
    
    const UPLOAD_API_UPLOAD = "/api/proxy/upload.json";
    const UPLOAD_API_BLOCK_INIT = "/api/proxy/blockInit.json";
    const UPLOAD_API_BLOCK_UPLOAD = "/api/proxy/blockUpload.json";
    const UPLOAD_API_BLOCK_COMPLETE = "/api/proxy/blockComplete.json";
    const UPLOAD_API_BLOCK_CANCEL = "/api/proxy/blockCancel.json";
    
    const TYPE_TOP = "TOP";
    const TYPE_CLOUD = "CLOUD";
    
    const DETECT_MIME_TRUE = 1;			//检测MimeType
    const DETECT_MIME_NONE = 0;			//不检测MimeType
    const INSERT_ONLY_TRUE = 1;			//文件上传不可覆盖
    const INSERT_ONLY_NONE = 0;			//文件上传可覆盖
    
    const MIN_OBJ_SIZE = 102400;		//1024*100;
    const HTTP_TIMEOUT = 30;			//http的超时时间：30s
    
    const BLOCK_MIN_SIZE = 102400;		//文件分片最小值：1024*100; 100K
    const BLOCK_DEFF_SIZE = 2097152;	//文件分片默认值：1024*1024*2; 2M
    const BLOCK_MAX_SIZE = 10485760;	//文件分片最大值：1024*1024*10; 10M
}
