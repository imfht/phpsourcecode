function checkChks(obj,cobj){
	WST.checkChks(obj,cobj);
	$(cobj).each(function(){
		id = $(this).val();
		if(obj.checked){
			$(this).addClass('selected');
		}else{
			$(this).removeClass('selected');
		}
		var cid = $(this).find(".j-chk").val();
		if(cid!=''){
		    WST.changeCartGoods(cid,$('#buyNum_'+cid).val(),obj.checked?1:0);
		    statCartMoney();
	    }
	})
}
function statCartMoney(){
	var cartMoney = 0,goodsTotalPrice,id;
	$('.j-gchk').each(function(){
		id = $(this).val();
		goodsTotalPrice = parseFloat($(this).attr('mval'))*parseInt($('#buyNum_'+id).val());
		$('#tprice_'+id).html(goodsTotalPrice);
		if($(this).prop('checked')){	
			cartMoney = cartMoney + goodsTotalPrice;
		}
	});
	$('#totalMoney').html(cartMoney);
}

function addrBoxOver(t){
	$(t).addClass('radio-box-hover');
	$(t).find('.operate-box').show();
}
function addrBoxOut(t){
	$(t).removeClass('radio-box-hover');
	$(t).find('.operate-box').hide();
}

function setDeaultAddr(id){
	$.post(WST.U('home/useraddress/setDefault'),{id:id},function(data){
		var json = WST.toJson(data);
		if(json.status==1){
			getAddressList();
			changeAddrId(id);
		}
	});
}


function changeAddrId(id){
	$.post(WST.U('home/useraddress/getById'),{id:id},function(data){
		var json = WST.toJson(data);
		if(json.status==1){
			inEffect($('#addr-'+id),1);
			$('#s_addressId').val(json.data.addressId);
			$("select[id^='area_0_']").remove();
			var areaIdPath = json.data.areaIdPath.split("_");
			// 设置收货地区市级id
			$('#s_areaId').val(areaIdPath[1]);
             
	     	$('#area_0').val(areaIdPath[0]);
	     	// 计算运费
			getCartMoney();
	     	var aopts = {id:'area_0',val:areaIdPath[0],childIds:areaIdPath,className:'j-areas'}
	 		WST.ITSetAreas(aopts);
			WST.setValues(json.data);
		}
	})
}

function delAddr(id){
	WST.confirm({content:'您确定要删除该地址吗？',yes:function(index){
		$.post(WST.U('home/useraddress/del'),{id:id},function(data,textStatus){
		     var json = WST.toJson(data);
		     if(json.status==1){
		    	 WST.msg(json.msg,{icon:1});
		    	 getAddressList();
		     }else{
		    	 WST.msg(json.msg,{icon:2});
		     }
		});
	}});
}

function getAddressList(obj){
	var id = $('#s_addressId').val();
	var load = WST.load({msg:'正在加载记录，请稍后...'});
	$.post(WST.U('home/useraddress/listQuery'),{rnd:Math.random()},function(data,textStatus){
		 layer.close(load);
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 if(json.data && json.data && json.data.length){
	    		 var html = [],tmp;
	    		 for(var i=0;i<json.data.length;i++){
	    			 tmp = json.data[i];
	    			 var selected = (id==tmp.addressId)?'j-selected':'';
	    			 html.push(
	    					 '<div class="wst-frame1 '+selected+'" onclick="javascript:changeAddrId('+tmp.addressId+')" id="addr-'+tmp.addressId+'" >'+tmp.userName+'<i></i></div>',
	    					 '<li class="radio-box" onmouseover="addrBoxOver(this)" onmouseout="addrBoxOut(this)">',
	    					 tmp.userName,
	    					 '&nbsp;&nbsp;',
	    					 tmp.areaName+tmp.userAddress,
	    					 '&nbsp;&nbsp;&nbsp;&nbsp;',
	    					 tmp.userPhone
	    					 )
	    			if(tmp.isDefault==1){
	    				html.push('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="j-default">默认地址</span>')
	    			}		
	    			html.push('<div class="operate-box">');
	    			if(tmp.isDefault!=1){
	    				html.push('<a href="javascript:;" onclick="setDeaultAddr('+tmp.addressId+')">设为默认地址</a>&nbsp;&nbsp;');
	    			}
	    			html.push('<a href="javascript:void(0)" onclick="javascript:toEditAddress('+tmp.addressId+',this,1,1)">编辑</a>&nbsp;&nbsp;');
	    			if(json.data.length>1){
	    				html.push('<a href="javascript:void(0)" onclick="javascript:delAddr('+tmp.addressId+',this)">删除</a></div>');
	    			}
	    			html.push('<div class="wst-clear"></div>','</li>');
	    		 }
	    		 html.push('<a style="color:#1c9eff" onclick="editAddress()" href="javascript:;">收起地址</a>'); 


	    		 $('#addressList').html(html.join(''));
	    	 }else{
	    		 $('#addressList').empty();
	    	 }
	     }else{
	    	 $('#addressList').empty();
	     }
	})
}

