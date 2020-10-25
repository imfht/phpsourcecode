<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * 订单
 * Class Payment
 * @package App\Models
 */
class Order extends BaseModels
{
    use SoftDeletes;
    //状态
    const STATUS_WAIT_PAY = 0;
    const STATUS_PAID = 1;
    const STATUS_SHIPMENT = 2;
    const STATUS_DONE = 3;
    const STATUS_COMMENT = 4;
    const STATUS_COMPLETE = 5;
    const STATUS_REFUND_COMPLETE = 6;
    const STATUS_CANNEL = 10;
    const STATUS_SYSTEM_CANNEL = 11;

    //后台展示状态
    const STATUS_DESC = [
        self::STATUS_WAIT_PAY => '待支付',
        self::STATUS_PAID => '已支付',
        self::STATUS_SHIPMENT => '待收货',
        self::STATUS_DONE => '待评价',
        self::STATUS_COMMENT => '已评价',
        self::STATUS_COMPLETE => '订单完成',
        self::STATUS_REFUND_COMPLETE => '全部退款',
        self::STATUS_CANNEL => '已取消',
        self::STATUS_SYSTEM_CANNEL => '系统取消'
    ];

    //用户展示状态
    const STATUS_MEMBER_DESC = [
        self::STATUS_WAIT_PAY => '待支付',
        self::STATUS_PAID => '待发货',
        self::STATUS_SHIPMENT => '待收货',
        self::STATUS_DONE => '待评价',
        self::STATUS_COMMENT => '交易完成',
        self::STATUS_COMPLETE => '交易完成',
        self::STATUS_REFUND_COMPLETE => '已退款',
        self::STATUS_CANNEL => '已取消',
        self::STATUS_SYSTEM_CANNEL => '已取消'
    ];

    //配送方式
    const DELIVERY_COURIER = 1;
    const DELIVERY_SINCE = 2;
    const DELIVERY_DESC = [
        self::DELIVERY_COURIER => '快递',
        self::DELIVERY_SINCE => '自提'
    ];

    //活动类型
    const MARKET_TYPE_DEFAULT = 0;
    const MARKET_TYPE_SECKILL = 1;
    const MARKET_TYPE_SPELL = 2;

    const MARKET_TYPE_DESC = [
        self::MARKET_TYPE_DEFAULT => '普通',
        self::MARKET_TYPE_SECKILL => '秒杀',
        self::MARKET_TYPE_SPELL => '拼团'
    ];

    //风险订单提示
    const FLAG_NO = 0;
    const FLAG_YES = 1;
    const FLAG_DESC = [
        self::FLAG_NO => '正常',
        self::FLAG_YES => '风险'
    ];

    protected $table = 'order';
    protected $guarded = ['id'];

    /**
     * 获取商品
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function goods()
    {
        return $this->hasMany('App\Models\OrderGoods');
    }

    /**
     * 获取发货信息
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function delivery()
    {
        return $this->hasMany('App\Models\OrderDelivery');
    }

    /**
     * 获取订单日志
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function log()
    {
        return $this->hasMany('App\Models\OrderLog');
    }

    /**
     * 获取发票信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function invoice()
    {
        return $this->hasOne('App\Models\OrderInvoice');
    }

    /**
     * 提交订单
     * @param array $order_data
     */
    public static function submitOrder(array $order_data)
    {
        if (!$order_data) return false;
        if ($order_data) {
            try {
                DB::transaction(function () use ($order_data) {
                    foreach ($order_data as $order) {
                        //商品信息
                        $order_goods = isset($order['goods']) ? $order['goods'] : [];
                        if (!$order_goods) return false;
                        unset($order['goods']);
                        //发票信息
                        $invoice = array();
                        if (isset($order['invoice'])) {
                            $invoice = $order['invoice'];
                            unset($order['invoice']);
                        }
                        //添加订单
                        $order_res = self::create($order);
                        $order_id = $order_res->id;
                        //更新优惠券使用状态
                        if ($order['coupons_id']) {
                            CouponsDetail::where('id', $order['coupons_id'])->update(['is_use' => CouponsDetail::USE_ON, 'use_at' => get_date()]);
                        }
                        //添加订单商品
                        foreach ($order_goods as $goods) {
                            $goods['order_id'] = $order_id;
                            OrderGoods::create($goods);
                        }
                        //发票信息
                        if ($invoice) {
                            $invoice['order_id'] = $order_id;
                            OrderInvoice::create($invoice);
                        }
                    }
                });
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }
}
