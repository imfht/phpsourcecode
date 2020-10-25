<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Form;
\app()->import('Gregwar\\Captcha', VENDOR_ROOT);

use \Gregwar\Captcha\CaptchaBuilder;
use \Cute\Utility\Word;


/**
 * 验证码
 */
class Captcha
{
    const ENCRYPT_SALT = 'captcha_code_salt';
    const FILENAME_PREFIX = 'cc_';
    public static $font = null;
    public static $finger_print = null;
    protected $builder = null;
    protected $phrase_size = 6;
    protected $width = 80;
    protected $height = 30;

    /**
     * 构造函数
     */
    public function __construct($phrase = '', $phrase_size = 6,
                                $width = 80, $height = 30)
    {
        $this->builder = new CaptchaBuilder();
        if (!empty($phrase)) {
            $this->phrase_size = strlen($phrase);
            $this->builder->setPhrase($phrase);
        } else {
            $this->refresh($phrase_size);
        }
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * 更换phrase
     */
    public function refresh($phrase_size = 0)
    {
        if ($phrase_size) {
            $this->phrase_size = $phrase_size;
        }
        $phrase = Word::randString($this->phrase_size);
        $this->builder->setPhrase($phrase);
        return $this;
    }

    /**
     * 设置图片格式
     */
    public function build(array $args = [])
    {
        $origin = [
            $this->width, $this->height,
            self::$font, self::$finger_print,
        ];
        //$args后面缺少的元素使用$origin的元素补齐
        exec_method_array($this->builder, 'build', $args + $origin);
        return $this->builder;
    }

    /**
     * 展示验证码用于同源
     */
    public function show($name = 'phrase', $inline = true)
    {
        $build_args = array_slice(func_get_args(), 2);
        @session_start();
        $phrase = $this->builder->getPhrase();
        $_SESSION[$name] = self::encrypt($phrase);
        $this->build($build_args);
        if ($inline) {
            return $this->builder->inline();
        } else {
            @header('Content-Type: image/jpeg');
            return $this->builder->output();
        }
    }

    /**
     * 保存验证码用于跨域，提供跳转网址
     */
    public function save($savedir, $saveurl = '/captcha')
    {
        $build_args = array_slice(func_get_args(), 2);
        self::clean($savedir, 0.3, 60);
        $filename = uniqid(self::FILENAME_PREFIX) . '.jpg';
        $this->build($build_args);
        $this->builder->save($savedir . '/' . $filename);
        $phrase = $this->builder->getPhrase();
        $url = rtrim($saveurl, '/') . '/' . $filename;
        $url .= '#' . self::encrypt($phrase);
        die($url);
    }

    /**
     * 加密验证码文本
     * @param string $phrase 验证码文本
     * @return string 哈希后的验证码
     */
    public static function encrypt($phrase)
    {
        return md5(strtolower($phrase) . self::ENCRYPT_SALT);
    }

    /**
     * 清理旧的验证码图片文件
     * @param string $dir 验证码目录
     * @param float $freq 频率，大于等于1时每次都删除
     * @param int $limit 最近一段时间的文件不要清理，单位：秒
     */
    public static function clean($dir, $freq = 0.3, $limit = 60)
    {
        $rand = mt_rand(1, 10000) / 10000;
        if ($freq <= 0 || $freq >= 1 || $rand <= $freq) { // 命中概率
            $limit_time = time() - $limit;
            $files = glob($dir . 'cc_*.jpg');
            foreach ($files as $file) { //清理旧的图片文件
                if (fileatime($file) < $limit_time) {
                    unlink($file);
                }
            }
        }
    }
}
