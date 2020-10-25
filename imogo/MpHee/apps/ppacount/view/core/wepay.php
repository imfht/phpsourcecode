<html>
<head>
<meta charset="utf-8">
<title>价值积分支付界面</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;"  />
<style type="text/css">
body{ 
background: #f5faff;
}
.con{
width: 100%;
margin:100px auto 0;
text-align: center;
}
.button{
width: 140px;
line-height: 38px;
text-align: center;
font-weight: bold;
color: #fff;
text-shadow:1px 1px 1px #333;
border-radius: 5px;
margin:0 20px 20px 0;
position: relative;
overflow: hidden;
}
.button.yellow{
border:1px solid #d2a000;
box-shadow: 0 1px 2px #fedd71 inset,0 -1px 0 #a38b39 inset,0 -2px 3px #fedd71 inset;
background: -webkit-linear-gradient(top,#fece34,#d8a605);
background: -moz-linear-gradient(top,#fece34,#d8a605);
background: linear-gradient(top,#fece34,#d8a605);
}
</style>
</head>
<script language="javascript">
function callpay()
{
	WeixinJSBridge.invoke('getBrandWCPayRequest',{$page},function(res){
	if(res.err_msg == "get_brand_wcpay_request:ok"){
	    alert("支付成功");
		var wxid = '<?php echo $_GET['wxid'];?>';
		location.replace("http://wx.miucity.com/index.php?r=huiyuan/mobile/index&wxid="+wxid);
	}else if(res.err_msg == "get_brand_wcpay_request:cancel"){
	    alert("支付取消");
		var wxid = '<?php echo $_GET['wxid'];?>';
		location.replace("http://wx.miucity.com/index.php?r=huiyuan/mobile/index&wxid="+wxid);
	}else if(res.err_msg == "get_brand_wcpay_request:fail"){
	    alert("支付失败");
		var wxid = '<?php echo $_GET['wxid'];?>';
		location.replace("http://wx.miucity.com/index.php?r=huiyuan/mobile/index&wxid="+wxid);
	}
	});
}
</script>
<body>
<div class="con">
积分的充值卡
</br>
<button class="button yellow" type="button" onclick="callpay()">确定支付</button>
</div>
</body>
</html>
