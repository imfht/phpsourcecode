<?php
/**
 * Created by PhpStorm.
 * User: china_wangyu@aliyun.com
 * Date: 2018/6/5
 * Time: 10:14
 *  *  *  *  ** 求职区 **
 *  期望城市： 成都
 *  期望薪资： 12k
 *
 *  个人信息
 *
 *  工作经验: 3年
 *  开发语言: PHP / Python
 *
 *  联系方式：china_wangyu@aliyun.com
 */

namespace aliyuns\amts;

include_once __DIR__.'/../aliyun-php-sdk-core/Config.php';

use aliyuns\Json;

/**
 * Class AmtsClient 请求连接类
 * @package aliyun\amts
 * @authors china_wangyu (china_wangyu@aliyun.com)
 * @date    2018-06-5 16:45:19
 * @version 1.0.2
 */
class AmtsClient extends \DefaultAcsClient
{
    /**
     * 实例化 \ DefaultAcsClient对象
     * @var array
     */
    public static $instance = [];
    /**
     * 资源地址
     * @var
     */
    private static $mpsRegionId;
    /**
     * 阿里云授权 accessKeyId
     * @var
     */
    private static $accessKeyId;
    /**
     * 阿里云授权 accessKeySecret
     * @var
     */
    private static $accessKeySecret;

    /**
     * 实例化链接对象
     * @param $mps_region_id
     * @param $access_key_id
     * @param $access_key_secret
     * @return \DefaultAcsClient
     */
    public static function instance($mps_region_id, $access_key_id,$access_key_secret):parent
    {
        self::validate_param($mps_region_id,$access_key_id,$access_key_secret);
        return self::setSelf();
    }


    /**
     * 验证参数
     * @param $mps_region_id
     * @param $access_key_id
     * @param $access_key_secret
     */
    public static function validate_param($mps_region_id,$access_key_id,$access_key_secret)
    {
        empty($mps_region_id) && Json::error('mps_region_id is null');
        self::$mpsRegionId = $mps_region_id;
        empty($access_key_id) &&  Json::error('access_key_id is null');
        self::$accessKeyId = $access_key_id;
        empty($access_key_secret) && Json::error('access_key_secret is null');
        self::$accessKeySecret = $access_key_secret;
    }

    /**
     * 验证
     * @return \DefaultAcsClient
     */
    public static function setSelf():parent
    {
        if (isset(self::$instance)){
            # 创建DefaultAcsClient实例并初始化
            $clientProfile = \DefaultProfile::getProfile(
                self::$mpsRegionId,                   # 您的 Region ID
                self::$accessKeyId,                   # 您的 AccessKey ID
                self::$accessKeySecret                # 您的 AccessKey Secret
            );
            self::$instance = new parent($clientProfile);
        }
        return self::$instance;
    }
}