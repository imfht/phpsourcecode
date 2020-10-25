<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;
use Illuminate\Support\Facades\DB;

/**
 * 商家余额
 * Class Payment
 * @package App\Models
 */
class SellerBalance extends BaseModels
{

    protected $table = 'seller_balance';
    protected $guarded = ['id'];

    /**
     * 修改商家现金账户并记录详情
     * @param int $m_id 用户id
     * @param $amount 金额
     * @param int $event 类型
     * @param string $detail_no 单号
     * @param string $note 备注
     * @return bool|mixed
     */
    public static function updateAmount(int $m_id, float $amount, int $event, $detail_no = '', $note = '') {
        $return = array(
            'status' => false,
            'message' => __('api.fail')
        );
        if (!$m_id || !$amount || !$event) {
            $return['message'] = __('api.missing_params');
            return $return;
        }

        if (!isset(SellerBalanceDetail::EVENT_DESC[$event])) {
            $return['message'] = __('api.balance_event_error');
            return $return;
        }
        //变动详情
        $detail = array(
            'm_id' => $m_id,
            'type' => $amount >= 0 ? SellerBalanceDetail::TYPE_INCR : SellerBalanceDetail::TYPE_RECR,
            'event' => $event,
            'amount' => abs($amount),
            'balance' => 0,
            'detail_no' => $detail_no,
            'note' => $note
        );

        try {
            $res = DB::transaction(function () use ($m_id, $amount, $detail) {
                $res_data = self::where('m_id', $m_id)->lockForUpdate()->first();
                if ($amount < 0 && (!isset($res_data['amount']) || ($res_data['amount'] + $amount) < 0)) {
                    $message = __('api.balance_insufficient');
                    return $message;
                } else {
                    //数据存在的时候直接修改
                    if ($res_data) {
                        $where[] = ['m_id', $m_id];
                        //减少的时候加上条件
                        if ($amount < 0) {
                            $where[] = ['amount', '>=', abs($amount)];
                        }
                        $res = self::where($where)->increment('amount', $amount);
                    } else {
                        $res_data['amount'] = 0;
                        $result = self::create(['m_id' => $m_id, 'amount' => $amount]);
                        $res = $result->id;
                    }
                    $detail['balance'] = $res_data['amount'] + $amount;
                    SellerBalanceDetail::create($detail);
                    if ($res) {
                        return true;
                    }
                }
            });
        } catch (\Exception $e) {
            $res = false;
        }
        if ($res === true) {
            $return['status'] = true;
            $return['message'] = '';
            return $return;
        }
        if ($res) {
            $return['message'] = $res;
        }
        return $return;
    }
}
