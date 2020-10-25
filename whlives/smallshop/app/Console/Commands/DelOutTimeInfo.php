<?php

namespace App\Console\Commands;

use App\Models\DeliveryTraces;
use App\Models\OrderDeliveryTemplate;
use Illuminate\Console\Command;

class DelOutTimeInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'del_out_time_info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '删除超时的信息';

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
        //删除一个月以上的物流信息
        $traces_date_time = get_date(time() - 30 * 24 * 3600);
        DeliveryTraces::where([['created_at', '<', $traces_date_time]])->delete();

        //删除10天以上的快递模板信息
        $tmp_date_time = get_date(time() - 10 * 24 * 3600);
        OrderDeliveryTemplate::where([['created_at', '<', $tmp_date_time]])->delete();
    }
}
