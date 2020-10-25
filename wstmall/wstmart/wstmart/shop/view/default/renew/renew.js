function getPayUrl(){
	var params = {};
		params.payObj = "enter";
		params.needPay = $.trim($("#needPay").val());
		params.payCode = $.trim($("#payCode").val());
	if(params.needPay==0){
		WST.msg('年费金额有误', {icon: 5});
		return;
	}
	if(params.payCode==""){
		WST.msg('请先选择支付方式', {icon: 5});
		return;
	}
	jQuery.post(WST.U('shop/'+params.payCode+'/get'+params.payCode+"URL"),params,function(data) {
		var json = WST.toJson(data);
		if(json.status==1){
			if(params.payCode=="alipays"){
				$("#alipayform").html(json.result);
			}else{
				location.href = json.url;
			}
		}else{
			WST.msg('缴纳年费失败', {icon: 5});
		}
	});
}

$(function(){
	$("#wst-check-orders").click(function(){
		$("#wst-orders-box").slideToggle(600);
	});
	$("div[class^=wst-payCode]").click(function(){
		var payCode = $(this).attr("data");
		$("div[class^=wst-payCode]").each(function(){
			$(this).removeClass().addClass("wst-payCode-"+$(this).attr("data"));
		});
		$(this).removeClass().addClass("wst-payCode-"+payCode+"-curr");
		$("#payCode").val(payCode);
	});
	if($("div[class^=wst-payCode]").length>0){
		$("div[class^=wst-payCode]")[0].click();
	}
});

function renew(){
	var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('shop/shops/renew'),{},function(data,textStatus){
		layer.close(loading);
		var json = WST.toJson(data);
		if(json.status=='1'){
			WST.msg(json.msg,{icon:1});
		}else{
			WST.msg(json.msg,{icon:2});
		}
	});
}

function payByWallet(){
	var params = WST.getParams('.j-ipt');
	if(params.payPwd==""){
		WST.msg('请输入密码', {icon: 5});
		return;
	}
	if(window.conf.IS_CRYPT=='1'){
		var public_key=$('#token').val();
		var exponent="10001";
		var rsa = new RSAKey();
		rsa.setPublic(public_key, exponent);
		params.payPwd = rsa.encrypt(params.payPwd);
	}
	var load = WST.load({msg:'正在核对支付密码，请稍后...'});
	$.post(WST.U('shop/wallets/payByWallet'),params,function(data,textStatus){
		layer.close(load);
		var json = WST.toJson(data);
		if(json.status==1){
			WST.msg(json.msg, {icon: 1,time:1500},function(){
				window.location = WST.U('shop/logmoneys/shopmoneys');
			});
		}else{
			WST.msg(json.msg,{icon:2,time:1500});
		}
	});
}

function setPaypwd(){
	layerbox =	layer.open({
		title:['设置支付密码','text-align:left'],
		type: 1,
		area: ['450px', '240px'],
		content: $('.j-paypwd-box'),
		btn: ['设置支付密码，并支付年费', '关闭'],
		yes: function(index, layero){
			var newPass = $.trim($("#payPwd").val());
			var reNewPass = $.trim($("#reNewPass").val());
			if(newPass==""){
				WST.msg("请输入支付密码！");
				return false;
			}
			if(reNewPass==""){
				WST.msg("请输入确认支付密码！");
				return false;
			}
			if(newPass!=reNewPass){
				WST.msg("密码不一致！");
				return false;
			}
			if(window.conf.IS_CRYPT=='1'){
				var public_key=$('#token').val();
				var exponent="10001";
				var rsa = new RSAKey();
				rsa.setPublic(public_key, exponent);
				newPass = rsa.encrypt(newPass);
				reNewPass = rsa.encrypt(reNewPass);
			}
			var load = WST.load({msg:'正在提交支付密码，请稍后...'});
			$.post(WST.U('shop/users/payPassEdit'),{newPass:newPass,reNewPass:reNewPass},function(data,textStatus){
				layer.close(load);
				var json = WST.toJson(data);
				if(json.status==1){
					WST.msg(json.msg, {icon: 1,time:1500},function(){
						layer.close(layerbox);
						payByWallet();
					});
				}else{
					WST.msg(json.msg,{icon:2,time:1500});
				}
			});

			return false;
		},
		btn2: function(index, layero){
			$('#paypwd-box').hide();
		}
	});
}