<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 3:33 PM
 */

namespace App\Models;

/**
 * 用户登陆记录
 * Class AdminUser
 * @package App\Models
 */
class LoginLog extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '下线'
    ];

    protected $table = 'login_log';
    protected $guarded = ['id'];

}
