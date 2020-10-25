<?php

namespace App\Jobs;

use App\Models\FundMerchant;
use App\Models\FundOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderNotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order_no = '';
    
    // 依次等待时间，模仿三方支付异步通知等待模式
    protected $delays = [
    	0, 3, 5, 10, 30
	];
    protected $times = 0;
    
    /**
     * Create a new job instance.
     *
	 * @param string $order_no 订单号
	 * @param int $times 第几次执行了
     * @return void
     */
    public function __construct($order_no, int $times = 0)
    {
		$this->order_no = $order_no;
		$this->times = $times;
		$delay = $this->delays[$times];
		$this->delay($delay);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		$order = FundOrder::where(['order_no'=>$this->order_no])->first();
		$merchant = FundMerchant::where(['id' => $order->merchant_id])->first(['appid','secret']);
		
		$notify_url = $order->notify_url;
		$param = [
			'ebank_appid'	=> $merchant->appid,
			'order_no'		=> $order->order_no,
			'pay_status'	=> $order->pay_status,
			'notify_param'	=> $order->notify_param,	// 第二维数组
		];
		
		$param['ebank_sign'] = sign_merchant($param,$merchant->secret);
		
		$result = curl_post($notify_url,$param);
		// 商户输出 SUCCESS/success 才表示成功
		if(strcmp('success',strtolower($result)) === 0){
			$order->notify_status = 1;
			$order->notify_time = time2date(time());
			$order->save();
		}else{
			exception('['.$this->order_no.']未返回 SUCCESS 字符串，标记为失败');
		}
    }
	
	
	public function failed(\Exception $exception){
    	// 依次触发等待时间序列
    	if($this->times < count($this->delays) - 1){
			OrderNotify::dispatch($this->order_no, $this->times + 1);
		}else{
			email_bug($this->order_no.'订单异步通知到商户失败，请通知商户及时处理程序错误',$exception->__toString());
		}
	}
}
