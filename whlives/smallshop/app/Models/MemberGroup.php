<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/14
 * Time: 下午1:11
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 会员组
 * Class MemberGroup
 * @package App\Models
 */
class MemberGroup extends BaseModels
{
    use SoftDeletes;
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    protected $table = 'member_group';
    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

}
