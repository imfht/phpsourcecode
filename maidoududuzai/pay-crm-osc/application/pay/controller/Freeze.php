<?php

namespace app\pay\controller;

use \think\Db;
use \app\common\Pay;
use \app\common\PayAction;

class Freeze
{

	public function __construct()
	{
		$this->AopSdk = new \app\common\AopSdk();
		$this->AopSdk->aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
		$this->AopSdk->aop->notify_url = url('/pay/alipay/notify', null, null, true);
		$this->AopSdk->set([
			'appId' => '2016101400684979',
			'rsaPrivateKey' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDihtqk8j8fcn5vYibnZ4wV+0yfPA9oyDhiqjqtq0krRGzMI/FvMka5OXPI2EH6jae9r4oVEwdgrHnegpglUI3Wx9PXYRMPOhtjxVzBN9RhIJXMzAMvuFgXWiI7QARXEkX8MgKyEe8UP34dhvrTPvof9eepwfWyF2FS1TxzFrrLPrTO9VCiQ8Yv8JXNmhNBgrOQD6YdMzNLrcJxaBx+KjOu544e+oQOACw2Wap3/lKuLkQ8OOcVUbXgZ+3+lXNOH0hoNWaTa1K9Xof1DqoLASInT4QoGaenjg7n6HbMb4NKp6fAbLtDGF6cg/w+gQ6UfttTYS4tfNl16a8GADZHDrLvAgMBAAECggEBALO6x3Oj+M1crFB3keJ8V7uBHbQhlSBP69zsr43nnzBDJAd8ZS0SuOZxXRp36zF+fx6TTn2WEX50mmUfPNQua7uD5OK4VMT6F407pHJxd4Jwtio9nDGak9pDW3GjQ05KY1jL3TOn4wcvsUKAPDarew0ssFgSWnyo7EEqisHNwPtMENLBvYWGk3Zlg4EStzf6la1sxRFEeIhsuyPxCxv4/1Q73wpxU6gu77YqrfdcIkYZcfXuOp6zESAHbtCqFj8GK/OIFIL5FRHCabTuA0G6qB5RykZFEMqA4qxpt1Ww57tvCssoPqvMZ/R3KWGuSdQ7EcXHWUQPvPOkBiiYQBl+TWECgYEA/QoiRM251UsHqsbuZXG+ypKrjqeo0fJTCOO/2VgDbr6TyJ34q+mOuZR4Glu3ZZN7UTK+mf9AaApfvooFE8b52f95S28t2IG3boJ+a65oVyksa6GpbPpZ9HGAy8P9lwKLxHcCRD+Bnpglkg28P3ZK1e3sHLpQmzg6eR7bcFaO3GcCgYEA5S1QIpvfHlTFgCYejpg7VySWe9gn5W+U5nhMmwi/ybBHi7GCaPUtR9+Oz+BcQ/MWelJU0ApfF69A6rxj1TLHcPsDxNTehvNaKUYtOdfqTaQ9G3+eNRDwzm5ZO/yZNCFR03ZhP9f7raEZtNpExGR+QQrdGfraZM2Tq7k1Inz/YDkCgYB+Vxr5I4bPumCfoifRutM6LbU+yvN9r/JJk/1sNYexObJsDoPkwf6jJkA5WOXe75440o9cMJgl2lgnuJ4EW+rQL8COK3rGiS3fHYSlmzU5n7MwIk2HvhA//pQCKV9qkLjcZVdaYCMF3o2TuQvu289NtTeYuGYauh5n70I9etplrQKBgFeyDTYRG0HKAoFVFOBP1HU2JPdi8XFUT1AZvIaexHTzJY2I8XQkTZ1xKH7XRbcir/lCw/2P2m9/uoGYcUNF2ReclgadxkExodClb+zweFIZhOCe5vU7dEop46+WqzFNhrg6VmBNz5rTSLjxxNq58a4F8DFe0m88U0Ok7XYzCq3RAoGAQIO89vnFcjjcfc14NDfB67xBuZwatvTYHQ9ywiM13zAcS+FgOWIduM9WdcM5MclEHQPrZ2hJPLtSjtw5CjG+O5pPX7Q7rqSxzirZBSl2GhLVypuOHmUByisw5W74cIjdbEqoceCQ309oqhbvsJ61KHhO8+pYwxuA1p2owvhm/Hw=',
			'alipayrsaPublicKey' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvN4JFXKA0pxAdlNcHC88IYcqecaiu0YiWX7OGolT0LTSHy6JkNl5RYbkHprH2mvdKnkglJ9/e5RQ1DZWiCwbfHpicl140e/4ZEhq1WWFII9lYCzYXVl42D0R97sEiNmHPI/e0FKSLfb7HHE/FvPkxbrwKx2ZXJA4ip6tNSUFwnsTvJOdYKI74uz+99FIKM7Z6TLvezK0YJUm7QJV027kZscWtUwHoKQi7xN8oSRAs52MTujewHprLcup1bRUE4X6MCm3p2u5atgd6hS34mb7ZQXOW5Ta79LNeMvw2gN8IvSgnk9jAfFOxpc9f7Lzs9QJjCdoo1RgPzVCJXwuxBhBnQIDAQAB',
		]);
	}

