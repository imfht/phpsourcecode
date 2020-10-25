<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-9
 * Time: 下午7:04
 */
namespace Helper;

use DateTime;
use DateTimeZone;
use MongoDate;

class MongoHelper
{
    /**
     * 新建一个 MongoDate （MongoDB的日期对象）
     * @param $dateString
     * @return MongoDate
     */
    public static function buildMongoDate($dateString)
    {
        $dt = new DateTime(date($dateString), new DateTimeZone('UTC'));
        $ts = $dt->getTimestamp();

        return new MongoDate($ts);
    }
}