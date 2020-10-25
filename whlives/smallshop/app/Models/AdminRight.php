<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 3:33 PM
 */

namespace App\Models;

/**
 * 管理员权限码
 * Class AdminUser
 * @package App\Models
 */
class AdminRight extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    protected $table = 'admin_right';
    protected $guarded = ['id'];

}
