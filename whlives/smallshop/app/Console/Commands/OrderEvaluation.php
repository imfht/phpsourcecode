<?php

namespace App\Console\Commands;

use App\Models\Evaluation;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\OrderLog;
use App\Services\OrderService;
use Illuminate\Console\Command;

class OrderEvaluation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order_evaluation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '评价超时的订单默认评价';

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
        $user_data = array(
            'id' => 0,
            'username' => 'system'
        );
        $where = array(
            ['status', Order::STATUS_DONE],
            ['done_at', '<', get_date(time() - config('other.order.order_evaluation_time'))]
        );
        while (true) {
            $order_res = Order::select('id', 'm_id', 'status')->where($where)->limit(10)->orderBy('id', 'asc')->get();
            if ($order_res->isEmpty()) {
                break;
            } else {
                foreach ($order_res as $order) {
                    $this->evaluation($order);
                }
            }
        }
    }

    /**
     * 默认评价
     * @param $order 订单信息
     * @return array|string|null
     */
    private function evaluation($order)
    {
        //查询子商品
        $order_goods = OrderGoods::select('id', 'goods_id', 'sku_id', 'spec_value')->where('order_id', $order['id'])->get();
        if ($order_goods->isEmpty()) {
            return __('api.invalid_params');
        }
        $evaluation = array();
        foreach ($order_goods as $value) {
            $_item = array(
                'id' => $value['id'],
                'm_id' => $order['m_id'],
                'goods_id' => $value['goods_id'],
                'sku_id' => $value['sku_id'],
                'spec_value' => $value['spec_value'],
                'level' => 5,
                'content' => '好评',
                'image' => [],
                'is_image' => Evaluation::IS_IMAGE_FALSE
            );
            $evaluation[] = $_item;
        }
        if ($evaluation) {
            //修改订单状态
            $order_res = Order::where(['id' => $order['id'], 'status' => Order::STATUS_DONE])->update(['status' => Order::STATUS_COMMENT, 'comment_at' => get_date()]);
            if ($order_res) {
                //保存评论信息
                Evaluation::saveData($evaluation);
            }
        }
    }
}
