<?php


namespace App\Library\Helper;


class Singleton
{
    private static $instance;

    /**
     * private 禁止外部实例化
     */
    private function __construct()
    {
    }

    /**
     * private 禁止外部克隆
     */
    private function __clone()
    {
    }

    /**
     * 返回唯一实例
     * @return Singleton
     */
    public static function getOneInstance(): Singleton
    {
        $result = self::$instance;

        if (!(self::$instance instanceof Singleton)) {
            self::$instance = new Singleton();
        }

        return $result;
    }
}