function inEffect(obj,n){
	$(obj).addClass('j-selected').siblings('.wst-frame'+n).removeClass('j-selected');
}
function editAddress(){
	var isNoSelected = false;
	$('.j-areas').each(function(){
		isSelected = true;
		if($(this).val()==''){
			isNoSelected = true;
			return;
		}
	})
	if(isNoSelected){
		WST.msg('请选择完整收货地址！',{icon:2});
		return;
	}
	layer.close(layerbox);
	var load = WST.load({msg:'正在提交数据，请稍后...'});
	var params = WST.getParams('.j-eipt');
	params.areaId = WST.ITGetAreaVal('j-areas');
	$.post(WST.U('home/useraddress/'+((params.addressId>0)?'toEdit':'add')),params,function(data,textStatus){
		layer.close(load);
		var json = WST.toJson(data);
	     if(json.status==1){
	    	 $('.j-edit-box').hide();
	    	 $('.j-list-box').hide();
	    	 $('.j-show-box').show();
	    	 if(params.addressId==0){
	    		 $('#s_addressId').val(json.data.addressId);
	    	 }else{
	    		 $('#s_addressId').val(params.addressId);
	    	 }
	    	 var areaIds = WST.ITGetAllAreaVals('area_0','j-areas');
	    	 $('#s_areaId').val(areaIds[1]);
	    	 getCartMoney();
	    	 var areaNames = [];
	    	 $('.j-areas').each(function(){
	    		 areaNames.push($('#'+$(this).attr('id')+' option:selected').text());
	    	 })
	    	 $('#s_userName').html(params.userName+'<i></i>');
	    	 $('#s_address').html(params.userName+'&nbsp;&nbsp;&nbsp;'+areaNames.join('')+'&nbsp;&nbsp;'+params.userAddress+'&nbsp;&nbsp;'+params.userPhone);

	    	 $('#s_address').siblings('.operate-box').find('a').attr('onclick','toEditAddress('+params.addressId+',this,1,1,1)');

	    	 if(params.isDefault==1){
	    		 $('#isdefault').html('默认地址').addClass('j-default');
	    	 }else{
	    		 $('#isdefault').html('').removeClass('j-default');
	    	 }
	     }else{
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}
var layerbox;
function showEditAddressBox(){
	getAddressList();
	toEditAddress();
}
function emptyAddress(obj,n){
	inEffect(obj,n);
	$('#addressForm')[0].reset();
	$('#s_addressId').val(0);
	$('#addressId').val(0);
	$("select[id^='area_0_']").remove();

	layerbox =	layer.open({
					title:'用户地址',
					type: 1,
					area: ['800px', '300px'],
					content: $('.j-edit-box')
					});
}
function toEditAddress(id,obj,n,flag,type){
	inEffect(obj,n);
	id = (id>0)?id:$('#s_addressId').val();
	$.post(WST.U('home/useraddress/getById'),{id:id},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	     	if(flag){
		     	layerbox =	layer.open({
					title:'用户地址',
					type: 1,
					area: ['800px', '300px'], //宽高
					content: $('.j-edit-box')
				});
	     	}
	     	if(type!=1){
				 $('.j-list-box').show();
		    	 $('.j-show-box').hide();
	     	}
	    	 WST.setValues(json.data);
	    	 $('input[name="addrUserPhone"]').val(json.data.userPhone)
	    	 $("select[id^='area_0_']").remove();
	    	 if(id>0){
		    	 var areaIdPath = json.data.areaIdPath.split("_");
		     	 $('#area_0').val(areaIdPath[0]);
		     	 var aopts = {id:'area_0',val:areaIdPath[0],childIds:areaIdPath,className:'j-areas'}
		 		 WST.ITSetAreas(aopts);
	    	 }
	     }else{
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}
function getCartMoney(){
	var params = {};
	params.isUseScore = $('#isUseScore').prop('checked')?1:0;
	params.useScore = $('#useScore').val();
	params.areaId2 = $('#s_areaId').val();
	params.rnd = Math.random();
	params.deliverType = $('#deliverType').val();
	var load = WST.load({msg:'正在计算订单价格，请稍后...'});
	$.post(WST.AU('groupon://carts/getCartMoney'),params,function(data,textStatus){
		layer.close(load);  
		var json = WST.toJson(data);
		if(json.status==1){
		    json = json.data;
		    var shopFreight = 0;
		    // 设置每间店铺的运费及总价格
		    $('#shopF_'+json.shops['shopId']).html(json.shops['freight']);
		    $('#shopC_'+json.shops['shopId']).html(json.shops['goodsMoney']);
		    shopFreight = shopFreight + json.shops['freight'];
		    $('#deliverMoney').html(shopFreight);
		    $('#useScore').val(json.useScore);
		    $('#scoreMoney2').html(json.scoreMoney);
		 	$('#totalMoney').html(json.realTotalMoney);
		}
	});
}
function changeDeliverType(n,index,obj){
	changeSelected(n,index,obj);
	getCartMoney();
}
function submitOrder(){
	var params = WST.getParams('.j-ipt');
	params.isUseScore = $('#isUseScore').prop('checked')?1:0
	var load = WST.load({msg:'正在提交，请稍后...'});
	$.post(WST.AU('groupon://carts/submit'),params,function(data,textStatus){
		layer.close(load);   
		var json = WST.toJson(data);
	    if(json.status==1){
	    	 WST.msg(json.msg,{icon:1},function(){
	    		 location.href=WST.U('home/orders/succeed','orderNo='+json.data);
	    	 });
	    }else{
	    	WST.msg(json.msg,{icon:2});
	    }
	});
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
	$('.inv_ul').append('<li class="inv_li inv_li_curr inv_editing"><input type="text" id="invoiceHead" placeholder="新增单位发票抬头" value="" style="width:65%;height:21px;padding:1px;"><i></i><div style="top:8px;" class="inv_opabox"><a href="javascript:void(0)" onCLick="addInvoice()">保存</a></div></li>');
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
			$('#invoiceId').val(json.data.id);
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

function changeSelected(n,index,obj){
	$('#'+index).val(n);
	inEffect(obj,2);
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