<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap;

/**
 * Ap class file
 * ap包中类管理器
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Ap.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
class Ap
{
    /**
     * @var string 当前的版本号
     */
    protected static $_version = '1.0.0';

    /**
     * @var string 项目编码
     */
    protected static $_encoding = 'UTF-8';

    /**
     * @var string 项目语言种类
     */
    protected static $_languageType = 'zh-CN';

    /**
     * @var instance of tfc\ap\HttpRequest
     */
    protected static $_request = null;

    /**
     * @var instance of tfc\ap\HttpResponse
     */
    protected static $_response = null;

    /**
     * @var instance of tfc\ap\HttpSession
     */
    protected static $_session = null;

    /**
     * 获取当前的版本号
     * @return string
     */
    public static function getVersion()
    {
        return self::$_version;
    }

    /**
     * 设置当前的版本号
     * @param string $version
     * @return void
     */
    public static function setVersion($version)
    {
        self::$_version = (string) $version;
    }

    /**
     * 获取项目编码
     * @return string
     */
    public static function getEncoding()
    {
        return self::$_encoding;
    }

    /**
     * 设置项目编码
     * @param string $encoding
     * @return void
     */
    public static function setEncoding($encoding)
    {
        self::$_encoding = (string) $encoding;
    }

    /**
     * 获取项目语言种类
     * @return string
     */
    public static function getLanguageType()
    {
        return self::$_languageType;
    }

    /**
     * 设置项目语言种类
     * @param string $languageType
     * @return void
     */
    public static function setLanguageType($languageType)
    {
        self::$_languageType = (string) $languageType;
    }

    /**
     * 获取请求模式处理类
     * @return \tfc\ap\HttpRequest
     */
    public static function getRequest()
    {
        if (self::$_request === null) {
            self::setRequest();
        }

        return self::$_request;
    }

    /**
     * 设置请求模式处理类
     * @param \tfc\ap\HttpRequest $request
     * @return void
     */
    public static function setRequest(HttpRequest $request = null)
    {
        if ($request === null) {
            $request = new HttpRequest();
        }

        self::$_request = $request;
    }

    /**
     * 获取响应模式发送类
     * @return \tfc\ap\HttpResponse
     */
    public static function getResponse()
    {
        if (self::$_response === null) {
            self::setResponse();
        }

        return self::$_response;
    }

    /**
     * 设置响应模式发送类
     * @param \tfc\ap\HttpResponse $response
     * @return void
     */
    public static function setResponse(HttpResponse $response = null)
    {
        if ($response === null) {
            $response = new HttpResponse();
        }

        self::$_response = $response;
    }

    /**
     * 获取HTTP会话管理类
     * @return \tfc\ap\HttpSession
     */
    public static function getSession()
    {
        if (self::$_session === null) {
            self::setSession();
        }

        return self::$_session;
    }

    /**
     * 设置HTTP会话管理类
     * @param \tfc\ap\HttpSession $session
     * @return void
     */
    public static function setSession(HttpSession $session = null)
    {
        if ($session === null) {
            $session = new HttpSession();
        }

        $session->open();
        self::$_session = $session;
    }
}
