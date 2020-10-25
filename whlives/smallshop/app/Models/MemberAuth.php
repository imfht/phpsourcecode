<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

/**
 * 第三方账号
 * Class Member
 * @package App\Models
 */
class MemberAuth extends BaseModels
{
    //状态
    const TYPE_QQ = 1;
    const TYPE_WECHAT = 2;
    const TYPE_WEIBO = 3;

    const TYPE_DESC = [
        self::TYPE_QQ => 'qq',
        self::TYPE_WECHAT => '微信',
        self::TYPE_WEIBO => '微博'
    ];

    protected $table = 'member_auth';
    protected $guarded = ['id'];
}
