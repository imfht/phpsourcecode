<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * 会员
 * Class Member
 * @package App\Models
 */
class Member extends BaseModels
{
    use SoftDeletes;
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    protected $table = 'member';
    protected $guarded = ['id'];
    protected $hidden = ['password', 'pay_password', 'deleted_at'];

    protected $dates = ['deleted_at'];

    /**
     * 获取会员资料
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne('App\Models\MemberProfile');
    }

    /**
     * 获取会员组
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group()
    {
        return $this->hasOne('App\Models\MemberGroup');
    }

    /**
     * 保存数据
     * @param array $member_data 主表数据
     * @param array $profile_data 附属表数据
     * @param int $id
     * @return bool|mixed
     */
    public static function saveData($member_data, $profile_data, $id = '')
    {
        if (!$member_data) return false;
        try {
            $res = DB::transaction(function () use ($id, $member_data, $profile_data) {
                if (isset($member_data['password'])) {
                    $member_data['password'] = Hash::make($member_data['password']);
                }
                if ($id) {
                    $res = self::where('id', $id)->update($member_data);
                    MemberProfile::where('member_id', $id)->update($profile_data);
                } else {
                    $result = self::create($member_data);
                    $res = $result->id;
                    $profile_data['member_id'] = $res;
                    MemberProfile::create($profile_data);
                }
                return $res;
            });
        } catch (\Exception $e) {
            $res = false;
        }
        return $res;
    }
}
