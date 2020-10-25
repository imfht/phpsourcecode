<?php
/**
 * 微信支付成功异步回调页面
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
file_put_contents(date('Y-m-d') . '.log', date('Y-m-d H:i:s') . '-wx:'.file_get_contents('php://input') . chr(10), FILE_APPEND | LOCK_EX);

require_once('../lib/wechat/WxPayPubHelper.php');
include_once("config.php");

//使用通用通知接口
$notify = new \Jykj\Donation\Weixin\Notify_pub();

//存储微信的回调
$xml = file_get_contents('php://input'); //$GLOBALS['HTTP_RAW_POST_DATA'];
$notify->saveData($xml);

//验证签名，并回应微信。
//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
//尽可能提高通知的成功率，但微信不保证通知最终能成功。
if ($notify->checkSign() == FALSE) {
    $notify->setReturnParameter("return_code", "FAIL");//返回状态码
    $notify->setReturnParameter("return_msg", "签名失败");//返回信息
} else {
    $notify->setReturnParameter("return_code", "SUCCESS");//设置返回码
}
$returnXml = $notify->returnXml();
echo $returnXml; //应答给微信

//==商户根据实际情况设置相应的处理流程，此处仅作举例=======

if ($notify->checkSign() == TRUE) {
    if ($notify->data["result_code"] == 'SUCCESS') {
        //此处应该更新一下订单状态，商户自行增删操作
        //商户自行增加处理流程,
        $out_trade_no = end(explode("-", $notify->data['out_trade_no'])); //商户订单号
        //更新系统数据
        $query = "update tx_donation_domain_model_pay set hidden=0,ordernumber=? WHERE uid = ? ";
		$stmt = $con->prepare($query);
		$stmt->bind_param('ss',$notify->data['transaction_id'],$out_trade_no);
		$rest = $stmt->execute();
    }
}
$con->close();
exit;
