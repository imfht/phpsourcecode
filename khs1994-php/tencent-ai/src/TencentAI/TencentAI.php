<?php

declare(strict_types=1);

namespace TencentAI;

use Curl\Curl;
use TencentAI\Kernel\Request;

/**
 * Class TencentAI.
 *
 * @version v18.06.08
 *
 * @method Audio audio()
 *
 * @property Audio                   audio
 *
 * @method Face face()
 *
 * @property Face                    face
 *
 * @method Image image()
 *
 * @property Image                   image
 *
 * @method NaturalLanguageProcessing nlp()
 *
 * @property NaturalLanguageProcessing nlp
 *
 * @method OCR ocr()
 *
 * @property OCR                     ocr
 *
 * @method Photo photo()
 *
 * @property Photo                   photo
 *
 * @method Translate translate()
 *
 * @property Translate               translate
 */
class TencentAI
{
    private const VERSION = '18.06.08';

    private static $tencentAI;

    private function __construct($appId,
                                 string $appKey,
                                 bool $jsonFormat = false,
                                 $timeout = 100,
                                 $retry = 1,
                                 bool $debug = false)
    {
        Request::setAppId($appId);
        Request::setAppKey($appKey);
        Request::setRetry($retry);
        Request::$debug = $debug;

        // default format is array
        $jsonFormat ? Request::setFormat('json') : Request::setFormat('array');

        Request::setCurl(new Curl(), (int) $timeout);
    }

    private function __clone()
    {
        // Private clone
    }

    /**
     * @param        $appId
     * @param string $appKey
     * @param bool   $jsonFormat
     * @param int    $timeout
     * @param int    $retry
     * @param bool   $debug
     *
     * @return TencentAI
     */
    public static function getInstance($appId,
                                       string $appKey,
                                       bool $jsonFormat = false,
                                       $timeout = 100,
                                       $retry = 1,
                                       bool $debug = false)
    {
        if (!(self::$tencentAI instanceof self)) {
            self::$tencentAI = new self($appId, $appKey, $jsonFormat, $timeout, $retry, $debug);
        }

        return self::$tencentAI;
    }

    /**
     * 返回对象
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        switch ($name) {
            case 'nlp':
                $service = '\\TencentAI\\NaturalLanguageProcessing';

                break;
            case 'ocr':
                $service = '\\TencentAI\\OCR';

                break;
            default:
                $service = '\\TencentAI\\'.ucfirst($name);
        }

        return new $service();
    }

    public function __get($name)
    {
        return $this->$name();
    }

    /**
     * 查看版本号.
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    public static function close(): void
    {
        Request::close();
    }
}
