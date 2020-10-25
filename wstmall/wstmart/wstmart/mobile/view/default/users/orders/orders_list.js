jQuery.noConflict();
// 提醒发货
function noticeDeliver(id){
  hideDialog('#wst-di-prompt');
  $.post(WST.U('mobile/orders/noticeDeliver'),{id:id},function(data){
      var json = WST.toJson(data);
      if(json.status==1){
    	WST.msg(json.msg,'success');
        setTimeout(function(){
        	reFlashList();
        },2000);
      }else{
        WST.msg(json.msg,'info');
      }
  });
}

// 拒收
function showNoticeBox(event){
    $("#wst-event4").attr("onclick","javascript:"+event);
    $("#noticeBox").dialog("show");
}


// 获取订单列表
function getOrderList(){
  $('#Load').show();
    loading = true;
    var param = {};
    param.type = $('#type').val();
    param.pagesize = 10;
    param.page = Number( $('#currPage').val() ) + 1;
    $.post(WST.U('mobile/orders/getOrderList'), param, function(data){
        var json = WST.toJson(data);
        var html = '';
        if(json && json.data && json.data.length>0){
          var gettpl = document.getElementById('shopList').innerHTML;
          laytpl(gettpl).render(json.data, function(html){
            $('#order-box').append(html);
          });

          $('#currPage').val(json.current_page);
          $('#totalPage').val(json.last_page);
        }else{
	        html += '<div class="wst-prompt-icon"><img style="margin-left:0.07rem" src="'+ window.conf.MOBILE +'/img/nothing-order.png"></div>';
	        html += '<div class="wst-prompt-info">';
	        html += '<p>暂无订单</p>';
	        html += '<button class="ui-btn-s" onclick="javascript:WST.intoIndex();">去逛逛</button>';
	        html += '</div>';
	        $('#order-box').html(html);
        }
        WST.imgAdapt('j-imgAdapt');
        loading = false;
        $('#Load').hide();
        echo.init();//图片懒加载
    });
}

// 刷新列表页
function reFlashList(){
  $('#currPage').val('0');
  $('#order-box').html(' ');
  getOrderList();
}

function showCancelBox(event){
    $("#wst-event0").attr("onclick","javascript:"+event);
    jQuery('#cover2').attr("onclick","javascript:hideCancelBox();").show();
    jQuery('#cancelBox').animate({"bottom": 0}, 500);
}
function hideCancelBox(){
    jQuery('#cancelBox').animate({'bottom': '-100%'}, 500);
    jQuery('#cover2').hide();
}
// 取消订单
function cancelOrder(oid){
    hideCancelBox();
    var flag=false;
    $('.active').each(function(k,v){
        if($(this).prop('checked')){
            flag = true;
            $('#reason').val($(this).val());
        }
    });
    if(!flag){
        WST.msg('请选择取消订单的原因');
        return;
    }
  $.post(WST.U('mobile/orders/cancellation'),{id:oid,reason:$('#reason').val()},function(data){
    var json = WST.toJson(data);
    if(json.status==1){
	  	WST.msg(json.msg,'success');
        hideCancelBox();
	    setTimeout(function(){
	    	reFlashList();
	    },2000);
    }else{
      WST.msg(json.msg,'info');
    }
  });
}

// 拒收
function showRejectBox(event){
    $("#wst-event3").attr("onclick","javascript:"+event);
    $("#rejectBox").dialog("show");
}

function rejectOrder(oid){
  var param = {};
  param.id=oid;
  param.reason=$('#reject').val();
  param.content=$('#rejectContent').val();
  if(param.reason == 10000){
    var content = $.trim($('#rejectContent').val());
    if(content == ''){
      WST.msg('请输入拒收原因','info');
      return;
    }
  }
  
  $.post(WST.U('mobile/orders/reject'),param,function(data){
    
    hideDialog('#rejectBox');

    var json = WST.toJson(data);
    if(json.status==1){
      $('#content').val(' ');
	  	WST.msg(json.msg,'success');
	    setTimeout(function(){
	    	reFlashList();
	    },2000);
    }else{
      WST.msg(json.msg,'info');
    }
  });

}

