<?php
/**
 * Author: wanghongbin
 * 支付宝支付成功异步回调页面
 */
header("Content-type:text/html;charset=utf-8");
set_time_limit(0);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
define('TYPO3_MODE', true);

date_default_timezone_set('Asia/Shanghai');

//connect db
$configArray = include_once("../../../../typo3conf/LocalConfiguration.php");
$configDB = $configArray['DB']['Connections']['Default'];
$con = new mysqli($configDB['host'],$configDB['user'],$configDB['password'],$configDB['dbname']);

if(!$con){
    die("connect error:".mysqli_connect_error());
}
file_put_contents(date('Y-m-d') . '.log', date('Y-m-d H:i:s') . '-zfb:'.json_encode($_POST). chr(10) . chr(10), FILE_APPEND | LOCK_EX);

require_once('../lib/alipay/alipay_notify.class.php');

include_once("config.php");

$alipay_config['partner'] = $GLOBALS['ALIPAY_PARTNER']; //合作者身份
$alipay_config['key'] = $GLOBALS['ALIPAY_KEY']; //安全校验码
$alipay_config['sign_type'] = strtoupper('MD5');
$alipay_config['input_charset'] = strtolower('utf-8');
$alipay_config['cacert'] = str_replace("/api", "", dirname(__FILE__)).'/lib/alipay/cacert.pem';
$alipay_config['transport'] = 'http';

//计算得出通知验证结果
$alipayNotify = new \Jykj\Donation\Controller\AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
file_put_contents(date('Y-m-d') . '.log', date('Y-m-d H:i:s') . ':'.json_encode($verify_result). chr(10) . chr(10), FILE_APPEND | LOCK_EX);
if ($verify_result) {
    //交易状态
    $trade_status = $_POST['trade_status'];
    if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
        $out_trade_no = end(explode("-", $_POST['out_trade_no'])); //商户订单号
        
		$query = "update tx_donation_domain_model_pay set hidden=0,ordernumber=? WHERE uid = ? ";
		$stmt = $con->prepare($query);
		$stmt->bind_param('ss',$_POST['trade_no'],$out_trade_no);
		$rest = $stmt->execute();
    }
    $con->close();
    echo "success";//请不要修改或删除
} else {
	$con->close();
    echo "fail";
}
exit;