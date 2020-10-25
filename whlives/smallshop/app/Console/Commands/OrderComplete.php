<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\BalanceDetail;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\Refund;
use App\Models\Seller;
use App\Models\SellerBalance;
use App\Models\SellerBalanceDetail;
use Illuminate\Console\Command;

class OrderComplete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order_complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单交易完成';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $page = 1;
        $pagesize = 10;
        $user_data = array(
            'id' => 0,
            'username' => 'system'
        );
        $where = array(
            ['status', Order::STATUS_COMMENT],
            ['comment_at', '<', get_date(time() - config('other.order.order_complete_time'))]
        );
        while (true) {
            $offset = ($page - 1) * $pagesize;
            $order_res = Order::select('id', 'm_id', 'status', 'seller_id', 'subtotal', 'order_no', 'level_one_m_id', 'level_two_m_id')->where($where)->offset($offset)->limit($pagesize)->orderBy('id', 'asc')->get();
            if ($order_res->isEmpty()) {
                break;
            } else {
                $page++;
                $order_ids = $seller_ids = array();
                foreach ($order_res as $order) {
                    $order_ids[] = $order['id'];
                    $seller_ids[] = $order['seller_id'];
                }
                //查询正在售后的订单
                $refund_order_id = OrderGoods::whereIn('order_id', array_unique($order_ids))->whereIn('refund', [OrderGoods::REFUND_APPLY, OrderGoods::REFUND_ONGOING])->pluck('order_id')->toArray();

                //组合没有在售后的订单
                $new_order_ids = array();
                foreach ($order_res as $order) {
                    if (!in_array($order['id'], $refund_order_id)) {
                        $new_order_ids[] = $order['id'];
                    }
                }
                //查询商家结算信息
                $seller = Seller::whereIn('id', array_unique($seller_ids))->pluck('pct', 'id');

                //修改订单状态
                Order::whereIn('id', $new_order_ids)->where('status', Order::STATUS_COMMENT)->update(['status' => Order::STATUS_COMPLETE, 'complete_at' => get_date()]);

                //查询订单下的商品
                $order_goods_res = OrderGoods::select('order_id', 'id', 'sell_price', 'promotion_price', 'buy_qty', 'level_one_pct', 'level_two_pct')->whereIn('order_id', $new_order_ids)->get();
                $order_goods = array();
                if ($order_goods_res->isEmpty()) {
                    continue;
                }
                foreach ($order_goods_res as $_goods) {
                    $order_goods[$_goods['order_id']][] = $_goods;
                }
                //查询已经完成的售后单
                $refund_res = Refund::select('order_id', 'amount', 'order_goods_id')->whereIn('order_id', array_unique($new_order_ids))->where('status', Refund::STATUS_DONE)->get();
                $refund_data = array();
                if (!$refund_res->isEmpty()) {
                    foreach ($refund_res as $_refund) {
                        $refund_data[$_refund['order_id']][$_refund['order_goods_id']] = $_refund;
                    }
                }

                //开始结算
                foreach ($order_res as $_order) {
                    //只有没有售后的才完成
                    if (in_array($_order['id'], $new_order_ids)) {
                        //计算订单金额
                        $refund_amount = $level_one_amount = $level_two_amount = 0;
                        if (isset($order_goods[$_order['id']])) {
                            foreach ($order_goods[$_order['id']] as $_goods) {
                                $_refund_amount = isset($refund_data[$_order['id']][$_goods['id']]['amount']) ? $refund_data[$_order['id']][$_goods['id']]['amount'] : 0;//单个商品售后金额
                                $_goods_amount = ($_goods['sell_price'] - $_goods['promotion_price']) * $_goods['buy_qty'] - $_refund_amount;//商品最终金额
                                $refund_amount = $refund_amount + $_refund_amount;//累计售后金额
                                $level_one_amount = $level_one_amount + format_price($_goods_amount * $_goods['level_one_pct'] / 100);//累计一级推荐提成
                                $level_two_amount = $level_two_amount + format_price($_goods_amount * $_goods['level_two_pct'] / 100);//累计二级推荐提成
                            }
                            //给商家结算
                            $pct = isset($seller[$_order['seller_id']]) ? $seller[$_order['seller_id']] : 0;//商家结算手续费比例
                            $amount = format_price($_order['subtotal'] - $refund_amount);//待结算金额
                            $poundage = format_price($amount * ($pct / 100));//手续费
                            SellerBalance::updateAmount($_order['seller_id'], $amount, SellerBalanceDetail::EVENT_ORDER, $_order['order_no'], '订单完成结算');
                            if ($poundage) {
                                SellerBalance::updateAmount($_order['seller_id'], -$poundage, SellerBalanceDetail::EVENT_POUNDAGE, $_order['order_no'], '订单结算手续费');
                            }
                            //提成结算
                            if ($level_one_amount) {
                                Balance::updateAmount($_order['level_one_m_id'], $level_one_amount, BalanceDetail::EVENT_RECOMMEND_ORDER, $_order['order_no'], '推荐订单收益');
                            }
                            if ($level_two_amount) {
                                Balance::updateAmount($_order['level_two_m_id'], $level_two_amount, BalanceDetail::EVENT_RECOMMEND_ORDER, $_order['order_no'], '推荐订单收益');
                            }
                        }
                    }
                }
            }
        }
    }
}
