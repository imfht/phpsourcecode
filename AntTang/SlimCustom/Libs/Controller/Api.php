<?php
/**
 * @package     Api.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年6月5日
 */
namespace SlimCustom\Libs\Controller;

/**
 * Restful Api
 *
 * @author Jing <tangjing3321@gmail.com>
 */
class Api extends Controller
{

    /**
     * 允许响应的contenTypes
     *
     * @var array
     */
    protected static $allowedContentTypes = [
        'application/json'
        // 'application/xml',
    ];

    /**
     * 默认响应 application/json
     *
     * @var string
     */
    protected static $defaultContentType = 'application/json';

    /**
     * 基础版本标识
     * 
     * @var string
     */
    protected static $baseVersion = 'V1';

    /**
     * 版本前缀
     * 
     * @var string
     */
    protected static $versionPrefix = 'V';

    /**
     * Api 初始化
     */
    public function __construct()
    {
        parent::__construct();
        static::initApiContentType();
    }

    /**
     * 初始化Api响应设置
     * 
     * @return \SlimCustom\Libs\Http\Response
     */
    public static function initApiContentType()
    {
        response()->setAllowedContentTypes(static::$allowedContentTypes)->setDefaultContentType(static::$defaultContentType);
        return true;
    }

    /**
     * 返回基础版本标识
     * 
     * @return string
     */
    public static function baseVersion()
    {
        return static::$baseVersion;
    }

    /**
     * 返回版本前缀标识
     * 
     * @return string
     */
    public static function versionPrefix()
    {
        return static::$versionPrefix;
    }
}