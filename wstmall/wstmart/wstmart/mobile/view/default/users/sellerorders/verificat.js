jQuery.noConflict();

/**
 * 查询订单信息
 */
function getVerificatOrder(){
	var params = {};
	var verificationCode = $("#verificationCode").val();
	$('#orderInfo').html("");
	if(verificationCode.length<10){
		WST.msg('请输入正确的核验码',{icon:2});
		return;
	}
	params.verificationCode = verificationCode;
	$('#Load').show();
	$.post(WST.U('mobile/orders/getVerificatOrder'),params,function(data,textStatus){
	    $('#Load').hide();
	    var json = WST.toJson(data);
	    if(json.status=='1'){
	       	var gettpl1 = document.getElementById('detailBox').innerHTML;
          	laytpl(gettpl1).render(json.data, function(html){
	       		$('#orderInfo').html(html);
	       	});
	    }else{
	      	WST.msg(json.msg,{icon:2});
	    }
	});
}



/**
 * 验证确认核销
 */
function orderVerificat() {
	var params = {};
	var verificationCode = $("#verificationCode").val();
	if(verificationCode.length<10){
		WST.msg('请输入正确的核验码',{icon:2});
		return;
	}
	params.verificationCode = verificationCode;
	
	$.post(WST.U('mobile/orders/orderVerificat'),params,function(data,textStatus){
	    var json = WST.toJson(data);
	    if(json.status=='1'){
	       	WST.msg(json.msg);
	       	getVerificatOrder();
	       	WST.dialogHide("prompt")
	    }else{
	      	WST.msg(json.msg,{icon:2});
	    }
	});
  	
	
}