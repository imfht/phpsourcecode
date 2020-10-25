<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace util;

use JPush\Client as JPush;

/**
 * 极光推送
 * @author 牧羊人
 * @date 2019/11/23
 * Class JPush
 * @package util
 */
class JPushNotice
{
    // KEY值
    private $appKey;
    // 密钥
    private $masterSecret;

    /**
     * 构造方法
     * JPush constructor.
     */
    public function __construct($appKey, $masterSecret)
    {
        $this->appKey = $appKey;
        $this->masterSecret = $masterSecret;
    }

    /**
     * 推送所有人(广播推送)
     * @param $content 推送内容
     * @param $type 推送类型：1带标题推送 2普通推送
     * @param string $title 推送标题
     * @param array $extras 自定义参数
     * @return bool
     * @author 牧羊人
     * @date 2019/11/23
     */
    public function sendNotifyAll($content, $type, $title = "", $extras = [])
    {
        $client = $client = new JPush($this->appKey, $this->masterSecret);
        if ($type == 1) {
            // 带标题的封装
            $result = $client->push()
                ->setPlatform(array('ios', 'android')) // 推送的接收平台
                ->addAllAudience() // 给所有人
//                ->setNotificationAlert('Hello, JPush')
                ->iosNotification(
                    [
                        "title" => $title,
//                        "subtitle" => "JPush Subtitle" ,
                        "body" => $content
                    ],
                    [
                        'sound' => 'sound',
                        'badge' => 1,
                        'extras' => $extras,
                    ]
                )
                ->androidNotification($title, [
                    'title' => $content,
                    'extras' => $extras
                ])
                ->options(array(
                    'apns_production' => false, // 测试环境
                ))
                ->send();
        } else {
            // 简单的推送(类似招商银行通知)
            $result = $client->push()
                ->setPlatform(array('ios', 'android'))// 推送的接收平台
                ->addAllAudience() // 给所有人
                ->setNotificationAlert($content)
                ->options(array(
                    'apns_production' => false, // 测试环境
                ))
                ->send();
        }
        if (isset($result['http_code']) && $result['http_code'] == 200) {
            return true;
        }
        return false;
    }

    /**
     * 指定设备推送
     * @param $content 推送内容
     * @param $type 推送类型：1复杂推送 2简单推送
     * @param $alias 推送别名
     * @param string $title 推送标题
     * @param array $extras 自定义参数
     * @return array
     * @author 牧羊人
     * @date 2019/11/23
     */
    public function sendNotifySpecial($content, $type, $alias, $title = "", $extras = [])
    {
        $client = new JPush($this->appKey, $this->masterSecret);
        if ($type == 1) {
            // 带标题的封装
            $result = $client->push()
                ->setPlatform(array('ios', 'android')) // 推送的接收平台
                ->addAlias($alias)// 别名推送
                ->iosNotification(
                    [
                        "title" => $title,
//                        "subtitle" => "JPush Subtitle" ,
                        "body" => $content
                    ],
                    [
                        'sound' => 'sound',
                        'badge' => 1,
                        'extras' => $extras,
                    ]
                )
                ->androidNotification($title, [
                    'title' => $content,
                    'extras' => $extras
                ])
                ->options(array(
                    'apns_production' => false, // 测试环境
                ))
                ->send();

        } else {
            // 简单的推送(类似招商银行通知)
            $result = $client->push()
                ->setPlatform(array('ios', 'android'))// 推送的接收平台
                ->addAlias($alias)// 别名推送
                ->setNotificationAlert($content)
                ->options(array(
                    'apns_production' => false, // 测试环境
                ))
                ->send();
        }
        if (isset($result['http_code']) && $result['http_code'] == 200) {
            return true;
        }
        return false;
    }
}
