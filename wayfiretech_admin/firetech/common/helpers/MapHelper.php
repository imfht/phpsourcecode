<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-24 20:13:05
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-03 11:28:28
 */


namespace common\helpers;

use common\models\DdRegion;
use Yii;
use yii\base\Model;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class MapHelper extends BaseObject
{
    /**
     * 求两个已知经纬度之间的距离,单位为米
     * 
     * @param lng1 $ ,lng2 经度
     * @param lat1 $ ,lat2 纬度
     * @return float 距离，单位米
     * @author www.Alixixi.com 
     */
    public static function getdistance($lng1, $lat1, $lng2, $lat2)
    {
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    }

    // 根据经纬度计算最近的地理位置数据
    public static function distance($lng, $lat)
    {
        $files = " ROUND(
            6378.138 * 2 * ASIN(
                SQRT(
                    POW(
                        SIN(
                            (
                                {$lat} * PI() / 180 - lat * PI() / 180
                            ) / 2
                        ),
                        2
                    ) + COS(40.0497810000 * PI() / 180) * COS(lat * PI() / 180) * POW(
                        SIN(
                            (
                                {$lng} * PI() / 180 - lng * PI() / 180
                            ) / 2
                        ),
                        2
                    )
                )
            ) * 1000
        ) AS juli";
        $DdRegion = new DdRegion();
        // 区县
        $region = $DdRegion->find()->where(['level' => 3])->select(['id', 'name', 'pid', $files])->orderBy('juli')->asArray()->one();
        $city_id = $region['pid'];
        $region_id = $region['id'];
        // 城市
        $city = $DdRegion->findOne(['id' => $city_id]);
        // 省份
        $province = $DdRegion->findOne(['id' => $city['pid']]);
        $province_id = $province['id'];
        return [
            $province_id => $province['name'],
            $city_id => $city['name'],
            $region_id => $region['name']
        ];
    }

    public static  function real_ip()
{
    static $realip = NULL;
    if ($realip !== NULL) {
        return $realip;
    }
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($arr as $ip) {
                $ip = trim($ip);
                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = '0.0.0.0';
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    return $realip;
}

public static function get_client_ip()
{
    $ip = null;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim(current($ip));
    }
    return $ip;
}

}
