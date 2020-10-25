jQuery.noConflict();
function inEffect(obj,n){
	$(".money-item div").removeClass('j-selected');
	$(obj).addClass('j-selected');
}
function changeSelected(n,index,obj,needPay){
	$('#'+index).val(n);
	rechargeMoney(needPay);
	inEffect(obj,2);
}

function rechargeMoney(n){
	$("#needPay").val(n);
}


function toPay(){
	var params = {};
		params.payObj = "recharge";
		params.targetType = 0;
		params.needPay = $.trim($("#needPay").val());
		params.payCode = $("input[name='payCode']:checked").val();	//支付方式
		params.itemId = $.trim($("#itemId").val());
		params.trade_no = $.trim($("#trade_no").val());
	if(params.itemId==0 && params.needPay<=0){
		WST.msg('请输入充值金额', 'info');
		return;
	}
	if(params.payCode==""){
		WST.msg('请先选择支付方式','info');
		return;
	}
	// console.log(params)
	if(params.payCode=="weixinpays"){
		location.href = WST.U('mobile/Weixinpays/toWeixinPay',params);
	}else{
		location.href = WST.U('mobile/Alipays/toAliPay',params);
	}
}

$(function(){
	jQuery(".wst-frame2:first").click();
});