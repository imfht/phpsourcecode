<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-10 08:57:16
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-10 11:02:58
 */

namespace common\models\enums;

use yii2mod\enum\helpers\BaseEnum;

class MessageStatus extends BaseEnum
{
    // 订单消息
    const ORDER = 0;

    // 到期消息
    const EXPIRE = 1;

    // 注册提醒

    const SIGNUP = 2;
    // 工单提醒
    const WORK = 3;

    // 系统更新

    const SYSTEM = 4;

    // 官方动态
    const OFFICIAL = 5;

    public static $messageCategory = 'app';

    /**
     * @var array
     */
    public static $list = [
        self::ORDER => '订单提醒',
        self::EXPIRE => '到期提醒',
        self::SIGNUP => '注册消息',
        self::WORK => '工单消息',
        self::SYSTEM => '系统消息',
        self::OFFICIAL => '官方动态',
    ];
}
