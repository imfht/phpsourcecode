<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/18
 * Time: 下午4:03
 */

namespace App\Services;

use App\Libs\Kdniao;
use App\Models\Cart;
use App\Models\CouponsDetail;
use App\Models\GoodsNum;
use App\Models\MarketSeckill;
use App\Models\Member;
use App\Models\OrderDeliveryTemplate;
use App\Models\ExpressCompany;
use App\Models\GoodsSku;
use App\Models\MemberProfile;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderGoods;
use App\Models\OrderLog;
use App\Models\Payment;
use App\Models\Point;
use App\Models\PointDetail;
use App\Models\Promotion;
use App\Models\Refund;
use App\Models\Trade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderService
{

    /**
     * 生成订单号
     * @return string
     */
    public static function getOrderNo()
    {
        $order_no = date('YmdHis', time()) . rand(100000, 999999);
        return $order_no;
    }

    /**
     * 订单是否可以取消
     * @param $order 订单信息
     */
    public static function isCancel($order = array())
    {
        if (isset($order['status']) && ($order['status'] == Order::STATUS_WAIT_PAY || $order['status'] == Order::STATUS_PAID)) {
            return true;
        }
        return false;
    }

    /**
     * 订单是否可以支付
     * @param $order 订单信息
     */
    public static function isPay($order = array())
    {
        if (isset($order['status']) && $order['status'] == Order::STATUS_WAIT_PAY) {
            return true;
        }
        return false;
    }

    /**
     * 订单是否可以确认
     * @param $order 订单信息
     */
    public static function isConfirm($order = array())
    {
        if (isset($order['status']) && $order['status'] == Order::STATUS_SHIPMENT) {
            return true;
        }
        return false;
    }

    /**
     * 订单是否可以发货
     * @param $order 订单信息
     */
    public static function isDelivery($order = array())
    {
        if (isset($order['status']) && ($order['status'] == Order::STATUS_SHIPMENT || $order['status'] == Order::STATUS_PAID)) {
            return true;
        }
        return false;
    }

    /**
     * 订单是否可以修改地址
     * @param $order 订单信息
     */
    public static function isUpdateAddress($order = array())
    {
        if (isset($order['status']) && ($order['status'] == Order::STATUS_WAIT_PAY || $order['status'] == Order::STATUS_PAID)) {
            return true;
        }
        return false;
    }

    /**
     * 订单是否可以评价
     * @param $order 订单信息
     */
    public static function isEvaluation($order = array())
    {
        if (isset($order['status']) && $order['status'] == Order::STATUS_DONE) {
            return true;
        }
        return false;
    }

    /**
     * 用户订单支付完成修改订单状态
     * @param array $notify_data 支付回调信息
     * @return bool
     */
    public static function updatePayOrder(array $notify_data)
    {
        if (!$notify_data) {
            return false;
        }
        //修改订单状态
        $update_order = array(
            'trade_id' => $notify_data['trade_id'],
            'status' => Order::STATUS_PAID,
            'payment_id' => $notify_data['payment_id'],
            'payment_no' => $notify_data['payment_no'],
            'flag' => $notify_data['flag'],
            'pay_at' => get_date()
        );
        //下单包含的sku商品
        $res = Order::where('status', Order::STATUS_WAIT_PAY)->whereIn('order_no', $notify_data['order_no'])->update($update_order);
        if ($res) {
            return true;
        }
        return false;
    }

    /**
     * 订单改价
     * @param $order 订单信息
     * @param $discount_price 改价金额
     * @param $delivery_price_real 运费金额
     * @param $user_data 操作用户名信息
     * @param int $user_type 用户类型0用户1系统2管理员3商家
     * @param string $note 备注
     * @return bool
     */
    public static function updatePrice($order, $discount_price, $delivery_price_real, $user_data, $user_type = 3, $note = '')
    {
        if (!self::isPay($order)) {
            return __('admin.order_status_error');
        }
        if ($order) {
            $order_id = $order['id'];
            $promotion_after_price = $order['sell_price_total'] - $order['promotion_price'];
            if ($delivery_price_real < 0) {
                return __('admin.order_delivery_price_real_error');
            } elseif (($promotion_after_price + $discount_price) < 0) {
                return '改价优惠金额不能大于' . $promotion_after_price . '元';
            }
            $subtotal = $promotion_after_price + $discount_price + $delivery_price_real;
            $update_data = array(
                'discount_price' => $discount_price,
                'delivery_price_real' => $delivery_price_real,
                'subtotal' => $subtotal
            );
            $order_log = array(
                'order_id' => $order['id'],
                'user_type' => $user_type,
                'user_id' => $user_data['id'],
                'username' => $user_data['username'],
                'action' => OrderLog::USER_TYPE_DESC[$user_type] . '改价',
                'note' => '改价金额' . $discount_price . ',修改运费金额' . $delivery_price_real
            );
            try {
                DB::transaction(function () use ($order_id, $update_data, $order_log) {
                    Order::where('id', $order_id)->update($update_data);
                    OrderLog::create($order_log);//添加订单日志
                });
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 后台支付订单支付
     * @param $order_id 订单信息
     * @param $user_data 操作用户名信息
     * @param int $user_type 用户类型0用户1系统2管理员3商家
     * @param string $note 备注
     * @return bool
     */
    public static function payOrder($order, $user_data, $user_type = 0, $note = '')
    {
        if (!self::isPay($order)) {
            return __('admin.order_status_error');
        }
        if ($order) {
            $order_id = $order['id'];
            $order_log = array(
                'order_id' => $order_id,
                'user_type' => $user_type,
                'user_id' => $user_data['id'],
                'username' => $user_data['username'],
                'action' => OrderLog::USER_TYPE_DESC[$user_type] . '支付',
                'note' => $note
            );

            try {
                DB::transaction(function () use ($order_id, $order_log) {
                    Order::where('id', $order_id)->update(['status' => Order::STATUS_PAID, 'pay_at' => get_date()]);
                    OrderLog::create($order_log);//添加订单日志
                });
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return __('api.order_error');
        }
    }

    /**
     * 订单取消
     * @param $order_id 订单id
     * @param $user_data 操作用户名信息
     * @param int $user_type 用户类型0用户1系统2管理员3商家
     * @param string $note 备注
     * @return bool
     */
    public static function cancel($order, $user_data, $user_type = 0, $note = '')
    {
        if (!self::isCancel($order)) {
            return __('api.order_status_error');
        }
        if ($order) {
            $order_id = $order['id'];
            $coupons_id = $order['coupons_id'];
            $order_log = array(
                'order_id' => $order_id,
                'user_type' => $user_type,
                'user_id' => $user_data['id'],
                'username' => $user_data['username'],
                'action' => OrderLog::USER_TYPE_DESC[$user_type] . '取消',
                'note' => $note
            );
            $status = $user_type == 1 ? Order::STATUS_SYSTEM_CANNEL : Order::STATUS_CANNEL;
            if ($order['status'] == Order::STATUS_PAID) $status = Order::STATUS_REFUND_COMPLETE;
            try {
                DB::transaction(function () use ($order_id, $coupons_id, $status, $order_log) {
                    Order::where(['id' => $order_id])->update(['status' => $status, 'close_at' => get_date()]);
                    OrderLog::create($order_log);//添加订单日志
                    if ($coupons_id) {
                        //存在优惠券的时候需要返还
                        CouponsDetail::where('id', $coupons_id)->update(['is_use' => CouponsDetail::USE_OFF]);
                    }
                });
                //还原库存
                self::orderStockIncr($order_id, $order['market_type']);
                //已经支付的需要做退款操作
                if ($order['status'] == Order::STATUS_PAID) {
                    $refund_res = self::payRefund($order['m_id'], $order['trade_id'], $order['order_no'], $order['subtotal'], '订单取消');
                    if ($refund_res === true) {
                        return true;
                    } else {
                        return __($refund_res);
                    }
                }
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return __('api.order_error');
        }
    }

    /**
     * 订单确认收货
     * @param $order 订单信息
     * @param $user_data 操作用户名信息
     * @param int $user_type 用户类型0用户1系统2管理员3商家
     * @param string $note 备注
     * @return bool
     */
    public static function confirm($order, $user_data, $user_type = 0, $note = '')
    {
        if (!self::isConfirm($order)) {
            return __('api.order_status_error');
        }
        if ($order) {
            $order_id = $order['id'];
            $order_log = array(
                'order_id' => $order_id,
                'user_type' => $user_type,
                'user_id' => $user_data['id'],
                'username' => $user_data['username'],
                'action' => OrderLog::USER_TYPE_DESC[$user_type] . '确认',
                'note' => $note
            );
            try {
                DB::transaction(function () use ($order_id, $order_log) {
                    Order::where('id', $order_id)->update(['status' => Order::STATUS_DONE, 'done_at' => get_date()]);
                    OrderLog::create($order_log);//添加订单日志
                });
                return true;
            } catch (\Exception $e) {
                return false;
            }
            //开始进行奖励

        } else {
            return __('api.order_error');
        }
    }

    /**
     * 订单奖励
     * @param $order
     */
    public static function reward($order)
    {
        //积分奖励
        Point::updateAmount($order['m_id'], $order['subtotal'], PointDetail::EVENT_ORDER_PAY, $order['order_no'], '订单奖励');
        //查询是否能参加优惠活动
        $where = array(
            ['seller_id', $order['seller_id']],
            ['status', Promotion::STATUS_ON],
            ['use_price', '<=', $order['subtotal']],
            ['start_at', '<=', get_date()],
            ['end_at', '>=', get_date()],
        );
        $group_id = Member::where('id', $order['m_id'])->value('group_id');
        $type_promotion = array(Promotion::TYPE_POINT, Promotion::TYPE_COUPONS);
        $res_promotion = Promotion::select('title', 'type', 'type_value')->where($where)->whereIn('type', $type_promotion)->whereRaw("find_in_set($group_id, user_group)")->get();
        if (!$res_promotion->isEmpty()) {
            $use_price = 0;
            $point_amount = 0;
            $point_promotion = array();
            $coupons_promotion = array();
            foreach ($res_promotion as $value) {
                switch ($value['type']) {
                    case Promotion::TYPE_POINT:
                        //获取奖励积分最多的
                        if ($value['type_value'] > $point_amount) {
                            $point_amount = $value['type_value'];
                            $point_promotion = $value;
                        }
                        break;
                    case Promotion::TYPE_COUPONS:
                        //获取起用金额最高的
                        if ($value['use_price'] > $use_price) {
                            $use_price = $value['use_price'];
                            $coupons_promotion = $value;
                        }
                        break;
                }
            }
            if ($point_promotion && $point_promotion['type_value']) {
                //发放额外奖励积分
                Point::updateAmount($order['m_id'], $point_promotion['type_value'], PointDetail::EVENT_ORDER_REWARD, $order['order_no'], '订单额外奖励');
                //发放优惠券
            }
            if ($coupons_promotion && $coupons_promotion['type_value']) {
                //发放优惠券
                CouponsDetail::obtain($order['m_id'], $coupons_promotion['type_value']);
            }
        }
    }

    /**
     * 订单发货
     * @param $order 订单信息
     * @param $order_goods_id 订单商品id
     * @param $company_id 快递公司id
     * @param $code 快递单号
     * @param $user_data 操作用户名信息
     * @param int $user_type 用户类型0用户1系统2管理员3商家
     * @param string $note 备注
     * @return bool
     */
    public static function delivery($order, $order_goods_id, $company_id, $code, $user_data, $user_type = 3, $note = '')
    {
        if (!self::isDelivery($order)) {
            return __('admin.order_status_error');
        }
        if ($order) {
            $order_id = $order['id'];
            $express_company = ExpressCompany::select('title', 'code')->where('id', $company_id)->first();
            $delivery_data = array(
                'order_id' => $order_id,
                'order_goods_id' => json_encode($order_goods_id),
                'company_code' => $express_company['code'],
                'company_name' => $express_company['title'],
                'code' => $code,
                'note' => $note
            );
            $order_log = array(
                'order_id' => $order_id,
                'user_type' => $user_type,
                'user_id' => $user_data['id'],
                'username' => $user_data['username'],
                'action' => OrderLog::USER_TYPE_DESC[$user_type] . '发货',
                'note' => $note
            );

            try {
                DB::transaction(function () use ($order_id, $order_goods_id, $delivery_data, $order_log) {
                    Order::where('id', $order_id)->update(['status' => Order::STATUS_SHIPMENT, 'send_at' => get_date()]);
                    //修改商品发货状态
                    OrderGoods::whereIn('id', $order_goods_id)->update(['delivery' => OrderGoods::DELIVERY_ON]);
                    OrderDelivery::create($delivery_data);//添加发货信息
                    OrderLog::create($order_log);//添加订单日志
                });
                //订阅物流消息
                $kdniao = New Kdniao();
                $kdniao->subscribe($express_company['code'], $code);
                return true;
            } catch (\Exception $e) {dd($e);
                return false;
            }
        } else {
            return __('api.order_error');
        }
    }

    /**
     * 订单调用第三方发货
     * @param $order 订单信息
     * @param $express_company 物流公司信息
     * @param $kdniao_delivery 快递鸟返回信息
     * @param $user_data 操作用户名信息
     * @param int $user_type 用户类型0用户1系统2管理员3商家
     * @param string $note 备注
     * @return bool
     */
    public static function apiDelivery($order, $express_company, $kdniao_delivery, $user_data, $user_type = 3, $note = '')
    {
        if (!self::isDelivery($order)) {
            return __('admin.order_status_error');
        }
        if ($order) {
            //开始调用快递鸟的接口
            $order_id = $order['id'];
            $order_goods_id = OrderGoods::where('order_id', $order_id)->pluck('id')->toArray();
            $delivery_data = array(
                'order_id' => $order_id,
                'order_goods_id' => json_encode($order_goods_id),
                'company_code' => $express_company['code'],
                'company_name' => $express_company['title'],
                'code' => $kdniao_delivery['code'],
                'note' => $note
            );
            $order_log = array(
                'order_id' => $order_id,
                'user_type' => $user_type,
                'user_id' => $user_data['id'],
                'username' => $user_data['username'],
                'action' => OrderLog::USER_TYPE_DESC[$user_type] . '发货',
                'note' => $note
            );
            $print_template = array(
                'order_id' => $order_id,
                'seller_id' => $order['seller_id'],
                'content' => $kdniao_delivery['print_template']
            );

            try {
                DB::transaction(function () use ($order_id, $order_goods_id, $delivery_data, $order_log, $print_template) {
                    Order::where('id', $order_id)->update(['status' => Order::STATUS_SHIPMENT, 'send_at' => get_date()]);
                    //修改商品发货状态
                    OrderGoods::where('order_id', $order_id)->update(['delivery' => OrderGoods::DELIVERY_ON]);
                    $delivery_res = OrderDelivery::create($delivery_data);//添加发货信息
                    $delivery_id = $delivery_res->id;
                    OrderLog::create($order_log);//添加订单日志
                    $print_template['order_delivery_id'] = $delivery_id;
                    OrderDeliveryTemplate::create($print_template);
                });
                //订阅物流消息
                $kdniao = New Kdniao();
                $kdniao->subscribe($express_company['code'], $kdniao_delivery['code']);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return __('api.order_error');
        }
    }

    /**
     * 还原订单库存
     * @param $order_id 订单id
     * @param $market_type 订单类型
     */
    public static function orderStockIncr($order_id, $market_type)
    {
        if ($order_id) {
            $is_special = ($market_type == Order::MARKET_TYPE_SECKILL) ? true : false;
            //秒杀的还需要还原redis start
            if ($market_type == Order::MARKET_TYPE_SECKILL) {
                $sku_qty = OrderGoods::select('buy_qty', 'sku_id', 'goods_id')->where('order_id', $order_id)->get();
                if (!$sku_qty->isEmpty()) {
                    foreach ($sku_qty as $value) {
                        self::seckillStockIncr($value['goods_id'], $value['sku_id'], $value['buy_qty']);
                    }
                }
            }
            //秒杀的还需要还原redis end
            $sku_qty = OrderGoods::where('order_id', $order_id)->pluck('buy_qty', 'sku_id');
            if ($sku_qty) {
                self::stockIncr($sku_qty->toArray(), $is_special);
            }
        } else {
            return false;
        }
    }

    /**
     * 减少库存
     * @param $sku_qty skuid和buy_qty数组
     * @param $is_special 是否活动
     */
    public static function stockDecr($sku_qty, $is_special = false)
    {
        if ($sku_qty && is_array($sku_qty)) {
            $field = 'stock';
            if ($is_special) {
                //减少活动库存
                $field = 'activity_stock';
            }
            foreach ($sku_qty as $key => $val) {
                GoodsSku::where('id', $key)->decrement($field, $val);
                GoodsNum::where('goods_id', $key)->increment('sale', $val);//增加销量
            }
        } else {
            return false;
        }
    }

    /**
     * 还原库存
     * @param $sku_qty skuid和buy_qty数组
     * @param $is_special 是否活动
     */
    public static function stockIncr($sku_qty, $is_special = false)
    {
        if ($sku_qty && is_array($sku_qty)) {
            $field = 'stock';
            if ($is_special) {
                //秒杀的需要还原活动库存
                $field = 'activity_stock';
            }
            foreach ($sku_qty as $key => $val) {
                GoodsSku::where('id', $key)->increment($field, $val);
                GoodsNum::where('goods_id', $key)->decrement('sale', $val);//减少销量
            }
        } else {
            return false;
        }
    }

    /**
     * 变更秒杀商品的库存
     * @param int $goods_id 主商品id
     * @param int $sku_id 商品skuid
     * @param int $buy_qty 数量
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public static function seckillStockIncr(int $goods_id, int $sku_id, int $buy_qty)
    {
        if (!$goods_id || !$sku_id || !$buy_qty) {
            return false;
        }
        //还原库存
        $_redis_key = MarketSeckill::GOODS_REDIS_KEY . $goods_id;
        Redis::hincrby($_redis_key, $sku_id, $buy_qty);
    }

    /**
     * 订单操作按钮
     * @param $status 订单状态
     * @param $is_detail 是否详情页
     */
    public static function orderButton($status, $is_detail = false)
    {
        $button = array(
            'cancel' => 0,//取消订单
            'payment' => 0,//支付
            'confirm' => 0,//确认
            'delete' => 0,//删除
            'evaluation' => 0,//评价
            'delivery' => 0//物流
        );
        //待支付
        if ($status == Order::STATUS_WAIT_PAY) {
            $button['cancel'] = 1;
            $button['payment'] = 1;
        }
        //已经发货
        if ($status == Order::STATUS_SHIPMENT) {
            $button['delivery'] = 1;
            $button['confirm'] = 1;
        }
        //已经确认
        if ($status == Order::STATUS_DONE) {
            $button['delivery'] = 1;
            $button['evaluation'] = 1;
        }
        //已经评价
        if ($status == Order::STATUS_COMMENT) {
            //$button['delivery'] = 1;
        }
        //交易成功
        if ($status == Order::STATUS_COMPLETE) {
            //$button['delivery'] = 1;
        }
        //已取消
        if ($status == Order::STATUS_CANNEL || $status == Order::STATUS_SYSTEM_CANNEL) {
            $button['delete'] = 1;
        }
        return $button;
    }

    /**
     * 售后退款操作
     * @param $refund_id 售后单id
     * @param $amount 售后金额
     * @return array|bool|string|null
     */
    public static function refund($refund_id, $amount)
    {
        $refund = Refund::find($refund_id);
        if (!$refund) {
            return __('api.invalid_params');
        }
        $order = Order::select('id', 'm_id', 'trade_id')->where('id', $refund['order_id'])->first();
        if (!$order) {
            return __('api.order_error');
        }
        $refund_res = self::payRefund($order['m_id'], $order['trade_id'], $refund['refund_no'], $refund['amount']);

        if ($refund_res === true) {
            $res = Refund::done($refund['id'], $refund['order_id'], $refund['order_goods_id'], '售后退款');
            if ($res) {
                return true;
            } else {
                return __('api.fail');
            }
        } else {
            return __($refund_res);
        }
    }

    /**
     * 退款操作资金退回
     * @param $m_id 用户id
     * @param $trade_id 交易单号
     * @param $refund_no 退款单号
     * @param $amount 退款金额
     * @param $note 备注
     * @return array|bool|string|null
     */
    public static function payRefund($m_id, $trade_id, $refund_no, $amount, $note = '')
    {
        //后台支付的退到余额
        $payment_id = Payment::PAYMENT_BALANCE;
        if ($trade_id) {
            $trade = Trade::select('pay_total', 'payment_no', 'payment_id')->where('id', $trade_id)->first();
            $payment_id = $trade['payment_id'];
        }

        $payment = Payment::find($payment_id);
        if (!$payment) {
            return __('api.payment_error');
        }

        $refund_info = array(
            'm_id' => $m_id,
            'payment_no' => isset($trade['payment_no']) ? $trade['payment_no'] : '',
            'refund_no' => $refund_no,
            'pay_total' => isset($trade['pay_total']) ? $trade['pay_total'] : 0,
            'refund_amount' => $amount,
            'note' => $note
        );

        $class_name = '\App\Libs\Payment\\' . $payment['class_name'];
        $pay = new $class_name();
        $refund_res = $pay->refund($refund_info);
        if ($refund_res && is_array($refund_res)) {
            return true;
        } else {
            return __($refund_res);
        }
    }
}
