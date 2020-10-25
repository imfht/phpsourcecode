<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

/**
 * 商家资料
 * Class MemberProfile
 * @package App\Models
 */
class SellerProfile extends BaseModels
{
    //性别
    const SEX_UNKNOWN = 0;
    const SEX_BOY = 1;
    const SEX_GIRL = 2;

    protected $table = 'seller_profile';
    protected $guarded = [];

    public $timestamps = false;


}
