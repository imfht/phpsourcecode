<?php

class AlipayController extends Controller
{

	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('notify_url','return_url','ajax'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	
	public function actionIndex(){
		header("Content-Type: text/html; charset=utf-8");
		//echo Yii::app()->getBaseUrl();exit;
		require(dirname(Yii::app()->BasePath)."/alipays/alipay.config.php");
		require(dirname(Yii::app()->BasePath)."/alipays/lib/alipay_submit.class.php");
	/**************************请求参数**************************/

			//支付类型
			$payment_type = "1";
			//必填，不能修改
			//服务器异步通知页面路径
			//$notify_url = Yii::app()->params['returnHost']."alipay/notify_url.html";
			$notify_url = Yii::app()->params['returnHost']."alipay/notify_url/";
			//需http://格式的完整路径，不允许加?id=123这类自定义参数
			//页面跳转同步通知页面路径
			$return_url = Yii::app()->params['returnHost']."alipay/return_url/";
			//需http://格式的完整路径，不允许加?id=123这类自定义参数
			//卖家支付宝帐户
			//$seller_email = $_POST['WIDseller_email'];
			$seller_email = '156472@qq.com';
			//必填
			//商户订单号
			
			$out_trade_no =intval($_POST['pay_number']) ;
			//$out_trade_no = "123222";
			//商户网站订单系统中唯一订单号，必填
			//订单名称
			$gameName=Games::model()->getGamesName($_POST['pay_game']);
			$subject = $gameName[0].$_POST['pay_server'];
			//必填
			//付款金额
			$total_fee = $_POST['price'];
			//必填
			//订单描述
			$body = '918游戏充值 用户名：'.Yii::app()->user->name.'为'.$gameName[0].$_POST['pay_server'].'充值'.$_POST['price']."元";
			//商品展示地址
			$show_url = $_POST['WIDshow_url'];
			//需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html
			//防钓鱼时间戳
			$anti_phishing_key = "";
			//若要使用请调用类文件submit中的query_timestamp函数
			//客户端的IP地址
			//$exter_invoke_ip = $_POST['WIDexter_invoke_ip'];
			$exter_invoke_ip=Yii::app()->request->userHostAddress;
			//非局域网的外网IP地址，如：221.0.0.1
			
			/*echo $out_trade_no."订单<br>";
			echo $subject."游戏名称<br>";
			echo $total_fee."钱<br>";
			echo $body."<br>";
			echo $show_url."<br>";
			echo $anti_phishing_key."<br>";
			echo $exter_invoke_ip."<br>";
			echo $_POST['pay_server_value'];
			//exit;*/

	/************************************************************/
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "create_direct_pay_by_user",
				"partner" => trim($alipay_config['partner']),
				"payment_type"	=> $payment_type,
				"notify_url"	=> $notify_url,
				"return_url"	=> $return_url,
				"seller_email"	=> $seller_email,
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"body"	=> $body,
				"show_url"	=> $show_url,
				"anti_phishing_key"	=> $anti_phishing_key,
				"exter_invoke_ip"	=> $exter_invoke_ip,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		echo $html_text;
		
		$gameServerTrue=Games::model()->getGamesServerTrue($_POST['pay_game'],$_POST['pay_server_value']);
		if($gameServerTrue){
			$order=new Order;
			$order->order_number=$out_trade_no;
			$order->mid=Yii::app()->user->id ;
			$order->gid=$_POST['pay_game'];
			$order->gid_server_id=$_POST['pay_server_value'];
			$order->price=$total_fee;
			$order->pay_type=$_POST['pay_type'];
			$order->pay_time=time();
			$order->pay_ip=Yii::app()->request->userHostAddress;
			$order->save(false);
		}
		
		
	}
	
	public function actionNotify_url(){
		/* */
		require(dirname(Yii::app()->BasePath)."/alipays/alipay.config.php");
		require(dirname(Yii::app()->BasePath)."/alipays/lib/alipay_notify.class.php");

		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();

		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代

			//Order::model()->updateAll(array('pay'=> 1),"order_number=".$_POST['out_trade_no']);
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

			//商户订单号
			$out_trade_no = $_POST['out_trade_no'];

			//支付宝交易号
			$trade_no = $_POST['trade_no'];

			//交易状态
			$trade_status = $_POST['trade_status'];


			if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序

				//注意：
				//该种交易状态只在两种情况下出现
				//1、开通了普通即时到账，买家付款成功后。
				//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}
			else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序

				//注意：
				//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

			echo "success";		//请不要修改或删除

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			echo "fail";

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
		/*     */
	}
	public function actionReturn_url(){
		/*  */
		require(dirname(Yii::app()->BasePath)."/alipays/alipay.config.php");
		require(dirname(Yii::app()->BasePath)."/alipays/lib/alipay_notify.class.php");
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
			Order::model()->updateAll(array('pay'=> 1),"order_number=".$_GET['out_trade_no']);
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
			
			//商户订单号
			$out_trade_no = $_GET['out_trade_no'];

			//支付宝交易号
			$trade_no = $_GET['trade_no'];

			//交易状态
			$trade_status = $_GET['trade_status'];


			if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
			}
			else {
			  echo "trade_status=".$_GET['trade_status'];
			}
			header("Content-Type: text/html; charset=utf-8");
			echo "验证成功<br />";

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			//如要调试，请看alipay_notify.php页面的verifyReturn函数
			header("Content-Type: text/html; charset=utf-8");
			echo "验证失败";
		}
		/*  */
	}
	
}

