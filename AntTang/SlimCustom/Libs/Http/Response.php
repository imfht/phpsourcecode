<?php
/**
 * @package     Response.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月15日
 */

namespace SlimCustom\Libs\Http;

use Psr\Http\Message\StreamInterface;
use Slim\Interfaces\Http\HeadersInterface;
use UnexpectedValueException;

/**
 * Response
 * 
 * @author Jing <tangjing3321@gmail.com>
 */
class Response extends \Slim\Http\Response
{
    /**
     * Known handled content types
     *
     * @var array
     */
    protected $allowedContentTypes = [
        'application/json',
        'application/xml',
        'text/xml',
        'text/html',
    ];
    
    /**
     * Default contentType
     * 
     * @var string
     */
    protected $defaultContentType = 'text/html';
    
    /**
     * 初始化响应类
     * 
     * @param number $status
     * @param HeadersInterface $headers
     * @param StreamInterface $body
     */
    public function __construct($status = 200, HeadersInterface $headers = null, StreamInterface $body = null)
    {
        parent::__construct($status, $headers, $body);
    }
    
    /**
     * Determine which content type we know about is wanted using Accept header
     *
     * Note: This method is a bare-bones implementation designed specifically for
     * Slim's error handling requirements. Consider a fully-feature solution such
     * as willdurand/negotiation for any other situation.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function determineContentType()
    {
        $acceptHeader = request()->getHeaderLine('Accept');
        $selectedContentTypes = array_intersect(explode(',', $acceptHeader), $this->allowedContentTypes);
        if (count($selectedContentTypes)) {
            return current($selectedContentTypes);
        }
        
        // handle +json and +xml specially
        if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
            $mediaType = 'application/' . $matches[1];
            if (in_array($mediaType, $this->allowedContentTypes)) {
                return $mediaType;
            }
        }
        
        if (strpos(PHP_SAPI, 'cli') !== false) {
            $this->defaultContentType = 'text/cli';
        }
    
        return $this->defaultContentType;
    }
    
    /**
     * 成功返回
     * 
     * @param mix $data
     * @param integer $status
     * @param number $encodingOptions
     * @return \SlimCustom\Libs\Http\Response
     */
    public function success($data, $status = null, $encodingOptions = 0)
    {
        $data = [
            'code' => 0,
            'msg' => 'success',
            'result' => $data
        ];
        return $this->output($data, $status, $encodingOptions);
    }

    /**
     * 失败返回
     * 
     * @param string $code
     * @param string $msg
     * @param integer $status
     * @param number $encodingOptions
     * @return \SlimCustom\Libs\Http\Response
     */
    public function error($code, $msg = '', $status = null, $encodingOptions = 0)
    {
        $data = [
            'code' => $code,
            'msg' => $msg ? $msg : config('errors.' . $code . '.' . config('language', 'zh'))
        ];
        return $this->output($data, $status, $encodingOptions);
    }
    
    /**
     * 响应输出
     * 
     * @param mix $data
     * @param integer $status
     * @param number $encodingOptions
     * @return \SlimCustom\Libs\Http\Response
     */
    public function output($data, $status= null, $encodingOptions = 0)
    {
        $contentType = $this->determineContentType();
        switch ($contentType) {
            case 'application/json':
                return $this->withJson($data, $status, $encodingOptions);
                break;
        
            case 'text/xml':
            case 'application/xml':
                return $this->withXml($data, $status);
                break;
        
            case 'text/html':
                return $this->write($data);
                break;
        
            default:
                throw new UnexpectedValueException('Cannot render unknown content type ' . $contentType);
        }
    }
    
    /**
     * withXml
     * 
     * @param mxi $data
     * @param integer $status
     * @param string $version
     * @return \SlimCustom\Libs\Http\Response
     */
    public function withXml($data, $status = null, $version = '1.0')
    {
        $responseWithXml = $this->write(arrayToXml($data, $version))->withHeader('Content-Type', 'application/xml;charset=utf-8');
        if (isset($status)) {
            return $responseWithXml->withStatus($status);
        }
        return $responseWithXml;
    }
    
    /**
     * Set default allowedContentTypes
     * 
     * @param array $allowedContentTypes
     * @return \SlimCustom\Libs\Http\Response
     */
    public function setAllowedContentTypes(array $allowedContentTypes)
    {
        $this->allowedContentTypes = $allowedContentTypes;
        return $this;
    }
    
    /**
     * Set default response contentType
     * 
     * @param string $defaultContentType
     * @return \SlimCustom\Libs\Http\Response
     */
    public function setDefaultContentType($defaultContentType)
    {
        $this->defaultContentType = $defaultContentType;
        return $this;
    }
}