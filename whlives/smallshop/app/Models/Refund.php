<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;
use Illuminate\Support\Facades\DB;

/**
 * 售后
 * Class Article
 * @package App\Models
 */
class Refund extends BaseModels
{
    //状态
    const STATUS_WAIT_APPROVE = 0;
    const STATUS_REFUSED_APPROVE = 1;
    const STATUS_WAIT_DELIVERY = 2;
    const STATUS_RECEIVED = 3;
    const STATUS_REFUSED_RECEIVED = 4;
    const STATUS_WAIT_SELLER_DELIVERY = 5;
    const STATUS_WAIT_PAY = 6;
    const STATUS_DONE = 7;
    const STATUS_CUSTOMER_CANCEL = 8;

    const STATUS_DESC = [
        self::STATUS_WAIT_APPROVE => '待审核',
        self::STATUS_REFUSED_APPROVE => '审核拒绝',
        self::STATUS_WAIT_DELIVERY => '待买家退货',
        self::STATUS_RECEIVED => '待商家收货',
        self::STATUS_REFUSED_RECEIVED => '拒绝收货',
        self::STATUS_WAIT_SELLER_DELIVERY => '待卖家发货',
        self::STATUS_WAIT_PAY => '待退款',
        self::STATUS_DONE => '售后完成',
        self::STATUS_CUSTOMER_CANCEL => '买家撤销',
    ];

    //会员看到状态
    const STATUS_MEMBER_DESC = [
        self::STATUS_WAIT_APPROVE => '待审核',
        self::STATUS_REFUSED_APPROVE => '售后关闭',
        self::STATUS_WAIT_DELIVERY => '待退货',
        self::STATUS_RECEIVED => '待商家收货',
        self::STATUS_REFUSED_RECEIVED => '售后关闭',
        self::STATUS_WAIT_SELLER_DELIVERY => '待卖家发货',
        self::STATUS_WAIT_PAY => '待退款',
        self::STATUS_DONE => '售后完成',
        self::STATUS_CUSTOMER_CANCEL => '售后关闭',
    ];

    //商家看到状态
    const STATUS_SELLER_DESC = [
        self::STATUS_WAIT_APPROVE => '待审核',
        self::STATUS_REFUSED_APPROVE => '售后关闭',
        self::STATUS_WAIT_DELIVERY => '待买家退货',
        self::STATUS_RECEIVED => '待商家收货',
        self::STATUS_REFUSED_RECEIVED => '拒绝收货',
        self::STATUS_WAIT_SELLER_DELIVERY => '待卖家发货',
        self::STATUS_WAIT_PAY => '待退款',
        self::STATUS_DONE => '售后完成',
        self::STATUS_CUSTOMER_CANCEL => '售后关闭',
    ];

    //售后类型
    const REFUND_TYPE_MONEY = 1;
    const REFUND_TYPE_GOODS = 2;
    const REFUND_TYPE_REPLACE = 3;

    const REFUND_TYPE_DESC = [
        self::REFUND_TYPE_MONEY => '仅退款',
        self::REFUND_TYPE_GOODS => '退货退款',
        self::REFUND_TYPE_REPLACE => '换货',
    ];

    //售后理由1仅退款，2退货退款，3换货
    const REASON_DESC = [
        self::REFUND_TYPE_MONEY => [
            '1' => '不想要了',
            '2' => '买错了/订单信息错误',
            '3' => '未按约定时间发货',
            '4' => '缺货',
            '5' => '其他',
        ],
        self::REFUND_TYPE_GOODS => [
            '1' => '七天无理由退换货',
            '2' => '商品破损',
            '3' => '收到假货',
            '4' => '收到商品与实际不符',
            '5' => '商品质量问题',
            '6' => '物流太慢/未收到货',
            '7' => '发票问题',
            '8' => '其他',
        ],
        self::REFUND_TYPE_REPLACE => [
            '1' => '商品破损',
            '2' => '收到假货',
            '3' => '收到商品与实际不符',
            '4' => '商品质量问题',
            '5' => '其他',
        ]
    ];

