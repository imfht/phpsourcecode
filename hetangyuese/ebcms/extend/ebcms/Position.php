<?php
namespace ebcms;

class Position
{

    // 位置信息
    protected static $positions = [];

    public static function add($position)
    {
        if (!self::$positions) {
            array_push(self::$positions, ['title' => '首页', 'url' => \think\Url::build('index/index/index')]);
        }
        array_push(self::$positions, $position);
    }

    public static function get()
    {
        if (!self::$positions) {
            array_push(self::$positions, ['title' => '首页', 'url' => \think\Url::build('index/index/index')]);
        }
        return self::$positions;
    }

    public static function getLast()
    {
        if (!self::$positions) {
            array_push(self::$positions, ['title' => '首页', 'url' => \think\Url::build('index/index/index')]);
        }
        return end(self::$positions);
    }

}