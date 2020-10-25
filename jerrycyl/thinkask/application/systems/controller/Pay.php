<?php

/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
|	支付处理
+---------------------------------------------------------------------------
 */
namespace app\systems\controller;
use app\index\model\Pay;
class Pay extends \think\Controller
{

    public function weixin()
    {
		if(request()->isPost()){
			$Pay = new Pay;
			$result = $Pay->weixin([
				'body' => input('post.body/s','','trim,strip_tags'),
				'attach' => input('post.attach/s','','trim,strip_tags'),
				'out_trade_no' => input('post.orderid/s','','trim,strip_tags'),
				'total_fee' => input('post.total_fee/f',0,'trim,strip_tags')*100,//订单金额，单位为分，如果你的订单是100元那么此处应该为 100*100
				'time_start' => date("YmdHis"),//交易开始时间
				'time_expire' => date("YmdHis", time() + 604800),//一周过期
				'goods_tag' => '在线充值余额',
				'notify_url' => request()->domain().url('index/index/weixin_notify'),
				'trade_type' => 'NATIVE',
				'product_id' => rand(1,999999),
			]);
			if(!$result['code']){
				return $this->error($result['msg']);
			}
			return $this->success($result['msg']);
		}
		$this->view->orderid = date("YmdHis").rand(100000,999999);
		return $this->fetch();
    }

	public function weixin_notify()
	{
		$notify_data = file_get_contents("php://input");
		if(!$notify_data){
			$notify_data = $GLOBALS['HTTP_RAW_POST_DATA'] ?: '';
		}
		if(!$notify_data){
			exit('');
		}
		$Pay = new Pay;
		$result = $Pay->notify_weixin($notify_data);
		exit($result);
	}

	public function alipay()
	{
		echo "string";
		die;
		if(request()->isPost()){
			$Pay = new Pay;
			$result = $Pay->alipay([
				'notify_url' => request()->domain().url('index/index/alipay_notify'),
				'return_url' => '',
				'out_trade_no' => input('post.orderid/s','','trim,strip_tags'),
				'subject' => input('post.subject/s','','trim,strip_tags'),
				'total_fee' => input('post.total_fee/f'),//订单金额，单位为元
				'body' => input('post.body/s','','trim,strip_tags'),
			]);
			if(!$result['code']){
				return $this->error($result['msg']);
			}
			return $result['msg'];
		}
		$this->view->orderid = date("YmdHis").rand(100000,999999);
		return $this->fetch();
	}

	public function alipay_notify()
	{
		$Pay = new Pay;
		$result = $Pay->notify_alipay();
		exit($result);
	}
}
