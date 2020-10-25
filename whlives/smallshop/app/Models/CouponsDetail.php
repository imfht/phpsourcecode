<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;
use Illuminate\Support\Str;

/**
 * 优惠券明细
 * Class Payment
 * @package App\Models
 */
class CouponsDetail extends BaseModels
{
    //状态
    const STATUS_OFF = 0;//未禁用
    const STATUS_ON = 1;//已禁用

    const STATUS_DESC = [
        self::STATUS_OFF => '未禁用',
        self::STATUS_ON => '已禁用'
    ];

    //是否使用
    const USE_OFF = 0;//未使用
    const USE_ON = 1;//已使用

    const USE_DESC = [
        self::USE_OFF => '未使用',
        self::USE_ON => '已使用',
    ];

    protected $table = 'coupons_detail';
    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * 生成优惠券
     * @param array $coupons 优惠券信息
     * @param int $num 生成数量
     * @param int $m_id 绑定用户
     * @return bool
     */
    public static function generate( array $coupons, int $num = 1, int $m_id = 0) {
        if ($num) {
            //已经过期的或者状态不对的
            if ($coupons['end_at'] < get_date() || $coupons['status'] != Coupons::STATUS_ON) {
                return false;
            }
            //开始生成
            $insert_data = array();
            for ($i = 1; $i <= $num; $i++) {
                $code   = strto32(md5(time() . $coupons['id'] . Str::random(15)));
                $insert = array(
                    'coupons_id'   => $coupons['id'],
                    'code' => $code,
                    'start_at' => $coupons['start_at'],
                    'end_at' => $coupons['end_at']
                );
                if ($m_id) {
                    $insert['m_id']    = $m_id;
                    $insert['bind_at'] = get_date();
                }

                $insert_data[] = $insert;
            }
            //增加数据
            if ($insert_data) {
                try {
                    self::insert($insert_data);
                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * 发给用户
     * @param int $m_id 用户id
     * @param int $coupons_id 优惠券id
     */
    public static function obtain( int $m_id, int $coupons_id)
    {
        if ($m_id && $coupons_id) {
            //查询优惠券
            $coupons = Coupons::find($coupons_id);
            if (!$coupons || $coupons['status'] == Coupons::STATUS_OFF) {
                return __('api.coupons_not_exists');
            } elseif ($coupons['end_at'] < get_date()) {
                return __('api.coupons_overdue');
            }
            //查询已经领取的数量
            $obtain_num = self::where(['m_id' => $m_id, 'coupons_id' => $coupons_id])->count();
            if ($obtain_num < $coupons['limit']) {
                //开始领取优惠券
                $res = self::generate($coupons->toArray(), 1, $m_id);
                if ($res) {
                    return true;
                } else {
                    api_error(__('api.fail'));
                }
            } else {
                return __('api.coupons_obtain_max');
            }
        }
    }

}
