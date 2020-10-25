// 列表
function queryByPage(p){
	$('#loading').show();
	var params = {};
	params = WST.getParams('.s-ipt');
	params.key = $.trim($('#key').val());
	params.page = p;
	$.post(WST.U('shop/orderservices/pageQuery'),params,function(data,textStatus){
		$('#loading').hide();
	    var json = WST.toJson(data);
	    $('.j-order-row').remove();
	    if(json.status==1){
	    	json = json.data;
            if(params.page>json.data.last_page && json.data.last_page >0){
                queryByPage(json.data.last_page);
                return;
            }
	       	var gettpl = document.getElementById('tblist').innerHTML;
	       	laytpl(gettpl).render(json.data, function(html){
	       		$(html).insertAfter('#loadingBdy');
         		$('.gImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.RESOURCE_PATH+'/'+WST.conf.GOODS_LOGO});
	       	});
       		laypage({
	        	 cont: 'pager', 
	        	 pages:json.last_page, 
	        	 curr: json.current_page,
	        	 skin: '#1890ff',
	        	 groups: 3,
	        	 jump: function(e, first){
	        		 if(!first){
	        			 queryByPage(e.curr);
	        		 }
	        	 } 
	        });
       	}   
	});
}

// 处理退换货
function isArgee(val){
    $('#isShopAgree').val(val);
    if(val==1){
        $('#j-agree-box').show();
        $('#j-disagree-box').hide();
    }else{
        $('#j-agree-box').hide();
        $('#j-disagree-box').show();
    }
}
function beforeCommit(){
    var isShopAgree = parseInt($('#isShopAgree').val());
    if(isShopAgree!==0 && isShopAgree!==1){
        return WST.msg('请选择是否受理');
    }
    var _type = $('#goodsServiceType').val();
    if(_type==0 || _type==2){
        // 商家同意
        commit();
    }else{
        // 退款
        refund();
    }
}
// 退款
function refund(){
    var postData = {
        id:$('#id').val(),
        isShopAgree:$('#isShopAgree').val()
    }
    console.log('postData', postData);
    if(postData.isShopAgree!=0 && postData.isShopAgree!=1){
        return WST.msg('请选择是否受理');
    }
    if(postData.isShopAgree==0){
        // 不受理
        var disagreeRemark =  $('#disagreeRemark').val();
        if(disagreeRemark.length==0){
            return WST.msg('请输入不受理原因');
        }
        postData.disagreeRemark = disagreeRemark;
    }
    $.post(WST.U("shop/orderservices/dealRefund"),postData,function(res){
        var json = WST.toJson(res);
        WST.msg(json.msg);
        if(json.status==1){
            return goBack();
        }
    });
}


// 提交换货
function commit(){
    var postData = {
        id:$('#id').val(),
        isShopAgree:$('#isShopAgree').val()
    }
    if(postData.isShopAgree!=0 && postData.isShopAgree!=1){
        return WST.msg('请选择是否受理');
    }
    if(postData.isShopAgree==1){
        // 受理
        var shopAddress =  $('#shopAddress').val();
        var shopName =  $('#shopName').val();
        var shopPhone =  $('#shopPhone').val();
        if(shopAddress.length==0){
            return WST.msg('商家收货地址不能为空');
        }
        if(shopName.length==0){
            return WST.msg('商家收货人不能为空');
        }
        if(shopPhone.length==0){
            return WST.msg('商家联系人不能为空');
        }
        postData.shopAddress = shopAddress;
        postData.shopName = shopName;
        postData.shopPhone = shopPhone;
    }
    if(postData.isShopAgree==0){
        // 不受理
        var disagreeRemark =  $('#disagreeRemark').val();
        if(disagreeRemark.length==0){
            return WST.msg('请输入不受理原因');
        }
        postData.disagreeRemark = disagreeRemark;
    }
    
    $.post(WST.U("shop/orderservices/dealApply"),postData,function(res){
        var json = WST.toJson(res);
        WST.msg(json.msg);
        if(json.status==1){
            return goBack();
        }
    });
}
// 确认收货
function receive(p){
    var postData = {
        id:$('#id').val(),
        isShopAccept:$('#isShopAccept').val(),
        shopRejectType:$('#shopRejectType').val(),
        shopRejectOther:$('#shopRejectOther').val()
    }
    if(postData.shopRejectType=='10000' && postData.shopRejectOther.length==0){
        return WST.msg('请输入拒收原因');
    }
    $.post(WST.U('shop/orderservices/shopReceive'),postData,function(res){
        var json = WST.toJson(res);
        WST.msg(json.msg);
        if(json.status==1){
            return goBack();
        }
    })
}
// 是否确认收货
function isShopAccept(val){
    $('#isShopAccept').val(val);
    if(val==-1){
        $('#j-receive-box').show();
    }else{
        $('#j-receive-box').hide();
    }
}
// 选择拒收类型
function changeRejectType(val){
    if(val==10000){
        // 显示"原因输入框"
        $('#j-receive-input-box').show();
    }else{
        $('#j-receive-input-box').hide();
    }
}



// 商家发货相关

// 选择物流方式
function shopExpressType(val){
    $('#shopExpressType').val(val);
    if(val==1){
        $('.j-express-box').show();
    }else{
        $('.j-express-box').hide();
    }
}

// 发货
function send(p){
    var postData = WST.getParams('.ex-ipt');
    postData.id = $('#id').val();
    if(postData.shopExpressType==1){
        if(postData.shopExpressId==0)return WST.msg('请选择物流公司');
        if(postData.shopExpressNo.length==0)return WST.msg('请输入物流单号');
    }
    $.post(WST.U('shop/orderservices/shopSend'),postData,function(res){
        var json = WST.toJson(res);
        WST.msg(json.msg);
        if(json.status==1){
            return goBack(p);
        }
    });
}


function goBack(){
    history.go(-1);
    // location.href = 
}