<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderLog;
use App\Services\OrderService;
use Illuminate\Console\Command;

class OrderCancel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order_cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '取消超时未支付的订单';

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
            ['status', Order::STATUS_WAIT_PAY],
            ['created_at', '<', get_date(time() - config('other.order.order_cancel_time'))]
        );
        while (true) {
            $order_res = Order::select('id', 'm_id', 'status', 'coupons_id')->where($where)->limit(10)->orderBy('id', 'asc')->get();
            if ($order_res->isEmpty()) {
                break;
            } else {
                foreach ($order_res as $order) {
                    OrderService::cancel($order, $user_data, OrderLog::USER_TYPE_SYSTEM, '订单支付超时自动取消');
                }
            }
        }
    }
}
