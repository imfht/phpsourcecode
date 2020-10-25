<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderLog;
use App\Services\OrderService;
use Illuminate\Console\Command;

class OrderConfirm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order_confirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '确认发货超时的订单自动确认';

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
            ['status', Order::STATUS_SHIPMENT],
            ['send_at', '<', get_date(time() - config('other.order.order_confirm_time'))]
        );
        while (true) {
            $order_res = Order::select('id', 'm_id', 'status')->where($where)->limit(10)->orderBy('id', 'asc')->get();
            if ($order_res->isEmpty()) {
                break;
            } else {
                foreach ($order_res as $order) {
                    OrderService::confirm($order, $user_data, OrderLog::USER_TYPE_SYSTEM, '订单确认超时自动确认');
                }
            }
        }
    }
}
