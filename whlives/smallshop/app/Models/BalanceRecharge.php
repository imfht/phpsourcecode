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
 * 充值
 * Class Payment
 * @package App\Models
 */
class BalanceRecharge extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '成功',
        self::STATUS_OFF => '未成功'
    ];

    //风险订单提示
    const FLAG_NO = 0;
    const FLAG_YES = 1;
    const FLAG_DESC = [
        self::FLAG_NO => '正常',
        self::FLAG_YES => '风险'
    ];

    protected $table = 'balance_recharge';
    protected $guarded = ['id'];

    /**
     * 生成交易单号
     * @return string
     */
    public static function getRechargeNo()
    {
        $recharge_no = date('YmdHis', time()) . rand(100000, 999999);
        return $recharge_no;
    }

    /**
     * 余额充值成功处理
     * @param $notify_data
     * @return bool
     */
    public static function updatePayStatus($notify_data) {
        if (!$notify_data) {
            return false;
        }
        $recharge_no = $notify_data['order_no'];
        $recharge = BalanceRecharge::where('recharge_no', $recharge_no)->first();
        if (!$recharge) {
            return false;
        }
        if ($recharge['status'] == BalanceRecharge::STATUS_OFF) {
            $recharge_update = [
                'payment_id' => $notify_data['payment_id'],
                'payment_no' => $notify_data['payment_no'],
                'flag' => $notify_data['flag'],
                'status' => BalanceRecharge::STATUS_ON
            ];
            try {
                DB::transaction(function () use ($notify_data, $recharge, $recharge_update) {
                    BalanceRecharge::where(['recharge_no' => $notify_data['order_no'], 'status' => BalanceRecharge::STATUS_OFF])->update($recharge_update);
                    if ($recharge_update['flag'] == BalanceRecharge::FLAG_NO) {
                        Balance::updateAmount($recharge['m_id'], $recharge['amount'], BalanceDetail::EVENT_RECHARGE, $recharge['recharge_no'], '充值');
                    }
                });
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return true;
        }
    }

}
