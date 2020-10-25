function submitOrder(){
	var params = WST.getParams('.j-ipt');
	params.isUseScore = $('#isUseScore').prop('checked')?1:0
	var load = WST.load({msg:'正在提交，请稍后...'});
	$.post(WST.U('home/orders/quickSubmit'),params,function(data,textStatus){
		layer.close(load);   
		var json = WST.toJson(data);
	    if(json.status==1){
	    	 WST.msg(json.msg,{icon:1},function(){
	    		 location.href=WST.U('home/orders/succeed','pkey='+json.pkey);
	    	 });
	    }else{
	    	WST.msg(json.msg,{icon:2});
	    }
	});
}

function inEffect(obj,n){
	$(obj).addClass('j-selected').siblings('.wst-frame'+n).removeClass('j-selected');
}
function changeSelected(n,index,obj){
	$('#'+index).val(n);
	inEffect(obj,2);
}
function getCartMoney(){
	var params = {};
	params.isUseScore = $('#isUseScore').prop('checked')?1:0;
	params.useScore = $('#useScore').val();
	params.rnd = Math.random();
	params.deliverType = 1;
	var couponIds = [];
	$('.j-shop').each(function(){
		couponIds.push($(this).attr('dataval')+":"+$('#couponId_'+$(this).attr('dataval')).val());
	});
	params.couponIds = couponIds.join(',');
	var load = WST.load({msg:'正在计算订单价格，请稍后...'});
	$.post(WST.U('home/carts/getQuickCartMoney'),params,function(data,textStatus){
		layer.close(load);  
		var json = WST.toJson(data);
		if(json.status==1){
		    json = json.data;
		    for(var key in json.shops){
		    	$('#shopC_'+key).html(json.shops[key]['goodsMoney']);
		    }
		    $('#maxScoreSpan').html(json.maxScore);
		    $('#maxScoreMoneySpan').html(json.maxScoreMoney);
		    $('#isUseScore').attr('dataval',json.maxScore);
		    $('#useScore').val(json.useScore);
		    $('#orderScore').html(json.orderScore);
		    $('#scoreMoney2').html(json.scoreMoney);
		 	$('#totalMoney').html(json.realTotalMoney);
		}
	});
}
function checkScoreBox(v){
    if(v){
    	var val = $('#isUseScore').attr('dataval');
    	$('#useScore').val(val);
        $('#scoreMoney').show();
    }else{
    	$('#scoreMoney').hide();
    }
    getCartMoney();
}


var invoicebox;
function changeInvoice(t,str,obj){
	var param = {};
	param.isInvoice = $('#isInvoice').val();
	param.invoiceId = $('#invoiceId').val();
	var loading = WST.load({msg:'正在请求数据，请稍后...'});
	$.post(WST.U('home/invoices/index'),param,function(data){
		layer.close(loading);
		// layer弹出层
		invoicebox =	layer.open({
			title:'发票信息',
			type: 1,
			area: ['628px', '420px'], //宽高
			content: data,
			success :function(){
				if(param.invoiceId>0){
				    $('.inv_codebox').show();
				    $('#invoice_num').val($('#invoiceCode_'+param.invoiceId).val());
				 }
			},
		});
	});
}
function layerclose(){
  layer.close(invoicebox);
}


