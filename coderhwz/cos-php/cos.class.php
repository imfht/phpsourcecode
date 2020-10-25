<?php
define('SDK_PATH', dirname(__FILE__));
require_once SDK_PATH.'/lib/CosUtil.php';
define('COS_HOST', "cosapi.myqcloud.com");
define('COS_HOST_INNER', "cosapi.tencentyun.com");
define('COS_DOWNLOAD_HOST', "cos.myqcloud.com");
define('COS_DOWNLOAD_HOST_INNER', "cos.tencentyun.com");
define('COS_W_PRIVATE_R_PRIVATE', 0);
define('COS_W_PRIVATE_R_PUBLIC', 1);


define('COS_ERROR_REQUIRED_PARAMETER_EMPTY', 1001); // 参数为空
define('COS_ERROR_REQUIRED_PARAMETER_INVALID', 1002); // 参数格式错误
define('COS_ERROR_RESPONSE_DATA_INVALID', 1003); // 返回包格式错误
define('COS_ERROR_CURL', 1004); // 网络错误


class Cos_Exception extends Exception {}

/**
 * 如果您的 PHP 没有安装 cURL 扩展，请先安装 
 */
if (!function_exists('curl_init'))
{
    throw new Cos_Exception('OpenAPI needs the cURL PHP extension.');
}

/**
 * 如果您的 PHP 不支持JSON，请升级到 PHP 5.2.x 以上版本
 */
if (!function_exists('json_decode'))
{
    throw new Cos_Exception('OpenAPI needs the JSON PHP extension.');
}


class Cos {

    private $accessId;
    private $accessKey;
    private $secretId;
    private $host;

    /**
     * 构造函数
     *
     * @param string $host      服务器域名，可以设置内网或外网域名
     * @param string $accessId  开发商访问本服务的资源标识，例如 id为1234 下的资源a.jpg ,下载时的地
     *                          址为 http://cos.yun.qq.com/1234/bucket1/a.jpg
     * @param string $accessKey 访问Key, 用户用于签名的密钥。注册时返回，也可以在管理端查询。
     */
    public function __construct($host, $accessId = NULL, $accessKey = NULL, $secretId = NULL){
        $this->host = $host;
        $this->accessId = $accessId? $accessId:COS_ACCESS_ID;
        $this->accessKey = $accessKey? $accessKey:COS_ACCESS_KEY;
        $this->secretId = $secretId?$secretId:"";

        if(empty($this->host)){
            throw new Cos_Exception('Empty host!');
        }
        if(empty($this->accessId)){
            throw new Cos_Exception('Empty accessId');
        }
        if(empty($this->accessKey)){
            throw new Cos_Exception('Empty accessKey');
        }
    }

