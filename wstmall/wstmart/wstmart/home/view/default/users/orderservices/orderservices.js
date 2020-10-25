function queryPage(p){
	$('#loading').show();
	var params = {};
	params = WST.getParams('.u-query');
	params.key = $.trim($('#key').val());
	params.page = p;
	$.post(WST.U('home/orderservices/pagequery'),params,function(data,textStatus){
		$('#loading').hide();
	    var json = WST.toJson(data);
	    $('.j-order-row').remove();
	    if(json.status==1){
            json = json.data;
	    	if(params.page>json.last_page && json.last_page >0){
               queryPage(json.last_page);
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
		        	 skin: '#e23e3d',
		        	 groups: 3,
		        	 jump: function(e, first){
		        		 if(!first){
		        			 queryPage(e.curr);
		        		 }
		        	 }
		    });
       	} 
	});
}

// 快递方式
function deliverType(val){
    if(val==0){
        $('#j-express').hide();
    }else{
        $('#j-express').show();
    }
}
// 用户发货
function userExpress(){
    var params = WST.getParams('.ex-ipt');
    if(params.expressType==1){
        if(!params.expressId || params.expressId==0){
            return WST.msg('请选择物流公司');
        }
        // 需要快递
        if(!params.expressNo || params.expressNo.length==0){
            return WST.msg('请填写物流单号');
        }
    }
    params.id = $('#id').val();
    $.post(WST.U('home/orderservices/userExpress'),params,function(res){
        var json = WST.toJson(res);
        WST.msg(res.msg);
        if(json.status==1){
            // 刷新当前页
            location.reload();
        }
    });
}

// 用户确认收货相关
function isUserAccept(val){
    if(val==1){
        $('#j-confirm').hide();
    }else{
        $('#j-confirm').show();
    }
}
function changeRejectType(val){
    if(val=='10000'){
        $('#j-cm-input').show();
    }else{
        $('#j-cm-input').hide();
    }
}
// 用户确认收货
function userConfirm(){
    var postData = WST.getParams('.cm-ipt');
    postData.id = $('#id').val();
    if(postData.isUserAccept==-1 && postData.userRejectType==0){
        return WST.msg('请选择拒收类型');
    }
    if(postData.userRejectType=='10000' && postData.userRejectOther.length==0){
        return WST.msg('请输入拒收原因');
    }
    $.post(WST.U('home/orderservices/userReceive'),postData,function(res){
        var json = WST.toJson(res);
        WST.msg(json.msg);
        if(json.status==1){
            // 刷新当前页
            location.reload();
        }
    })
}