<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

/**
 * 会员资料
 * Class MemberProfile
 * @package App\Models
 */
class MemberProfile extends BaseModels
{
    //性别
    const SEX_UNKNOWN = 0;
    const SEX_BOY = 1;
    const SEX_GIRL = 2;

    const SEX_DESC = [
        self::SEX_UNKNOWN => '未知',
        self::SEX_BOY => '男',
        self::SEX_GIRL => '女',
    ];

    protected $table = 'member_profile';
    protected $guarded = [];

    public $timestamps = false;


}