    protected $table = 'refund';
    protected $guarded = ['id'];

    /**
     * 保存数据
     * @param int $id
     * @param array $save_data 需要保存的数据
     * @return bool|mixed
     */
    public static function saveData(int $id = 0, array $save_data)
    {
        if (!$save_data) return false;
        try {
            $res = DB::transaction(function () use ($id, $save_data) {
                $log = $save_data['log'];
                unset($save_data['log']);
                if ($id) {
                    $res = self::where('id', $id)->update($save_data);
                } else {
                    $result = self::create($save_data);
                    $res = $result->id;
                    $id = $res;
                }
                $log['refund_id'] = $id;
                //修改订单商品售后状态
                OrderGoods::where('id', $save_data['order_goods_id'])->update(['refund' => OrderGoods::REFUND_APPLY]);
                //日志信息
                if ($log) {
                    //日志信息
                    $image = array();
                    if (isset($log['image'])) {
                        $image = $log['image'];
                        unset($log['image']);
                    }
                    $log_res = RefundLog::create($log);
                    $log_id = $log_res->id;
                    //日志图片
                    $image_data = array();
                    foreach ($image as $key => $value) {
                        $image_data[] = array(
                            'log_id' => $log_id,
                            'image' => $value
                        );
                    }
                    if ($image_data) RefundImage::insert($image_data);
                }
                return $res;
            });
        } catch (\Exception $e) {
            $res = false;
        }
        return $res;
    }

    /**
     * 保存数据
     * @param int $id
     * @param array $save_data 需要保存的数据
     * @return bool|mixed
     */
    public static function cancel(int $id, int $order_goods_id, array $log)
    {
        try {
            $res = DB::transaction(function () use ($id, $order_goods_id, $log) {
                $res = self::where('id', $id)->update(['status' => Refund::STATUS_CUSTOMER_CANCEL, 'done_at' => get_date()]);
                //修改订单商品售后状态
                OrderGoods::where('id', $order_goods_id)->update(['refund' => OrderGoods::REFUND_CLOSE]);
                //日志信息
                if ($log) {
                    //日志信息
                    $image = array();
                    if (isset($log['image'])) {
                        $image = $log['image'];
                        unset($log['image']);
                    }
                    $log_res = RefundLog::create($log);
                    $log_id = $log_res->id;
                    //日志图片
                    $image_data = array();
                    foreach ($image as $key => $value) {
                        $image_data[] = array(
                            'log_id' => $log_id,
                            'image' => $value
                        );
                    }
                    if ($image_data) RefundImage::insert($image_data);
                }
                return $res;
            });
        } catch (\Exception $e) {
            $res = false;
        }
        return $res;
    }

    /**
     * 售后打款完成
     * @param $refund_id 售后id
     * @param $order_id 订单id
     * @param $order_goods_id 订单商品id
     * @return bool|mixed
     */
    public static function done($refund_id, $order_id, $order_goods_id)
    {
        try {
            $res = DB::transaction(function () use ($refund_id, $order_id, $order_goods_id) {
                //开始修改退款单和订单状态
                $res = self::where('id', $refund_id)->update(['status' => Refund::STATUS_DONE, 'done_at' => get_date()]);
                OrderGoods::where('id', $order_goods_id)->update(['refund' => OrderGoods::REFUND_DONE]);
                //判断订单下的商品是否全部退款,全部退款修改订单状态
                $refund_order_count = OrderGoods::where([['order_id', $order_id], ['refund', '!=', OrderGoods::REFUND_DONE]])->count();
                if ($refund_order_count == 0) {
                    Order::where('id', $order_id)->update(['status' => Order::STATUS_REFUND_COMPLETE]);
                }
                return $res;
            });
        } catch (\Exception $e) {
            $res = false;
        }
        return $res;
    }
}
