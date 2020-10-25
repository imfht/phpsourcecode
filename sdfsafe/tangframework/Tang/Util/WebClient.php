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
namespace Tang\Util;
use Tang\Exception\SystemException;
use Tang\Services\DirectoryService;

/**
 * WebClient 类
 * 用于web客户端请求 需要curl支持
 * @author wujibing
 *
 */
class WebClient
{
    /**
     * 超时时间
     * @var int
     */
    private $timeout = 30;
    /**
     * 请求关联的标头名称/值对集合
     * @var array
     */
    private $headers = array(
        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:19.0) Gecko/20100101 Firefox/19.0',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Charset' => 'GB2312,utf-8;q=0.7,*;q=0.7',
        'Cache-Control' => 'no-cache',
        'Connection' =>  'Close',
        'Accept-Language' => 'zh-cn,zh;q=0.5',
        'Expect' => ''
    );
    /**
     * 响应标头
     * @var array
     */
    private $responseHeader = array();
    /**
     * @var array
     */
    private $responseHeaders = array();
    /**
     * 线程数量
     * @var int
     */
    private $threadNumber = 10;
    /**
     * @var array
     */
    private $threadItems = array();
    /**
     * 是否多线程运行
     * @var bool
     */
    private $threadsAreRunning = false;

    /**
     * @param $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $timeout = (int)$timeout;
        $timeout < 1 && $timeout = 30;
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * 设置线程数量
     * @param $threadNumber
     * @return $this
     */
    public function setThreadNumber($threadNumber)
    {
        $threadNumber = (int)$threadNumber;
        $threadNumber < 1 && $threadNumber = 10;
        $this->threadNumber = $threadNumber;
        return $this;
    }

    /**
     * 添加报头
     * 例如addHeader('Connection',	'keep-alive')
     * @param $name
     * @param $value
     * @return $this
     */
    public function appendHeader($name,$value)
    {
        $this->headers[$name] = $value;
        return $this;
    }
    /**
     * 当使用downloadString和downloadFile方式时有效
     * 根据$name获取服务器返回的报头信息
     * 如果没有值的话，则返回$default
     * 只适用于downloadString和downloadFile两个方法使用
     * <code>
     * $webClient = new WebClient();
        $webClient->setTimeout(60);
        echo ($webClient->downloadFile('http://www.baidu.com','e:/d.txt'));
        print_r($webClient->getResponseHeader('HttpStatus'));//返回http状态
     * </code>
     * @param string $name
     * @param string $default
     * @return mixed
     */
    public function getResponseHeaderByName($name,$default='')
    {
        return isset($this->responseHeader[$name]) ? $this->responseHeader[$name] : $default;
    }

    /**
     * 当使用downloadString和downloadFile方式时有效
     * 返回所有的报头信息
     * @return array
     */
    public function getResponseHeader()
    {
        return $this->responseHeader;
    }
    /**
     * 下载字符串
     * @param string $url 请求网页
     * @param array $postData 请求的POST数据
     * @param ResponseCookies $cookies 请求的cookie
     * @return string 返回网页内容
     */
    public function downloadString($url,array $postData = array(),ResponseCookies $cookies = null)
    {
        $urlStruct = array('url' => $url,'postData' => $postData,'cookies' => $cookies);
        $content = '';
        $this->exec($urlStruct,$content);
        return $content;
    }

    /**
     * 下载文件
     * @param $url url地址
     * @param $saveFileName 保存的文件地址
     * @param array $postData post数据
     * @param ResponseCookies $cookies 请求的cookie
     */
    public function downloadFile($url,$saveFileName,array $postData = array(),ResponseCookies $cookies = null)
    {
        $urlStruct = array('url' => $url,'postData' => $postData,'filePath'=>$saveFileName,'cookies' => $cookies);
        $content = '';
        $this->exec($urlStruct,$content);
    }

    /**
     * 添加多个URL同步下载，
     * $urlItems结构如下
     * $urlItems[] = array(
     * 	'url' => 'http://www.baidu.com',//下载的地址 此为必须
     * 	'postData' => array('a'=>1,'b' => 'c'),//构建post请求  并构成 a=1&b=c的post信息
     *  'filePath' => 'e:/q.txt',//将下载的内容保存在e:/q.txt 并不返回内容
     *  'headers' => array('User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:19.0) Gecko/20100101 Firefox/19.0',),//添加自定义的header信息
     *  'callback' => \Closure,//回调函数。若没有该参数 则使用$callBack参数进行回调
     *  'cookies' => ResponseCookies 包含传递的cookie信息
     * );
     * @param array $urlItems URL数组
     * @param \Closure $callback 回调函数 回调函数包含两个参数 1报头信息、2网页内容
     */
    public function addThreadItems(array $urlItems,\Closure $callback = null)
    {
        if(!$urlItems)
        {
            return ;
        }
        foreach ($urlItems as $urlStruct)
        {
            if(!is_array($urlStruct) || !isset($urlStruct['url']) || !$urlStruct['url'])
            {
                continue;
            }
            if(!isset($urlStruct['callback']) || !$urlStruct['callback'])
            {
                $urlStruct['callback'] = $callback;
            }
            $this->threadItems[] = $urlStruct;
        }
        if($this->threadsAreRunning)
        {
            return;
        }
        $this->threadsAreRunning = true;
        $this->runThread();
    }

    /**
     * 运行线程
     */
    private function runThread()
    {
        if(!$this->threadItems)
        {
            $this->threadsAreRunning = false;
            return;
        } else
        {
            $this->threadsAreRunning = true;
        }
        $this->responseHeader = array();
        $itemLength = count($this->threadItems);
        $itemLength > $this->threadNumber ? $itemLength = $this->threadNumber : '';
        $curlResource = curl_multi_init();
        $curlResources = array();
        for ($i = 0; $i < $itemLength; $i++)
        {
            $curlResources[$i] = $this->createCurl($this->threadItems[$i]);
            curl_multi_add_handle($curlResource,$curlResources[$i]);
        }
        $running = 0;
        do
        {
            curl_multi_exec($curlResource,$running);
        } while ($running > 0);
        for ($i = 0; $i < $itemLength; $i++)
        {
            $key = (int)$curlResources[$i];
            $temp = array_shift($this->threadItems);
            $result = curl_multi_getcontent($curlResources[$i]);
            $header = $this->getHeader($this->responseHeaders[$key]);
            curl_multi_remove_handle($curlResource,$curlResources[$i]);
            curl_close($curlResources[$i]);
            if(isset($temp['fileResource']) && is_resource($temp['fileResource']))
            {
                fclose($temp['fileResource']);
            }
            if(is_callable($temp['callback']))
            {
                $temp['callback']($header,$result);
            }
            unset($this->responseHeaders[$key]);
        }
        curl_multi_close($curlResource);
        $this->runThread();
    }

    /**
     * 根据$urlStruct数组结构创建curl
     * @param array $urlStruct
     * @return resource
     */
    private function createCurl(&$urlStruct)
    {
        $curlResource = curl_init($urlStruct['url']);
        curl_setopt($curlResource, CURLOPT_URL,$urlStruct['url']);
        if(isset($urlStruct['postData']) && $urlStruct['postData'])
        {
            curl_setopt($curlResource,CURLOPT_POST,count($urlStruct['postData']));
            curl_setopt($curlResource,CURLOPT_POSTFIELDS,$urlStruct['postData']);
        }
        if(isset($urlStruct['filePath']) && $urlStruct['filePath'])
        {
            DirectoryService::getService()->create(dirname($urlStruct['filePath']));
            $urlStruct['fileResource'] = fopen($urlStruct['filePath'], 'w');
            curl_setopt($curlResource,CURLOPT_FILE,$urlStruct['fileResource']);
        } else
        {
            curl_setopt($curlResource,CURLOPT_RETURNTRANSFER, true);
        }
        $headers = array();
        if(isset($urlStruct['headers']) && is_array($urlStruct['headers']) && $urlStruct['headers'])
        {
            $headers = array_merge($urlStruct['headers'],$this->headers);
        } else
        {
            $headers = $this->headers;
        }
        $headersArray = array();
        if ($headers) foreach ($headers as $key => $value)
        {
            $headersArray[] = $key.':'.$value;
        }
        if(isset($urlStruct['cookies']) && $urlStruct['cookies'])
        {
            $headersArray[] = 'cookie:'.$urlStruct['cookies'];
        }
        curl_setopt($curlResource,CURLOPT_HEADER,false);
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $headersArray);
        curl_setopt($curlResource, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($curlResource, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curlResource,CURLOPT_TIMEOUT,$this->timeout);
        curl_setopt($curlResource, CURLOPT_BUFFERSIZE, 1024);
        $that = $this;
        curl_setopt($curlResource,CURLOPT_HEADERFUNCTION,function($res, $header) use($that)
        {
            $key = (int)$res;
            if(!isset($that->responseHeaders[$key]))
            {
                $that->responseHeaders[$key] = '';
            }
            $that->responseHeaders[$key] .= $header;
            return strlen($header);
        });
        return $curlResource;
    }

    /**
     * 获取报头
     * @param $headerString
     * @return array
     */
    private function getHeader($headerString)
    {
        $headerArray = array();
        if(preg_match_all('$Set-Cookie:(.+?); $i', $headerString,$matches))
        {
            $splitIndex = 0;
            $cookies = new ResponseCookies();
            foreach ($matches[1] as $value)
            {
                $splitIndex = strpos($value, '=');
                $cookies->insert(trim(substr($value,0,$splitIndex)),trim(substr($value,$splitIndex+1)));
            }
            $headerArray['cookies'] = $cookies;
        }
        if(preg_match_all('$(.+?):(.+?)\n$', $headerString, $matches))
        {
            foreach ($matches[1] as $key => $value)
            {
                if(strtolower($value) == 'set-cookie')
                {
                    continue;
                }
                $headerArray[$value] = trim($matches[2][$key]);
            }
        }
        $headerArray['httpStatus'] = substr($headerString, 9,3);
        return $headerArray;
    }

    /**
     * 根据$urlStruct数组结构来进行curl处理
     * @param $urlStruct
     * @param $content
     * @throws \Tang\Exception\SystemException
     */
    private function exec($urlStruct,&$content)
    {
        $this->responseHeader = array();
        $curlResource = $this->createCurl($urlStruct);
        $content = curl_exec($curlResource);
        $curlErrorNo = curl_errno($curlResource);
        if($curlErrorNo)
        {
            throw new SystemException(curl_error($curlResource),null,50012);
        }
        $key = (int)$curlResource;
        $this->responseHeader = $this->getHeader($this->responseHeaders[$key]);
        curl_close($curlResource);
        if(isset($urlStruct['fileResource']) && is_resource($urlStruct['fileResource']))
        {
            fclose($urlStruct['fileResource']);
        }
        unset($this->responseHeaders[$key]);
    }
}