    /**
     * 上传本地文件
     *
     * @param string $bucketId  桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $path      文件将要存放的目录路径，以"/" 开头
     * @param string $cosFile   存储到COS之后的文件名
     * @param string $localFile 本地文件路径
     * @return array            结果数组
     */
    public function upload_file_by_file( $bucketId, $path, $cosFile, $localFile)
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );

        if(empty( $bucketId ) )
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'bucketId is empty.';
            return $ret;
        }

        if(empty( $localFile ) )
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'localFile is empty.';
            return $ret;
        }

        if(empty( $cosFile ) )
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'cosFile is empty.';
        return $ret;
        }

        if(empty( $path ))
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'Empty path param!';
        return $ret;
        }

        if($path[0] != "/")
        {
        $path = "/".$path;
        }

        $script_name = '/api/cos_upload';
        $params = array(
            'bucketId' => $bucketId,
            'path' => $path,
            'cosFile'=>$cosFile,
        );

        $post_param = array(
            'cosFile'=>'@'.$localFile,
        );

        return $this->api($script_name, $params, 'POST', 'http', true, $post_param);
    }

    /**
     * 直接上传文件内容
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $path     文件将要存放的目录，以 "/" 开头
     * @param string $filename 存储到COS之后的文件名
     * @param string $content  文件内容
     * @return array           结果数组
     */
    public function upload_file_by_content( $bucketId, $path, $filename, $content)
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );

        if(empty( $bucketId ) )
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'bucketId is empty.';
            return $ret;
        }
        if(empty( $filename ) )
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'filename is empty.';
            return $ret;
        }

        if(!is_string($content) )
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_INVALID;
            $ret['msg'] = 'bad content body.';
            return $ret;
        }

        if(empty( $path ))
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'Empty path param!';
            return $ret;
        }

        if($path[0] != "/")
        {
            $path = "/".$path;
        }

        $script_name = '/api/cos_upload';
        $params = array(
            'bucketId' => $bucketId,
            'path' => $path,
            'cosFile' => $filename,
        );

        return $this->api($script_name, $params, 'POST', 'http', true, $content);
    }

    /**
     * 上传文件的一个分片
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $path     分片将要存放的目录路径， 以"/"开头
     * @param string $filename 分片内容所属的文件名
     * @param float  $offset   分片在文件中的偏移位置.（大于等于0，且为64*1024的整数倍）
     * @param string $content  分片内容
     * @return array           结果数组
     */
    public function multipart_upload( $bucketId, $path, $filename, $offset, $content)
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );

        if(empty( $bucketId ) )
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'BucketId is empty';
            return $ret;
        }
        if(empty( $filename ) )
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'Filename is empty.';
            return $ret;
        }
        if($offset<0)
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_INVALID;
            $ret['msg'] = 'offset should >=0';
            return $ret;
        }
        if(empty( $path ))
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'Empty path param!';
            return $ret;
        }

        if($path[0] != "/")
        {
            $path = "/".$path;
        }

        if( !is_string($content) )
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_INVALID;
            $ret['msg'] = 'Content body is empty!';
            return $ret;
        }

        $script_name = '/api/cos_multipart_upload';
        $params = array(
            'bucketId' => $bucketId,
            'path' => $path,
            'cosFile' => $filename,
            'offset'	=>$offset,
        );
        return $this->api($script_name , $params , 'POST','http',true,$content);
    }
    /**
     * 设置文件上传结束标识（ 与cos_multipart_upload 配合使用 ） 
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $path     文件路径，以"/"开头
     * @return array           结果数组
     */
    public function complete_multipart_upload( $bucketId, $path)
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );

        if(empty( $bucketId ) )
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'BucketId is empty!';
            return $ret;
        }
        if(empty( $path ))
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'Empty path param!';
            return $ret;
        }

        if($path[0] != "/")
        {
            $path = "/".$path;
        }
        $script_name = '/api/cos_complete_multipart_upload';
        $params = array(
            'bucketId' => $bucketId,
            'path' => $path,
        );
        return $this->api($script_name , $params , 'PUT');
    }

    /**
     * 创建 bucket
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param array opt 可选参数列表: 
     *                "acl"  取值 0：必需在签名授权的情况下可读；1：公开读
     *                "$referer" 允许访问 bucket的referer，如 "http://qq.com"
     *  
     * @return array           结果数组
     */
    public function create_bucket($bucketId, $opt=array())
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );

        if(empty( $bucketId ) )
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'BucketId is empty!';
        return $ret;
        }

        $script_name = '/api/cos_create_bucket';
        $params = array(
            'bucketId' => $bucketId,			
        );

        if(isset($opt['acl']))
        {
        $acl = $opt['acl'];
        if($acl != 0 && $acl != 1)
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_INVALID;
        $ret['msg'] = 'Invalid acl!';
        return $ret;
        }
        $params['acl'] = $acl;
        }

        if(isset($opt['referer']))
        {
            $params['referer'] = $opt['referer'];
        }

        return $this->api($script_name , $params , 'PUT');
    }

    /**
     * 获取 bucket 列表
     * @param int $offset     起始地址（可选，默认为 0）
     * @param int $count      获取数量（可选，取值范围为0 到100，默认值为 20）
     * @param string $prefix  按照该名字前缀拉取bucket（可选，默认为空）
     * @return array          结果数组
     */
    public function list_bucket($offset=0, $count=20, $prefix="")
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );
        
            if( $offset < 0)
            {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_INVALID;
            $ret['msg'] = 'Illegal offset!';
            return $ret;
            }

        if($count < 0 || $count > 100)
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_INVALID;
        $ret['msg'] = 'Illegal count!';
        return $ret;
        }

        $script_name = '/api/cos_list_bucket';
        $params = array(
            'offset' => $offset,
            'count'  => $count,
        );

        if(strlen($prefix) > 0)
        {
            $params['prefix'] = $prefix;
        }

        return $this->api($script_name, $params, 'GET');
    }

    /**
     * 获取文件列表
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $path     目录路径， 以 "/" 开头
     * @param int    $offset   起始地址（可选，默认为 0）
     * @param int    $count    获取数量（可选，取值范围为0到100，默认值为 20）
     * @param int    $prefix   按照该名字前缀拉取file（可选，默认为空）
     * @return array           结果数组
     */
    public function list_file($bucketId, $path, $offset=0, $count=20, $prefix="")
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );

        if(empty( $bucketId ) )
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'BucketId is empty!';
        return $ret;
        }

        if(empty( $path ))
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'Empty path param!';
            return $ret;
        }

        if($path[0] != "/")
        {
            $path = "/".$path;
        }
            
            if( $offset < 0)
            {
            $ret['code'] = -1;
            $ret['msg'] = 'Illegal offset!';
            return $ret;
            }
            
            if($count < 0 || $count > 100)
            {
            $ret['code'] = -1;
            $ret['msg'] = 'Illegal count!';
            return $ret;
            }

        $script_name = '/api/cos_list_file';
        $params = array(
            'bucketId' => $bucketId,
            'path' => $path,
            'offset' => $offset,
            'count' => $count,
        );

        if(strlen($prefix) > 0)
        {
        $params['prefix'] = $prefix;
        }

        return $this->api($script_name , $params , 'GET');
    }

    /**
     * 创建目录
     *
     * @param string $bucketId  桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $path      目录路径，以"/"开头（注：上级目录必须存在）
     *
     * 
     * @param array $opt 可选参数列表:
     * 			   "mkType"  批量创建目录标识:p 标识可以批量创建目录
     *             "expires"  该目录下的”直接”文件（一级文件），下载时的 Expires header （可选，默认为7200）
     *             "cacheControl" 文件被下载时的 Cache-Control header （可选，默认为空）
     *             "contentEncoding" 文件被下载时的 Content-Encoding （可选，默认为空）
     *             "contentLanguage" 文件被下载时的 Content-Language （可选，默认为空）
     *             "contentDisposition" 文件被下载时的 Content-Disposition（可选，默认为空）
     * 
     * @return array 结果数组
     */
    public function create_dir($bucketId, $path, $opt)
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );
        
        if(empty( $bucketId ) )
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'BucketId is empty!';
        return $ret;
        }

        if(empty( $path ))
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'Empty path param!';
            return $ret;
        }

        if($path[0] != "/")
        {
            $path = "/".$path;
        }

        $script_name = '/api/cos_mkdir';
        $params = array(
            'bucketId' => $bucketId,
            'path' => $path,
        );

        if(isset($opt["mkType"])){
            $params["mkType"] = $opt['mkType'];
        }

        if(isset($opt['expires'])){
            $params["expires"] = $opt['expires'];
        }
        if(isset($opt['cacheControl'])){
            $params["cacheControl"] = $opt['cacheControl'];
        }
        if(isset($opt['contentEncoding'])){
            $params["contentEncoding"] = $opt['contentEncoding'];
        }
        if(isset($opt['contentLanguage'])){
            $params["contentLanguage"] = $opt['contentLanguage'];
        }
        if(isset($opt['contentDisposition'])){
            $params["contentDisposition"] = $opt['contentDisposition'];
        }

        return $this->api($script_name, $params, 'PUT');
    }

    /**
     * 修改单个文件名，或者修改空目录的目录名
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $spath 修改前路径,长度<=4096，字符（1~9 and A~Z and a~z  and _  - . / 和中文）
     * @param string $dpath 修改后路径,长度<=4096，字符（1~9 and A~Z and a~z  and _  - . / 和中文）
     *
     * @return array 结果数组
     */
    public function rename($bucketId, $spath, $dpath )
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );
        
        if(empty( $bucketId ) )
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'BucketId is empty!';
        return $ret;
        }

        if(empty( $spath ) || $spath[0] != "/")
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_INVALID;
        $ret['msg'] = 'Invalid source path!';
        return $ret;
        }

        if(empty( $dpath ) || $dpath[0] != "/")
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_INVALID;
        $ret['msg'] = 'Invalid dest path!';
        return $ret;
        }

        $script_name = '/api/cos_rename';
        $params = array(
            'bucketId' => $bucketId,
            'spath' => $spath,
            'dpath' => $dpath
        );

        return $this->api($script_name , $params , 'PUT');
    }

    /**
     * 删除文件
     *
     * @param string $bucketId  桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $path      需要删除文件的目录路径，以 "/" 开头
     * @param array  $file_list 需要删除的文件列表
     * @return array 结果数组
     */
    public function delete_file( $bucketId, $path, $file_list )
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );
        
        if(empty( $bucketId ) )
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'BucketId is empty!';
        return $ret;
        }

        if(empty( $path ))
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'Empty path param!';
            return $ret;
        }

        if($path[0] != "/")
        {
            $path = "/".$path;
        }

        if(!is_array($file_list) || empty($file_list))
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_INVALID;
            $ret['msg'] = 'Illegal file list!';
            return $ret;
        }

        $script_name = '/api/cos_delete_file';
        $params = array(
            'bucketId' => $bucketId,
            'deleteObj' => join($file_list,'|'),
            'path'	    =>	$path,
        );

        return $this->api($script_name , $params , 'DELETE');
    }

    /**
     * 删除目录（为空的目录才能被删除）
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $path 待删除的目录路径，以 "/" 开头
     * @return array 结果数组
     */
     public function delete_dir( $bucketId, $path )
     {
     $ret = array (
         'code'=>0,
         'msg'=>'ok',
     );

    if(empty( $bucketId ) )
    {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'BucketId is empty!';
        return $ret;
    }

    if(empty( $path ))
    {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'Empty path param!';
        return $ret;
    }

    if($path[0] != "/")
    {
        $path = "/".$path;
    }
    
        $script_name = '/api/cos_rmdir';
        $params = array(
        'bucketId' => $bucketId,
        'path'	   => $path,
    );
    
    return $this->api($script_name, $params, 'DELETE');
     }

    /**
     * 删除 bucket
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @return array 结果数组
     */
     public function delete_bucket( $bucketId)
     {
     $ret = array (
     'code'=>0,
     'msg'=>'ok',
 );
 
 if(empty( $bucketId ) )
 {
 $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
 $ret['msg'] = 'BucketId is empty!';
 return $ret;
 }
 
 $script_name = '/api/cos_delete_bucket';
 $params = array(
 'bucketId' => $bucketId,
        );
        
        return $this->api($script_name, $params, 'DELETE');
     }

    /**
     * 获取bucket信息，包括：referer、acl等
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     *
     * @return array 结果数组
     */

    public function get_bucket_info( $bucketId)
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );
        
        if(empty( $bucketId ) )
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'BucketId is empty!';
        return $ret;
        }

        $script_name = '/api/cos_get_bucket';
        $params = array(
            'bucketId' => $bucketId
        );

        return $this->api($script_name , $params , 'GET');
    }

    /**
     * 设置bucket信息，包括：referer、acl等
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param array opt 可选参数列表: 
     *                "acl"  取值 0：必需在签名授权的情况下可读；1：公开读
     *                "referer" 允许访问 bucket的referer，如 "http://qq.com"
     *                
     * @return array 结果数组
     */
    public function set_bucket_info( $bucketId, $opt = array())
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );
        
        if(empty( $bucketId ) )
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'BucketId is empty!';
        return $ret;
        }

        if( empty($opt) )
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
                $ret['msg'] = 'Empty params!';
                return $ret;
        }

        $script_name = '/api/cos_set_bucket';
        $params = array(
            'bucketId' => $bucketId,
        );

        if( isset($opt['acl']) )
        {
            $acl = $opt['acl'];
            if($acl != 0 && $acl != 1)
            {
                $ret['code'] = -1;
                $ret['msg'] = 'acl param error.';
                return $ret;
            }
            $params["acl"] = $acl;
        }

        if( isset($opt['referer']) )
        {
            $params["referer"] = $opt['referer'];
        }

        return $this->api($script_name, $params, 'PUT');
    }

    /**
     * 获取文件或目录信息
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $path 文件或者目录路径，以 "/" 开头
     *
     * @return array 结果数组
     */
    public function get_meta($bucketId , $path )
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );
        
        if(empty( $bucketId ) )
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'BucketId is empty!';
        return $ret;
        }

        if(empty( $path ))
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
            $ret['msg'] = 'Empty path param!';
            return $ret;
        }

        if($path[0] != "/")
        {
            $path = "/".$path;
        }

        $script_name = '/api/cos_get_meta';
        $params = array(
            'bucketId' => $bucketId,
            'path' => $path
        );

        return $this->api($script_name , $params , 'GET');
    }

    /**
     * 设置 目录 属性，设置后，该目录下的所有文件被下载时将会输出对应的HTTP头
     *
     * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $path 文件或者目录路径
     * 
     * @param array $opt 可选参数列表：
     *             "expires"  该目录下的”直接”文件（一级文件），下载时的 Expires header （可选，默认为7200）
     *             "cacheControl" 文件被下载时的 Cache-Control header（可选）
     *             "contentEncoding" 文件被下载时的 Content-Encoding（可选）
     *             "contentLanguage" 文件被下载时的 Content-Language（可选）
     *             "contentDisposition" 文件被下载时的 Content-Disposition（可选）
     *
     * @return array 结果数组
     */
    public function set_meta($bucketId, $path, $opt)
    {
        $ret = array (
            'code'=>0,
            'msg'=>'ok',
        );
        
        if(empty( $bucketId ) )
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
        $ret['msg'] = 'BucketId is empty!';
        return $ret;
        }
        
        if(empty( $path ))
        {
        $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_INVALID;
        $ret['msg'] = 'Invalid path param!';
        return $ret;
        }

        if(empty($opt))
        {
            $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
                $ret['msg'] = 'Empty params! Nothing to be set!';
                return $ret;
        }

        $script_name = '/api/cos_set_meta';
        $params = array(
            'bucketId' => $bucketId,
            'path'     => $path,
        );

        if(isset($opt['expires'])){
            $params["expires"] = $opt['expires'];
        }
        if(isset($opt['cacheControl'])){
            $params["cacheControl"] = $opt['cacheControl'];
        }
        if(isset($opt['contentEncoding'])){
            $params["contentEncoding"] = $opt['contentEncoding'];
        }
        if(isset($opt['contentLanguage'])){
            $params["contentLanguage"] = $opt['contentLanguage'];
        }
        if(isset($opt['contentDisposition'])){
            $params["contentDisposition"] = $opt['contentDisposition'];
        }

        return $this->api($script_name , $params , 'PUT');
    }

    /**
     * 对线上文件进行压缩 （目前支持不超过50MB的JPG、PNG格式的图片）
     *
     * @param string $srcBucketId 待压缩文件所在的bucket, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $dstBucketId 压缩后文件存放的bucket, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $srcFilePath 待压缩文件所在路径, 长度小于等于4096, 字符（123456789 and A~Z and a~z  and _  - . / 
     *                        和中文), 以"/"开头
     * @param string $dstFilePath 压缩后文件的存放路径, 长度小于等于4096, 字符（123456789 and A~Z and a~z  and _  - . / 
     *                        和中文), 以"/"开头
     @param array $opt 附加参数,压缩/缩放/水印等参数 可选参数列表：
     array(
         "zoomType" =>1,//0不缩放; 1等比缩放,不裁剪;2缩放裁剪...;3非等比压缩,把整张图缩放到width/height内
         "width" =>800,//缩放后的宽度
         "height" =>800,//缩放后的高度
         "compress"=>1, //1 or 0 是否需要压缩(质量为85),(默认值为1)
         "WMText"	=>	"my tag", //水印文字内容
         "WMAlign"	=>	1, //水印的大致区域 1: 图片左上方; 2: 图片中上方;  3: 图片右上方;  4: 图片中间 齐左侧;  5: 图片正中;  6: 图片中间 齐右侧;  7: 图片左下方; 8: 图片中下方;  9: 图片右下方
         "WMOffsetX"	=>	100, //水印的偏移像素值。 X轴  (向右为正)
         "WMOffsetY"	=>	-150, //水印的偏移像素值。 Y轴  (向下为正)
         "WMColor"	=>	"#ff00007f", //图片的RGBA值(“#RGBA”,红绿蓝+透明度)
         "WMFontType"	=>	"仿宋", //水印字体类型. :仿宋;
    "WMFontSize"=>15,//水印文本字体大小
        "WMDegree" =>10,//水印文本旋转角度，正数为顺时针旋转
    );
    
        * @return array 结果数组
     */
     public function compress_online_file($srcBucketId, $dstBucketId, $srcFilePath, $dstFilePath,$opt=array())
     {
     $ret = array (
     'code'=>0,
     'msg'=>'ok',
 );
 
 if(empty( $srcBucketId ))
 {
 $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
 $ret['msg'] = 'Src BucketId is empty!';
 return $ret;
 }
 
 if(empty( $dstBucketId ))
 {
 $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
 $ret['msg'] = 'Dest BucketId is empty!';
 return $ret;
 }

    if(empty( $srcFilePath ))
    {
    $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
    $ret['msg'] = 'Src path is empty!';
    return $ret;
    }

    if($srcFilePath[0] != "/")
    {
    $srcFilePath = "/".$srcFilePath;
    }

    if(empty( $dstFilePath ))
    {
    $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
    $ret['msg'] = 'Dest path is empty!';
    return $ret;
    }

    if($dstFilePath[0] != "/")
    {
    $dstFilePath = "/".$dstFilePath;
    }
    
    $script_name = '/api/cos_compress_file';
    $params = array(
    'srcBucketId' => $srcBucketId,
    'dstBucketId' => $dstBucketId,
    'srcFilePath' => $srcFilePath,
    'dstFilePath' => $dstFilePath,
);
$params = $params+$opt;
return $this->api($script_name , $params , 'GET');
     }

    /**
     * 上传文件并压缩 （目前支持不超过50MB的JPG、PNG格式的图片，注：上传内容一定要符合图片格式）
     *
     * @param string $compressBucketId  压缩后文件存放的bucket, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     * @param string $compressFilePath  压缩后文件的存放路径, 长度小于等于4096, 字符（123456789 and A~Z and a~z  and _  - . /
     *                              和utf8编码的中文), 以"/"开头
     * @param string $localfile     本地文件
     * @param opt 可选参数列表： 
     *            "uploadBucketId"  原始文件上传后存放的bucket（可选参数，如不保存源文件，则不传此参数）,
     *                              长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
     *            "uploadFilePath"  原始文件上传后存放的完整路径（可选参数，如不保存源文件，则不传此参数）长度小于等于4096、
     *                          字符（123456789 and A~Z and a~z  and _  - . / 和中文）, 以"/"开头
     *  		"zoomType"			//0不缩放; 1等比缩放,不裁剪;2缩放裁剪...3非等比压缩,把整张图缩放到width/height内
     *			"width"				//缩放后的宽度
     *			"height"			//缩放后的高度
     *			"compress"			//1 or 0 是否需要压缩(质量为85),(默认值为1)
     *			"WMText"			//水印文字内容
     *			"WMAlign"			//水印的大致区域 1: 图片左上方; 2: 图片中上方;  3: 图片右上方;  4: 图片中间 齐左侧;  5: 图片正中;  6: 图片中间 齐右侧;  7: 图片左下方; 8: 图片中下方;  9: 图片右下方
     *			"WMOffsetX"			//水印的偏移像素值。 X轴  (向右为正)
     *			"WMOffsetY"			//水印的偏移像素值。 Y轴  (向下为正)
     *			"WMColor"			//图片的RGBA值(“#RGBA”,红绿蓝+透明度) 如 "#ff00007f"
     *			"WMFontType"		//水印字体类型. :仿宋;
     *			"WMFontSize"		//水印文本字体大小
     *			"WMDegree"			//水印文本旋转角度，正数为顺时针旋转
     * @return array 结果数组
     */
     public function upload_file_with_compress($compressBucketId, $compressFilePath, $localfile, $opt)
     {
     $ret = array (
     'code'=>0,
     'msg'=>'ok',
 );
 
 if(empty( $compressBucketId ))
 {
 $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
 $ret['msg'] = 'Dest BucketId is empty!';
 return $ret;
 }
 
 if(empty( $compressFilePath ))
 {
 $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
 $ret['msg'] = 'Compress path is empty!';
 return $ret;
 }

     if($compressFilePath[0] != "/")
     {
     $compressFilePath = "/".$compressFilePath;
     }

     if(empty( $localfile ))
     {
     $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
     $ret['msg'] = 'Local file is empty!';
     return $ret;
     }
     
     $script_name = '/api/cos_upload_with_compress';
     $params = array(
     'compressBucketId' => $compressBucketId,
     'compressFilePath' => $compressFilePath,
 );

     if(isset( $opt['uploadBucketId'] ))
     {
     $params['uploadBucketId'] = $opt['uploadBucketId'];
     }
     if(isset( $opt['uploadFilePath'] ))
     {
     $uploadFilePath = $opt['uploadFilePath'];
     if($uploadFilePath[0] != "/")
     {
     $uploadFilePath = "/".$uploadFilePath;
     $opt['uploadFilePath'] = $uploadFilePath;
     }
         $params['uploadFilePath'] = $uploadFilePath;
     }

     $post_param = array(
         'cosFile' => '@'.$localfile,
     );
     $params = $params+$opt;
     return $this->api($script_name, $params, 'POST', 'http', true, $post_param);
     }

     /**
      * 上传文件内容并进行压缩 （目前支持不超过50MB的JPG、PNG格式的图片，注：上传内容一定要符合图片格式）
      *
      * @param string $compressBucketId  压缩后文件存放的bucket, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
      * @param string $compressFilePath  压缩后文件的存放路径, 长度小于等于4096, 字符（123456789 and A~Z and a~z  and _  - . /
      *                              和utf8编码的中文), 以"/"开头
      * @param string $fileContent  将要上传的文件内容
      * @param array opt 可选参数列表： 
      *            "uploadBucketId"  原始文件上传后存放的bucket（可选参数，如不保存源文件，则不传此参数）,
      *                              长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
      *            "uploadFilePath"  原始文件上传后存放的完整路径（可选参数，如不保存源文件，则不传此参数）长度小于等于4096、
      *                          字符（123456789 and A~Z and a~z  and _  - . / 和中文）, 以"/"开头
      *
      * @return array 结果数组
      */
      public function upload_file_content_with_compress($compressBucketId, $compressFilePath, $fileContent, 
      $opt=array())
      {
      $ret = array (
      'code'=>0,
      'msg'=>'ok',
  );
     
         if(empty( $compressBucketId ))
         {
         $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
         $ret['msg'] = 'Dest BucketId is empty!';
         return $ret;
         }
         
         if(empty( $compressFilePath ))
         {
         $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
         $ret['msg'] = 'Compress path is empty!';
         return $ret;
         }
         
         if($compressFilePath[0] != "/")
         {
         $compressFilePath = "/".$compressFilePath;
         }
         
         if(empty( $fileContent ))
         {
         $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
         $ret['msg'] = 'File content is empty!';
         return $ret;
         }
     
         $script_name = '/api/cos_upload_with_compress';
         $params = array(
         'compressBucketId' => $compressBucketId,
         'compressFilePath' => $compressFilePath,
     );

     if(isset( $opt['uploadBucketId'] ))
     {
         $params['uploadBucketId'] = $opt['uploadBucketId'];
     }
     if(isset( $opt['uploadFilePath'] ))
     {
         $uploadPath = $opt['uploadFilePath'];
         if($uploadPath[0] != "/")
         {
         $uploadPath = "/".$uploadPath;
         }
         $params['uploadFilePath'] = $uploadPath;
     }
         $params = $params+$opt;
     return $this->api($script_name, $params, 'POST', 'http', true, $fileContent);
      }

     /**
      * 获取下载链接
      * 
      * @param string 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
      * @param string $path 文件的路径
      * @param bool   $need_sig 是否需要签名，如果为公有读则建议设置为false，如果为私有读则一定设置为true
      * @param array $option=array(
      *				"res_cache_control"=>"",
      *				"res_content_disposition"=>"",
      *				"res_content_type"	=>	"",
      *				"res_encoding"		=>"",
      *				"res_expires"		=>"",
      *				"res_content_language"=>"",
      *			)
      *
      * @return string;
      */
     public function get_download_url($bucketId, $path, $need_sig, $option=array())
     {
         $ret = array (
             'code'=>0,
             'msg'=>'ok',
         );
         
         if(empty( $bucketId ) )
         {
         $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
         $ret['msg'] = 'BucketId is empty!';
         return $ret;
         }

         if(empty( $path ))
         {
         $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_INVALID;
         $ret['msg'] = 'Invalid path param!';
         return $ret;
         }

         $filter =array(
             "res_cache_control"=>1,
             "res_content_disposition"=>1,
             "res_content_type"	=>	1,
             "res_encoding"		=>1,
             "res_expires"		=>1,
             "res_content_language"=>1,
         );

         foreach($option as $key=> $item){
             if(!isset($filter[$key])){
                 unset($option[$key]);
             }
         }

         if(strlen($path)>0 && $path[0]!="/"){
             $path = "/".$path;
         }

         $url = COS_DOWNLOAD_HOST.'/'.$this->accessId.'/'.$bucketId.$path.'?';
         if($need_sig)
         {
             $option['accessId'] = $this->accessId;
             $option['path'] = $path;
             $option['bucketId'] = $bucketId;
             if(!empty($this->secretId)){
                 $option['secretId']=$this->secretId;
             }
             $option['time'] = time();
             $option['sign'] =  CosUtil::getDownloadSign($option, $this->accessKey);
             unset($option['accessId']);
             unset($option['path']);
             unset($option['bucketId']);
         }
         $query_string = array();
         foreach ($option as $key => $val ) 
         { 
             array_push($query_string, rawurlencode($key) . '=' . rawurlencode($val));
         }  
         $query_string = join('&', $query_string);
         return $url.$query_string;
     }

     /**
      * 获取上传文件链接
      *
      * @param string $bucketId 桶Id, 长度<=64、字符（123456789 and A~Z and a~z  and _  - .）
      * @param string $path 文件或者目录路径
      * @param string $expires 该目录下的”直接”文件(一级文件)，下载时的Expires header
      * @param string $cacheControl   文件被下载时的cache-control
      * @param string $contentEncoding  文件被下载时的Content-Encoding
      * @param string $contentLanguage  文件被下载时的contentLanguage
      * @param string $opt 可选参数
      *
      * @return array 结果数组
      */
     public function get_upload_url($bucketId, $path, $cosFile)
     {
         $ret = array (
             'code'=>0,
             'msg'=>'ok',
         );
         
         if(empty( $bucketId ))
         {
         $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
         $ret['msg'] = 'BucketId is empty!';
         return $ret;
         }
         
         if(empty( $path ))
         {
             $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
             $ret['msg'] = 'Empty path param!';
             return $ret;
         }

         if($path[0] != "/")
         {
             $path = "/".$path;
         }

         if(empty( $cosFile ) )
         {
         $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
         $ret['msg'] = 'Cosfile is empty!';
         return $ret;
         }		

         $api_name = '/api/cos_upload';
         $params = array(
             'bucketId' => $bucketId,
             'cosFile'=>$cosFile,
             'path' => $path,
             'accessId' => $this->accessId,
             'time' => time(),
         );
         $sign = CosUtil::makeCosSig('',$api_name, $params, $this->accessKey);
         $url = 'http://' . $this->host . $api_name.'?';
         $query_string = array();
         foreach ($params as $key => $val ) 
         { 
             array_push($query_string, rawurlencode($key) . '=' . rawurlencode($val));
         }  
         $query_string = join('&', $query_string);
         return $url.$query_string;
     }

     /**
      * 执行API调用，返回结果数组
      *
      * @param array $script_name 调用的API方法 参考
      * @param array $params 调用API时带的参数
      * @param string $method 请求方法 post / get / put / delete / head
      * @param string $protocol 协议类型 http / https
      * @return array 结果数组
      */
     public function api($script_name, $params, $method='post', $protocol='http' ,$upload=false, $content='')
     {
         if(empty($this->accessId) || empty($this->accessKey))
         {
             $ret['code'] = COS_ERROR_REQUIRED_PARAMETER_EMPTY;
                 $ret['msg'] = 'Empty accessId or accessKey!';
                 return $ret;
         }

         // 无需传sign, 会自动生成
         unset($params['sign']);

         // 添加一些参数

         if(!empty($this->secretId )){
             $params['secretId'] = $this->secretId;
         }

         $params['accessId'] = $this->accessId;

         //记录接口调用开始时间
         $params['time'] = time();

         // 生成签名
         $secret = $this->accessKey;
         $sign = CosUtil::makeCosSig('', $script_name, $params, $secret);

         $params['sign'] = $sign;

         $url = $protocol . '://' . $this->host . $script_name;
         $cookie = array();

         // 发起请求
         if($upload)
             $ret =CosUtil::makeUploadRequest($url, $params, $cookie, $content, $protocol);
         else
             $ret = CosUtil::makeRequest($url, $params, $cookie, $method, $protocol);

         if (false === $ret['result']){
             $result_array = array(
                 'code' => COS_ERROR_CURL + $ret['errno'],
                 'msg' => $ret['msg'],
             );
         }

         $result_array = json_decode($ret['msg'], true);

         // 远程返回的不是 json 格式, 说明返回包有问题
         if (is_null($result_array)) {
             $result_array = array(
                 'code' => COS_ERROR_RESPONSE_DATA_INVALID,
                 'msg' => $ret['msg']
             );
         }

         return $result_array;
     }
}


//endof script;