function changeInvoiceItem(t,obj){
	$(obj).addClass('inv_li_curr').siblings().removeClass('inv_li_curr');
	$('.inv_editing').remove();// 删除正在编辑中的发票信息
	$('.inv_add').show();
	$('#invoiceId').val(t);
	if(t==0){
		// 为个人时，隐藏识别号
		$('.inv_codebox').css({display:'none'});
		$('#invoice_num').val(' ');
	}else{
		$('#invoice_num').val($('#invoiceCode_'+t).val());
		$('.inv_codebox').css({display:'block'});
	}
	$("#invoice_obj").val(t);
}
// 是否需要开发票
function changeInvoiceItem1(t,obj){
	$(obj).addClass('inv_li_curr').siblings().removeClass('inv_li_curr');
	$('#isInvoice').val(t);
}
// 显示发票增加
function invAdd(){
	$("#invoiceId").val(0);
	$("#invoice_obj").val(1);
	$('#invoice_num').val('');
	$('.inv_li').removeClass('inv_li_curr');// 移除当前选中样式
	$('.inv_ul').append('<li class="inv_li inv_li_curr inv_editing"><input type="text" id="invoiceHead" placeholder="新增单位发票抬头" value="" style="width:65%;height:20px;padding:1px;"><i></i><div style="top:8px;" class="inv_opabox"><a href="javascript:void(0)" onCLick="addInvoice()">保存</a></div></li>');
	$('.inv_ul').scrollTop($('.inv_ul')[0].scrollHeight);// 滚动到底部
	$('.inv_add').hide();// 隐藏新增按钮
	$('.inv_codebox').css({display:'block'});// 显示`纳税人识别号`
}
// 执行发票抬头新增
function addInvoice(){
	var head = $('#invoiceHead').val();
	if(head.length==0){
		WST.msg('发票抬头不能为空');
		return;
	}
	var loading = WST.load({msg:'正在提交数据，请稍后...'});
	$.post(WST.U('home/Invoices/add'),{invoiceHead:head},function(data){
		var json = WST.toJson(data);
		layer.close(loading);
		if(json.status==1){
			WST.msg(json.msg,{icon:1});
			$('.inv_editing').remove();
			var code = [];
			code.push('<li class=\'inv_li inv_li_curr\' onClick="changeInvoiceItem(\''+json.data.id+'\',this)">');
     		code.push('<input type="text" value="'+head+'" readonly="readonly" class="invoice_input" id="invoiceHead_'+json.data.id+'" />');
			code.push('<input type="hidden" id="invoiceCode_'+json.data.id+'" value=""} /><i></i>');
			code.push('<div class="inv_opabox">');
			code.push('<a href=\'javascript:void(0)\' onClick="invEdit(\''+json.data.id+'\',this)" class="edit_btn">编辑</a>');
			code.push('<a href=\'javascript:void(0)\' onClick="editInvoice(\''+json.data.id+'\',this)" style="display:none;" class="save_btn">保存</a>');
			code.push('<a href=\'javascript:void(0)\' onClick="delInvoice(\''+json.data.id+'\',this)">删除</a></div></li>');
			$('.inv_li:first').after(code.join(''));
			// 显示新增按钮
			$('.inv_add').show();
			// 修改invoiceId
			$('#invoiceId').val(json.data.id);
		}else{
			WST.msg(json.msg,{icon:2});
		}
	});
}
// 显示发票修改
function invEdit(id,obj){
	var input = $(obj).parent().parent().find('.invoice_input');
	input.removeAttr('readonly').focus();
	input.mouseup(function(){return false});
	$(obj).parent().parent().mouseup(function(){
		input.attr('readonly','readonly');
		$(obj).show().siblings('.save_btn').hide();
	});
	$(obj).hide().siblings('.save_btn').show();
	var invoice_code = $('#invoiceCode_'+id).val();
	$('.inv_codebox').css({display:'block'})
	$('#invoice_num').val(invoice_code);// 显示`纳税人识别号`)
}
// 完成发票修改
function editInvoice(id,obj){
	var head = $('#invoiceHead_'+id).val();
	if(head.length==0){
		WST.msg('发票抬头不能为空');
		return;
	}
	var loading = WST.load({msg:'正在提交数据，请稍后...'});
	$.post(WST.U('home/Invoices/edit'),{invoiceHead:head,id:id},function(data){
		var json = WST.toJson(data);
		layer.close(loading);
		if(json.status==1){
			var input = $(obj).parent().parent().find('.invoice_input');
			input.attr('readonly','readonly')
			$(obj).hide().siblings('.edit_btn').show();
			WST.msg(json.msg,{icon:1});
		}else{
			WST.msg(json.msg,{icon:2});
		}
	});
}

// 设置页面显示值
function setInvoiceText(invoiceHead){
	var isInvoice  = $('#isInvoice').val();
	var invoiceObj = $('#invoice_obj').val();// 发票对象
	var text = '不开发票';
	if(isInvoice==1){
		text = (invoiceObj==0)?'普通发票（纸质）  个人   明细':'普通发票（纸质）'+invoiceHead+' 明细';
	}
	$('#invoice_info').html(text);
	layerclose();
}


// 保存纳税人识别号
function saveInvoice(){
	var isInv = $('#isInvoice').val();
	var num = $('#invoice_num').val();
	var id = $('#invoiceId').val();
	var invoiceHead = $('#invoiceHead').val();// 发票抬头
	var url = WST.U('home/Invoices/add');
	var params = {};
	if(id>0){
		url = WST.U('home/Invoices/edit');
		invoiceHead = $('#invoiceHead_'+id).val();// 发票抬头
		params.id = id;
	}
	params.invoiceHead = invoiceHead;
	params.invoiceCode = num;
	if($('#invoice_obj').val()!=0){
		var loading = WST.load({msg:'正在提交数据，请稍后...'});
		$.post(url,params,function(data){
			var json = WST.toJson(data);
			layer.close(loading);
			if(json.status==1){
				// 判断用户是否需要发票
				setInvoiceText(invoiceHead);
				if(id==0)$('#invoiceId').val(json.data.id)
			}else{
				WST.msg(json.msg,{icon:2});
			}
		});
	}else{
		setInvoiceText('');
	}
}

// 删除发票信息
function delInvoice(id,obj){
	WST.confirm({content:'您确定要删除该发票信息吗？',yes:function(index){
		$.post(WST.U('home/invoices/del'),{id:id},function(data,textStatus){
		     var json = WST.toJson(data);
		     if(json.status==1){
		    	 WST.msg(json.msg,{icon:1});
		    	 $(obj).parent().parent().remove();
		    	 $('#invoiceId').val(0);
		    	 // 选中 `个人`
		    	 $('.inv_li:first').click();
		     }else{
		    	 WST.msg(json.msg,{icon:2});
		     }
		});
	}});
}