// 显示退款原因
function showRefundReasonBox(){
    $("#wst-event4").attr("onclick","javascript:hideRefundReasonBox();");
    jQuery('#cover2').attr("onclick","javascript:hideRefundReasonBox();").show();
    jQuery('#refundReasonBox').animate({"bottom": 0}, 500);
}
// 隐藏退款原因
function hideRefundReasonBox(){
    jQuery('#refundReasonBox').animate({'bottom': '-100%'}, 500);
    jQuery('#cover2').hide();
}
function changeRefundReason(obj){
    var reason = $(obj).val();
    var reasonReasonText = $(obj).attr('d-name');
    $('#refundReason').val(reason);
    $('#refundReasonText').html(reasonReasonText);
    if(reason==10000){
        $('#refundTr').show();
    }else{
        $('#refundTr').hide();
    }
}

//退款
function showRefundBox(id){
    // 重置表单
    $('#refundReason').val(1);
    $('#refundContent').html(' ');
    $('#money').val(' ');
    $('#refundTr').hide();
    $.post(WST.U('mobile/orders/getRefund'),{id:id},function(data){
        $('#realTotalMoney').html('¥'+data.realTotalMoney);
        $('#useScore').html(data.useScore);
        $('#scoreMoney').html('¥ '+data.scoreMoney);
        // 弹出层滚动条
        var clientH = WST.pageHeight();// 屏幕高度
        var boxheadH = $('#refund-boxTitle').height();// 弹出层标题高度
        var contentH = $('#refund-content').height(); // 弹出层内容高度
        $('#refund-content').css('height',clientH-boxheadH+'px');
        $("#wst-event8").attr("onclick","javascript:refund("+id+")");
        reFundDataShow();
    })
}
function changeRefundType(v){
  if(v==10000){
    $('#refundTr').show();
  }else{
    $('#refundTr').hide();
  }
}
//弹框
function reFundDataHide(){
    $('#shopBox').show();
    jQuery('#refundFrame').animate({'right': '-100%'}, 500);
    jQuery('#cover').hide();
}
function reFundDataShow(){
    jQuery('#cover').attr("onclick","javascript:reFundDataHide();").show();
    jQuery('#refundFrame').animate({"right": 0}, 500);
    setTimeout(function(){$('#shopBox').hide();},600)
}
// 退款
function refund(id){
  var params = {};
      params.reason = $.trim($('#refundReason').val());
      params.content = $.trim($('#refundContent').val());
      params.money = $.trim($('#money').val());
      params.id = id;
      if(params.money<0 || params.money==''){
        WST.msg('无效的退款金额','info');
        return;
      }
      if(params.reason==10000){
        var content = $.trim($('#refundContent').val());
            if(content == ''){
               WST.msg('请输入原因','info');
              return;
            }
      }
      $.post(WST.U('mobile/orderrefunds/refund'),params,function(data){
          var json = WST.toJson(data);
          if(json.status==1){
            WST.msg('申请退款成功','success');
            setTimeout(function(){
            	reFundDataHide();
            	reFlashList();
            },2000);
          }else{
            WST.msg(json.msg,'info');
          }
      })
}

function changeRejectType(v){
  if(v==10000){
    $('#rejectTr').show();
  }else{
    $('#rejectTr').hide();
  }
}

//  隐藏对话框
function hideDialog(id){
  $(id).dialog("hide");
}

// 确认收货
function receive(oid){
  hideDialog('#wst-di-prompt');
  $.post(WST.U('mobile/orders/receive'),{id:oid},function(data){
      var json = WST.toJson(data);
      if(json.status==1){
      	WST.msg(json.msg,'success');
        setTimeout(function(){
        	reFlashList();
        },2000);
      }else{
        WST.msg(json.msg,'info');
      }
  });
}



/*********************** 订单详情 ****************************/
//弹框
function dataShow(title){
    jQuery('#cover').attr("onclick","javascript:dataHide();").show();
    jQuery('#frame').animate({"right": 0}, 500);
    setTimeout(function(){$('#shopBox').hide();},600)
    $('#wordTitle').html(title);
}
function dataHide(){
    $('#shopBox').show();
    jQuery('#frame').animate({'right': '-100%'}, 500);
    jQuery('#cover').hide();
}



