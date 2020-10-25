<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['price'])) die('空的内容！^_^');

//条件检查
if (!check_account_addr($auth['id'])) info_jump('收货信息不完整！^_^', '?c=account&a=account_addr_set');
$price = doubleval($_GET['price']);
if (0.01 > $price) info_jump('充值金额有误！^_^', '?c=account&a=account_charge');

//插入充值记录
$rs = dt_query("INSERT INTO account_charge_log (user_id, price, status, c_at) VALUES (".$auth['id'].", '$price', 0, ".time().")");
if (!$rs) die('插入account_charge_log数据失败！^_^');

//获取订单号
$out_trade_no = dt_query_one("SELECT LAST_INSERT_ID()")[0];

$account_addr = dt_query_one("SELECT * FROM account_addr WHERE id = ".$auth['id']." LIMIT 1");
if (!$account_addr) die('获取account_addr数据失败！^_^');

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>支付宝纯担保交易接口接口</title>
</head>
<?php

require_once("alipay/alipay.config.php");
require_once("alipay/lib/alipay_submit.class.php");

$payment_type = "1";
$notify_url = 'http://'.$_SERVER['HTTP_HOST'].'/account/alipay/notify_url.php';
$return_url = 'http://'.$_SERVER['HTTP_HOST'].'/account/alipay/return_url.php';
$seller_email = 'tanxin_1989@qq.com';
$out_trade_no = $out_trade_no;
$subject = '衡阳搜索HIICI-账户充值-'.$account_addr['name'];
$price = $price;
$quantity = "1";
$logistics_fee = "0.00";
$logistics_type = "EXPRESS";
$logistics_payment = "SELLER_PAY";
$body = '充值账户:'.$account_addr['name'].' 流水号:'.$out_trade_no.' 充值金额:'.$price;
$show_url = 'http://'.$_SERVER['HTTP_HOST'].'/account-index.htm';
$receive_name = $account_addr['name'];
$receive_address = $account_addr['addr'];
$receive_zip = $account_addr['p_code'];
$receive_phone = '';
$receive_mobile = $account_addr['phone'];

$parameter = array(
	"service" => "create_partner_trade_by_buyer",
	"partner" => trim($alipay_config['partner']),
	"payment_type"	=> $payment_type,
	"notify_url"	=> $notify_url,
	"return_url"	=> $return_url,
	"seller_email"	=> $seller_email,
	"out_trade_no"	=> $out_trade_no,
	"subject"	=> $subject,
	"price"	=> $price,
	"quantity"	=> $quantity,
	"logistics_fee"	=> $logistics_fee,
	"logistics_type"	=> $logistics_type,
	"logistics_payment"	=> $logistics_payment,
	"body"	=> $body,
	"show_url"	=> $show_url,
	"receive_name"	=> $receive_name,
	"receive_address"	=> $receive_address,
	"receive_zip"	=> $receive_zip,
	"receive_phone"	=> $receive_phone,
	"receive_mobile"	=> $receive_mobile,
	"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
echo $html_text;

?>
</body>
</html>
