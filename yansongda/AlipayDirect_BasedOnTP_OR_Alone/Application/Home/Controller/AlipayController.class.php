<?php
namespace Home\Controller;
use Think\Controller;
use Common\Lib\Alipay\Alipay;

/**
* 支付宝
* --------------------
* @author @闫嵩达 <yansong.da@qq.com>
* 2014/11/10 14:12
* ------------------
*/
class AlipayController extends Controller
{

	/**
	 * 支付方法
	 * @return [type] [description]
	 */
	public function alipay()
	{
		/**
		 * 需要修改！
		 * 写自己的业务逻辑（获取POST过来的订单那数据）~或把此方法集成到其他控制器（比如说Buycontroller）中。
		 */
		$alipayp['total_fee'] = '0.01';//订单总金额
		$alipayp['out_trade_no'] = '1';//商户订单ID
		$alipayp['subject'] = 'XXXX';//订单商品标题
		$alipayp['body'] = 'XXX';//订单商品描述
		$alipayp['show_url'] = '';//订单商品地址
		$alipay = new alipay();
		$alipay->toAlipay($alipayp);
	}
	
	/**
	 * 支付宝同步通知
	 * @return [type] [description]
	 */
	public function alipayReturn()
	{
		if ( empty($_GET) ) {
			$this->error('您查看的页面不存在');
		}
		$alipay = new alipay();
		if ( !$alipay->isAlipay($_GET) ) {
			$this->error('验证失败请不要做违法行为！');
		}
		$alipay_no = I('get.trade_no');
		$order_id = I('get.out_trade_no');
		$status = I('get.trade_status');
		/**
		 * 这里需要修改。！！！
		 * --------------------------
		 * 写出自己的业务逻辑。
		 * 从数据库中获取订单信息，然后判断订单状态是否经过处理什么的！！
		 * ------------------
		 */
		
			if ( $status == 'TRADE_FINISHED' || $status == 'TRADE_SUCCESS') {
				/**
				 * 这里写出更新订单状态等的业务逻辑
				 */
			} else {
				/**
				 * 应该是hacking行为了
				 */
			}

		$this->display();
	}

	/**
	 * 支付宝异步通知
	 * @return [type] [description]
	 */
	public function alipayNotify()
	{
		if ( empty($_POST) ) {
			$this->error('您查看的页面不存在');
		}
		$alipay = new alipay();
		if ( !$alipay->isAlipay($_POST) ) {
			$this->error('请不要做违法行为！');
		}
		$alipay_no = I('post.trade_no');
		$order_id = I('post.out_trade_no');
		$status = I('post.trade_status');
		/**
		 * 同上！写出自己的业务逻辑
		 */
		echo "success";
	}
}