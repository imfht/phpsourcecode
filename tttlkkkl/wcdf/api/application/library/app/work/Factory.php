<?php

/**
 *
 * Date: 17-5-6
 * Time: 下午7:08
 * author :李华 yehong0000@163.com
 */
namespace app\work;

use app\work\logic\Work;
use app\work\logic\WorkRecord;

class Factory
{
    public static function __callStatic($name, $arguments)
    {
        throw new \Exception('Bad Request', 400);
    }

    /**
     * 打卡
     *
     * @param $data
     */
    public static function postWork($data)
    {
        return WorkRecord::getInstance()->sign($data);
    }

    /**
     * 获取打卡记录
     *
     * @param $data
     *
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getWork($data)
    {
        return WorkRecord::getInstance()->getListByUser($data);
    }

    /**
     * 后台打卡详情
     *
     * @param $data
     *
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getWorkLists($data)
    {
        return WorkRecord::getInstance()->getListByWork($data);
    }

    /**
     * 获取打卡主记录
     *
     * @param $data
     */
    public static function getClock($data)
    {
        return Work::getInstance()->getList($data);
    }
}