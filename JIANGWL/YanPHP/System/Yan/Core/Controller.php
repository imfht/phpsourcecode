<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 */

namespace Yan\Core;


class Controller
{

    /**
     * @var array
     */
    protected $_class = array();

    /**
     * loaded class
     * @var array
     */
    protected $_loaded = array();

    /**
     * to store the loaded models
     * @var array
     */
    protected $_models = array();

    /**
     * Library path according to configuration
     * @var string
     */
    protected $_libraryPath;

    /**
     * Model path according to configuration
     * @var string
     */
    protected $_modelPath;

    public function __construct()
    {
        Log::debug('Init Controller ' . static::class);
    }


    protected function succ(string $msg = '', array $data = [])
    {
        return genResult(ReturnCode::OK, $msg, $data);
    }

    protected function fail(int $code, string $msg = '', array $data = [])
    {
        return genResult($code, $msg, $data);
    }
}