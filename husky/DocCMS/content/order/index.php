<?php
function index()
{
}
function create()
{
	global $db,$request;
	foreach ($request as $k=>$v)
	{
		$request[$k]=RemoveXSS($v);//xss
	}
	require(ABSPATH.'/admini/models/order.php');
	//验证
	
	$order = new order();
	$order->addnew($request);
	//必填字段

	$order->dtTime=date('Y-m-d H:i:s');
	$order->channelId=$request['p'];
	//可选字段
	$order->title=$request['title'];
	$order->remark=$request['remark'];
	$order->custom=@implode('<|@|>',$request['custom']);
	
	if($order->save())
	{
		if(orderISON)
		{
			sys_mail(' 订单提醒','最新订单提醒：您的网站：<a href="http://'.WEBURL.'">'.WEBURL.'</a> 有最新订单，订单内容——'.$request['productName'].'，请及时前往查看！');
		}
		echo "<script language='javascript'>alert('恭喜，您的合作提案已提交成功，工作人员会在稍后与您联系！');window.location.href='".sys_href($request['p'])."';</script>";
		exit;       
	}
	else
	{ 
		echo "<script language='javascript'>alert('对不起，系统错误，您的合作提案未能及时提交，请电话与我们联系。');window.location.href='".sys_href($request['p'])."';</script>"; 
		exit;
	}
}

?>