	public function log($content = '')
	{
		if(is_array($content) || is_object($content)) {
			$content = JSON($content);
		}
		file_put_contents(TEMP_PATH . 'Test.log', '['.gsdate('H:i:s').']'.' '.$content."\r\n", FILE_APPEND);
	}

	//资金授权冻结
	public function AlipayFundAuthOrderFreeze()
	{
		$auth_code = '287253868047025608';
		$BizContent = [
			//支付授权码
			'auth_code' => $auth_code,
			//授权码类型
			'auth_code_type' => 'bar_code',
			//商户资金授权订单号
			'out_order_no' => 'O' . get_order_number(),
			//商户资金授权流水号
			'out_request_no' => 'R' . get_order_number(),
			//业务订单的简单描述
			'order_title' => '资金授权冻结',
			//需要冻结的金额
			'amount' => '1000',
			//收款方支付宝账号(可选)
			'payee_logon_id' => '',
			//收款方的支付宝唯一用户号(可选)
			'payee_user_id' => '',
			//订单付款超时时间
			'pay_timeout' => '1m',
			//销售产品码
			'product_code' => 'PRE_AUTH',
			//业务扩展参数
			'extend_params' => [
				//系统服务商PID
				'sys_service_provider_id' => Pay::config('alipay')['sys_service_provider_id'],
			],
		];
		if(preg_match('/^fp[a-z0-9]{33}$/', $auth_code)) {
			$BizContent['auth_code_type'] = 'security_code';
			$BizContent['scene_code'] = 'HOTEL';
		}
		$req = $this->AopSdk->load('AlipayFundAuthOrderFreeze');
		$res = $this->AopSdk->execute($BizContent, null, null);
		if($res['contents']['code'] = 10000) {
			$this->log($BizContent);
			$this->log($res);
		}
		echo JSON($res);
	}

	//资金授权解冻
	public function AlipayFundAuthOrderUnfreeze()
	{
		$BizContent = [
			//支付宝资金授权订单号
			'auth_no' => '2019122010002001640209313953',
			//商户资金授权流水号
			'out_request_no' => 'R20191220145330515705',
			//本次操作解冻的金额
			'amount' => '500',
			//商户本次资金操作的请求流水号
			'remark' => '资金授权解冻',
		];
		$req = $this->AopSdk->load('AlipayFundAuthOrderUnfreeze');
		$res = $this->AopSdk->execute($BizContent, null, null);
		if($res['status'] == 1) {
			$this->log($BizContent);
			$this->log($res);
		}
		echo JSON($res);
	}

	//资金授权查询
	public function AlipayFundAuthOperationDetailQuery()
	{
		$BizContent = [
			'out_order_no' => 'O20191220145330515652',
			'out_request_no' => 'R20191220145330515705',
		];
		$req = $this->AopSdk->load('AlipayFundAuthOperationDetailQuery');
		$res = $this->AopSdk->execute($BizContent, null, null);
		if($res['status'] == 1) {
			$this->log($BizContent);
			$this->log($res);
		}
		echo JSON($res);
	}

	//资金授权撤销
	public function AlipayFundAuthOperationCancel()
	{
		$BizContent = [
			'out_order_no' => 'O20191219171753883763',
			'out_request_no' => 'R20191219171753883846',
			'remark' => '资金授权撤销',
		];
		$req = $this->AopSdk->load('AlipayFundAuthOperationDetailQuery');
		$res = $this->AopSdk->execute($BizContent, null, null);
		if($res['status'] == 1) {
			$this->log($BizContent);
			$this->log($res);
		}
		echo JSON($res);
	}

	//资金授权转支付
	public function AlipayTradePay()
	{
		$BizContent = [
			'scene' => 'bar_code',
			'authcode' => '285487986271848010',
			'product_code' => 'PRE_AUTH',
			'subject' => '资金授权转支付',
			'body' => '',
			//买家id
			'buyer_id' => '2088102179345644',
			//卖家id
			'seller_id' => '2088102179546180',
			//商户交易号
			'out_trade_no' => 'P' . get_order_number(),
			//支付金额
			'total_amount' => '490.00',
			//支付宝资金授权订单号
			'auth_no' => '2019122010002001640209313953',
		];
		$req = $this->AopSdk->load('AlipayTradePay');
		$res = $this->AopSdk->execute($BizContent, null, null);
		if($res['status'] == 1) {
			$this->log($BizContent);
			$this->log($res);
		}
		echo JSON($res);
	}

	//交易查询
	public function AlipayTradeQuery()
	{
		$BizContent = [
			'out_trade_no' => 'T20191220150617281671',
		];
		$req = $this->AopSdk->load('AlipayTradeQuery');
		$res = $this->AopSdk->execute($BizContent, null, null);
		if($res['status'] == 1) {
			$this->log($BizContent);
			$this->log($res);
		}
		echo JSON($res);
	}

	//交易退款
	public function AlipayTradeRefund()
	{
		$BizContent = [
			'out_trade_no' => 'T20191220150617281671',
			'refund_amount' => '490.00',
		];
		$req = $this->AopSdk->load('AlipayTradeRefund');
		$res = $this->AopSdk->execute($BizContent, null, null);
		if($res['status'] == 1) {
			$this->log($BizContent);
			$this->log($res);
		}
		echo JSON($res);
	}

}

