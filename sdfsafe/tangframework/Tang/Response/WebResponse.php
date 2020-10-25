<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Response;
use Tang\Services\FileService;
use Tang\Web\Controllers\MessageController;

/**
 * Class WebResponse
 * @package Tang\Response
 */
class WebResponse implements IResponse
{
    /**
     * 报头状态码
     * @var array
     */
    protected  $status = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );
    /**
     * 系统编码
     * @var string
     */
    protected $charset;
    /**
     * @var MessageController
     */
    protected $messageController;
    /**
     * @param $charset
     * @param string $contentType
     */
    public function __construct($charset,$contentType='text/html')
    {
        $this->charset = $charset;
        $this->contentType($contentType='text/html');
    }

    /**
     * 设置过期时间
     * @param int $time
     */
    public function expires($time =0)
	{
		header('Expires: '.gmdate('D, d M Y H:i:s', time()+$time).' GMT');
	}

	/**
	 * 在$time秒后重定向到一个新的URI资源。
	 * @param int $time 时间
	 * @param string $url 重定向的URI资源
	 */
	public function refresh($time,$url)
	{
		header('Refresh: '.$time.'; url='.$url);
	}

    /**
     * 获取消息提示
     * @return MessageController
     */
    public function message()
    {
        !$this->messageController && $this->messageController = MessageController::create();
        return $this->messageController;
    }
    /**
     * 设置contentType
     * @param $contentType
     */
    public function contentType($contentType='text/html')
	{
		header('Content-Type: '.$contentType.'; charset='.$this->charset);
	}

    /**
     * 根据文件后缀名设置contentType
     * @param $extension
     */
    public function contentTypeByFileExtension($extension)
    {
        $type = array(
            'html'  =>  'text/html',
            'xml'   =>  'application/xml,text/xml,application/x-xml',
            'json'  =>  'application/json,text/x-json,application/jsonrequest,text/json',
            'js'    =>  'text/javascript,application/javascript,application/x-javascript',
            'css'   =>  'text/css',
            'rss'   =>  'application/rss+xml',
            'yaml'  =>  'application/x-yaml,text/yaml',
            'atom'  =>  'application/atom+xml',
            'pdf'   =>  'application/pdf',
            'text'  =>  'text/plain',
            'png'   =>  'image/png',
            'jpg'   =>  'image/jpg,image/jpeg,image/pjpeg',
            'gif'   =>  'image/gif',
            'csv'   =>  'text/csv'
        );
        if(!isset($type[$extension]))
        {
            $extension = 'html';
        }
        return $this->contentType($type[$extension]);
    }
    /**
     * 跳转
     * @param $url 跳转地址
     * @param int $statusCode 状态码
     */
    public function redirect($url,$statusCode=302)
	{
        $this->httpStatus($statusCode);
		header('location:'.$url);
		exit;
	}

    /**
     * 发送http状态码
     * @param $statusCode
     */
    public function httpStatus($statusCode)
    {
        if(isset($this->status[$statusCode]))
        {
            header('HTTP/1.1 '.$statusCode.' '.$this->status[$statusCode]);
            header('Status:'.$statusCode.' '.$this->status[$statusCode]);
        }
    }

	/**
	 * 设置报头的Sever信息
	 * 例如隐藏服务器为WINDOWS.APACHE
	 * Myserver/2.1 (Unix) (Red-Hat/Linux)
	 * @param string $serverName
	 */
	public function serverName($serverName)
	{
		header('Server: '.$serverName);
	}

    /**
     * 设置X-Powered-By
     * @param $value
     */
    public function poweredBy($value)
    {
        header('X-Powered-By:'.$value);
    }

    /**
     * 设置不缓存
     */
    public function noCache()
	{
		header('Pragma: no-cache');
	}

    /**
     * 发送header报头
     * @param $header
     */
    public function header($header)
	{
		header($header);
	}

    /**
     * @see IResponse::write
     */
    public function write($string)
	{
		echo $string;
	}

    /**
     * @see IResponse::writeLine
     */
    public function writeLine($string)
    {
        echo $string.'<br/>';
    }

    /**
     * @see IResponse::writeArray
     */
	public function writeArray($array)
	{
		echo var_export($array,true);
	}

	/**
	 * 将一个文件输出
	 * @param string $path
	 */
	public function writeFile($path)
	{
		$content = '';
        FileService::read($path, $content);
		echo $content;
	}
}