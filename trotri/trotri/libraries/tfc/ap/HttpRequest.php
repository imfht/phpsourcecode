<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap;

/**
 * HttpRequest class file
 * HTTP请求模式处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: HttpRequest.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
class HttpRequest extends Request
{
    /**
     * @var string 脚本访问协议：http
     */
    const SCHEME_HTTP = 'http';

    /**
     * @var string 脚本访问协议：https
     */
    const SCHEME_HTTPS = 'https';

    /**
     * @var string 当前应用的路径
     */
    protected $_baseUrl;

    /**
     * @var string 当前脚本的路径
     */
    protected $_scriptUrl;

    /**
     * @var string 用来指定要访问的页面，如“/index.html”
     */
    protected $_requestUri;

    /**
     * @var string 包含由客户端提供的、跟在真实脚本名称之后并且在查询语句（query string）之前的路径信息
     * 如当前脚本是通过“http://example.com/path_info.php/some/stuff?foo=bar”被访问，那么该值将包含“/some/stuff”
     */
    protected $_pathInfo;

    /**
     * @var string 浏览当前页面的用户的IP地址
     */
    protected $_clientIp;

    /**
     * @var string 当前执行脚本的绝对路径，如果在命令行界面使用相对路径执行脚本，那么该值将包含用户指定的相对路径
     */
    protected $_basePath;

    /**
     * @var string 当前请求头中Host:项的内容
     */
    protected $_httpHost;

    /**
     * 获取当前应用的路径
     * @param boolean $absolute
     * @return string
     */
    public function getBaseUrl($absolute = false)
    {
        if ($this->_baseUrl === null) {
            $this->setBaseUrl();
        }

        return ($absolute ? ($this->getHttpHost() . $this->_baseUrl) : $this->_baseUrl);
    }

    /**
     * 设置当前应用的路径
     * @param string|null $baseUrl
     * @return \tfc\ap\HttpRequest
     */
    public function setBaseUrl($baseUrl = null)
    {
        if ($baseUrl !== null) {
            $this->_baseUrl = rtrim((string) $baseUrl, '\\/');
            return $this;
        }

        $this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');
        return $this;
    }

    /**
     * 获取当前脚本的路径
     * @return string
     */
    public function getScriptUrl()
    {
        if ($this->_scriptUrl === null) {
            $this->setScriptUrl();
        }

        return $this->_scriptUrl;
    }

    /**
     * 设置当前脚本的路径
     * @param string|null $scriptUrl
     * @return \tfc\ap\HttpRequest
     * @throws InvalidArgumentException 如果不能获取当前脚本的路径，抛出异常
     */
    public function setScriptUrl($scriptUrl = null)
    {
        if ($scriptUrl !== null) {
            $this->_scriptUrl = (string) $scriptUrl;
            return $this;
        }

        $scriptName = isset($_SERVER['SCRIPT_FILENAME']) ? basename($_SERVER['SCRIPT_FILENAME']) : '';
        if (isset($_SERVER['SCRIPT_NAME']) && (basename($_SERVER['SCRIPT_NAME']) === $scriptName)) {
            $this->_scriptUrl = $_SERVER['SCRIPT_NAME'];
        } elseif (isset($_SERVER['PHP_SELF']) && (basename($_SERVER['PHP_SELF']) === $scriptName)) {
            $this->_scriptUrl = $_SERVER['PHP_SELF'];
        } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && (basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName)) {
            $this->_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
        } elseif (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
            $this->_scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
        } elseif (isset($_SERVER['DOCUMENT_ROOT']) && (strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0)) {
            $this->_scriptUrl = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
        } else {
            throw new InvalidArgumentException(
                'HttpRequest is unable to determine the entry script URL.'
            );
        }

        return $this;
    }

    /**
     * 获取要访问的页面名
     * @return string
     */
    public function getRequestUri()
    {
        if ($this->_requestUri === null) {
            $this->setRequestUri();
        }

        return $this->_requestUri;
    }

    /**
     * 设置要访问的页面名
     * @param string|null $requestUri
     * @return \tfc\ap\HttpRequest
     * @throws InvalidArgumentException 如果不能获取要访问的页面名，抛出异常
     */
    public function setRequestUri($requestUri = null)
    {
        if ($requestUri !== null) {
            $this->_requestUri = (string) $requestUri;
            return $this;
        }

        // IIS
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $this->_requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        }
        elseif (isset($_SERVER['REQUEST_URI'])) {
            $this->_requestUri = $_SERVER['REQUEST_URI'];
            if (!empty($_SERVER['HTTP_HOST'])) {
                if (strpos($this->_requestUri, $_SERVER['HTTP_HOST']) !== false) {
                    $this->_requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->_requestUri);
                }
            }
            else {
                $this->_requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $this->_requestUri);
            }
        }
        // IIS 5.0 CGI
        elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            $this->_requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $this->_requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        else {
            throw new InvalidArgumentException(
                'HttpRequest is unable to determine the request URI.'
            );
        }

        return $this;
    }

    /**
     * 获取由客户端提供的、跟在真实脚本名称之后并且在查询语句（query string）之前的路径信息
     * 如当前脚本是通过“http://example.com/path_info.php/some/stuff?foo=bar”被访问，那么该值将包含“/some/stuff”
     * @return string
     */
    public function getPathInfo()
    {
        if ($this->_pathInfo === null) {
            $this->setPathInfo();
        }

        return $this->_pathInfo;
    }

    /**
     * 设置由客户端提供的、跟在真实脚本名称之后并且在查询语句（query string）之前的路径信息
     * @param string|null $pathInfo
     * @return \tfc\ap\HttpRequest
     * @throws InvalidArgumentException 如果不能获取路径信息，抛出异常
     */
    public function setPathInfo($pathInfo = null)
    {
        if ($pathInfo !== null) {
            $this->_pathInfo = trim((string) $pathInfo, '/');
            return $this;
        }

        $pathInfo = $this->getRequestUri();
        if (($pos = strpos($pathInfo, '?')) !== false) {
           $pathInfo = substr($pathInfo, 0, $pos);
        }

        $pathInfo = $this->decodePathInfo($pathInfo);
        $scriptUrl = $this->getScriptUrl();
        $baseUrl = $this->getBaseUrl();
        if (strpos($pathInfo, $scriptUrl) === 0) {
            $pathInfo = substr($pathInfo, strlen($scriptUrl));
        } elseif ($baseUrl === '' || strpos($pathInfo, $baseUrl) === 0) {
            $pathInfo = substr($pathInfo, strlen($baseUrl));
        } elseif (strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0) {
            $pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
        } else {
            throw new InvalidArgumentException(
                'HttpRequest is unable to determine the path info of the request.'
            );
        }

        $this->_pathInfo = trim($pathInfo, '/');
        return $this;
    }

    /**
     * 获取浏览当前页面的用户的IP地址
     * @return string
     */
    public function getClientIp()
    {
        if ($this->_clientIp === null) {
            $this->setClientIp();
        }

        return $this->_clientIp;
    }

    /**
     * 设置当前页面的用户的IP地址
     * @param string|null $clientIp
     * @param boolean $directOnly
     * @return \tfc\ap\HttpRequest
     */
    public function setClientIp($clientIp = null, $directOnly = true)
    {
        if ($clientIp !== null) {
            $this->_clientIp = (string) $clientIp;
            return $this;
        }

        $directIp = '';
        // gets the default ip sent by the user
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $directIp = $_SERVER['REMOTE_ADDR'];
            if ($directOnly) {
                $this->_clientIp = $directIp;
                return $this;
            }
        }

        // gets the proxy ip sent by the user
        $proxyIp = '';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $proxyIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $proxyIp = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $proxyIp = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $proxyIp = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_VIA'])) {
            $proxyIp = $_SERVER['HTTP_VIA'];
        } elseif (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
            $proxyIp = $_SERVER['HTTP_X_COMING_FROM'];
        } elseif (!empty($_SERVER['HTTP_COMING_FROM'])) {
            $proxyIp = $_SERVER['HTTP_COMING_FROM'];
        }

        // returns the true IP if it has been found, else false
        // true IP without proxy
        if (empty($proxyIp)) {
            $this->_clientIp = $directIp;
        }
        else {
            $isIp = preg_match('|^([0-9]{1,3}\.){3,3}[0-9]{1,3}|', $proxyIp, $regs);
            // true IP behind a proxy
            if ($isIp && (count($regs) > 0)) {
                $this->_clientIp = $regs[0];
            }
            // can't define IP: there is a proxy but we don't have
            // information about the true IP
            else {
                $this->_clientIp = $directIp;
            }
        }

        return $this;
    }

    /**
     * 获取当前执行脚本的绝对路径
     * 如果在命令行界面使用相对路径执行脚本，那么该值将包含用户指定的相对路径
     * @return string
     */
    public function getBasePath()
    {
        if ($this->_basePath === null) {
            $this->setBasePath();
        }

        return $this->_basePath;
    }

    /**
     * 设置当前执行脚本的绝对路径
     * 如果在命令行界面使用相对路径执行脚本，那么该值将包含用户指定的相对路径
     * @param string|null $basePath
     * @return \tfc\ap\HttpRequest
     */
    public function setBasePath($basePath = null)
    {
        if ($basePath !== null) {
            $this->basePath = rtrim((string) $basePath, '/');
            return $this;
        }

        $baseUrl = $this->getBaseUrl();
        // basename() matches the script filename; return the directory
        if ($baseUrl !== '' && (basename($baseUrl) === basename($_SERVER['SCRIPT_FILENAME']))) {
            $baseUrl = str_replace('\\', '/', dirname($baseUrl));
        }

        $this->basePath = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * 获取当前请求头中Host:项的内容
     * @return string
     */
    public function getHttpHost()
    {
        if ($this->_httpHost === null) {
            $this->setHttpHost();
        }

        return $this->_httpHost;
    }

    /**
     * 设置当前请求头中Host:项的内容
     * @param string|null $httpHost
     * @return \tfc\ap\HttpRequest
     */
    public function setHttpHost($httpHost = null)
    {
        if ($httpHost !== null) {
            $this->_httpHost = (string) $httpHost;
            return $this;
        }

        $httpHost = $this->getServer('HTTP_HOST');
        if (!empty($httpHost)) {
            $this->_httpHost = $httpHost;
            return $this;
        }

        $scheme = $this->getScheme();
        $name   = $this->getServer('SERVER_NAME');
        $port   = $this->getServer('SERVER_PORT');
        if (($scheme == self::SCHEME_HTTP && $port == 80) || ($scheme == self::SCHEME_HTTPS && $port == 443)) {
            $this->_httpHost = $name;
        }
        else {
            $this->_httpHost = $name . ':' . $port;
        }

        return $this;
    }

    /**
     * 获取脚本访问协议，https或http
     * @return string
     */
    public function getScheme()
    {
        return ($this->getServer('HTTPS') == 'on') ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
    }

    /**
     * 编码路径信息
     * @param string $pathInfo
     * @return string
     */
    protected function decodePathInfo($pathInfo)
    {
        $pathInfo = urldecode($pathInfo);

        // is it UTF-8?
        // http://w3.org/International/questions/qa-forms-utf-8.html
        if(preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E]              # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
        )*$%xs', $pathInfo)) {
            return $pathInfo;
        }
        else {
            return utf8_encode($pathInfo);
        }
    }
}
