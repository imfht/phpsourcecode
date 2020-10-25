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
 * 商家
 * Class Member
 * @package App\Models
 */
class Seller extends BaseModels
{
    use SoftDeletes;
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;
    const STATUS_PENDING = 2;
    const STATUS_REFUSED = 3;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定',
        self::STATUS_PENDING => '待审',
        self::STATUS_REFUSED => '拒绝'
    ];

    //开发票
    const INVOICE_OFF = 0;
    const INVOICE_ON = 1;

    const INVOICE_DESC = [
        self::INVOICE_ON => '是',
        self::INVOICE_OFF => '否'
    ];

    protected $table = 'seller';
    protected $guarded = ['id'];
    protected $hidden = ['password', 'api_token', 'deleted_at'];

    protected $dates = ['deleted_at'];

    /**
     * 获取商家资料
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne('App\Models\SellerProfile');
    }

    /**
     * 保存数据
     * @param array $seller_data 主表数据
     * @param array $profile_data 附属表数据
     * @param int $id
     * @return bool|mixed
     */
    public static function saveData($seller_data, $profile_data, $id = '')
    {
        if (!$seller_data) return false;
        try {
            $res = DB::transaction(function () use ($id, $seller_data, $profile_data) {
                if (isset($seller_data['password'])) {
                    $seller_data['password'] = Hash::make($seller_data['password']);
                }
                if ($id) {
                    $res = self::where('id', $id)->update($seller_data);
                    SellerProfile::where('seller_id', $id)->update($profile_data);
                } else {
                    $result = self::create($seller_data);
                    $res = $result->id;
                    $profile_data['seller_id'] = $res;
                    SellerProfile::create($profile_data);
                }
                return $res;
            });
        } catch (\Exception $e) {
            $res = false;
        }
        return $res;
    }
}