function getOrderDetail(oid){
  $.post(WST.U('mobile/orders/getDetail'),{id:oid},function(data){
      var json = WST.toJson(data);
      if(json.status!=-1){
        var gettpl1 = document.getElementById('detailBox').innerHTML;
          laytpl(gettpl1).render(json, function(html){
            $('#content').html(html);
            // 弹出层滚动条
            var clientH = WST.pageHeight();// 屏幕高度
            var boxheadH = $('#boxTitle').height();// 弹出层标题高度
            var contentH = $('#content').height(); // 弹出层内容高度
            $('#content').css('height',clientH-boxheadH+'px');
            dataShow('订单详情');
          });
      }else{
        WST.msg(json.msg,'info');
      }
  });
}

//加入购物车
function addCart(goodsId, goodsSpecId, goodsType){
	$.post(WST.U('mobile/carts/addCart'),{goodsId:goodsId,goodsSpecId:goodsSpecId,buyNum:1,rnd:Math.random()},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,'success');
	    	 if(type==1){
	    		 setTimeout(function(){
	    			 if(goodsType==1){
	    				 location.href=WST.U('mobile/carts/'+json.data.forward);
	    			 }else{
	    				 location.href=WST.U('mobile/carts/settlement');
	    			 }
	    		 },1000);
	    	 }
	     }else{
	    	 WST.msg(json.msg,'info');
	     }
	});
}
// 跳转售后申请页
function afterSale(orderId){
  location.href = WST.U('mobile/orderservices/apply',{orderId:orderId});
}


// 跳转到评价页
function toAppr(oid){
  location.href=WST.U('mobile/orders/orderappraise',{'oId':oid});
}
// 投诉
function complain(oid){
  location.href=WST.U('mobile/ordercomplains/complain',{'oId':oid});
}

//余额支付
function walletPay(type){
	var payPwd = $('#payPwd').val();
	if(!payPwd){
		WST.msg('请输入支付密码','info');
		return;
	}
	if(type==0){
		var payPwd2 = $('#payPwd2').val();
		if(payPwd2==''){
	    	WST.msg('确认密码不能为空','info');
	        return false;
	    }
		if(payPwd!=payPwd2){
	    	WST.msg('确认密码不一致','info');
	        return false;
	    }
	}
    if(window.conf.IS_CRYPTPWD==1){
        var public_key=$('#key').val();
        var exponent="10001";
   	    var rsa = new RSAKey();
        rsa.setPublic(public_key, exponent);
        var payPwd = rsa.encrypt(payPwd);
    }
	var params = {};
	if(type==0){
		params.newPass = payPwd;
		$.post(WST.U('mobile/users/editpayPwd'),params,function(data,textStatus){
			WST.noload(); 
			var json = WST.toJson(data);
		    if(json.status==1){
		    	WST.load('成功设置密码，<br>订单支付中···');
		    }else{
		    	WST.msg(json.msg,'info');
		    }
		});
	}else{
		WST.load('正在核对密码···');
	}
    params.payPwd = payPwd;
    params.orderNo = $('#orderNo').val();
    params.isBatch = $('#isBatch').val();
    $('.wst-btn-dangerlo').attr('disabled', 'disabled');
    setTimeout(function(){
	$.post(WST.U('mobile/wallets/payByWallet'),params,function(data,textStatus){
		WST.noload(); 
		var json = WST.toJson(data);
	    if(json.status==1){
	    	WST.msg(json.msg,'success');
	        setTimeout(function(){
	        	location.href = WST.U('mobile/orders/index');
	        },2000);
	    }else{
	    	WST.msg(json.msg,'info');
	        setTimeout(function(){
	            $('.wst-btn-dangerlo').removeAttr('disabled');
	        },2000);   
	    }
	});
    },1000);
}
//选择支付方式
function choicePay(pkey){
	location.href=WST.U('mobile/orders/succeed',{'pkey':pkey});
}
//跳转支付
function toPay(pkey,n){
	if(n=='alipays'){
		   location.href=WST.U('mobile/alipays/toAliPay',{'pkey':pkey});
	}else if(n=='wallets'){
    location.href = WST.U('mobile/wallets/payment',{'pkey':pkey});
  }else if(n=='weixinpays'){
    location.href = WST.U('mobile/weixinpays/toWeixinPay',{'pkey':pkey});
  }
}