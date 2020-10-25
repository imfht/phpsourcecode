$(function(){
	//取消订单
	$(document).on('click','[data-toggle="cannel_order"]',function(){
		if(confirm("是否取消？")==true){
			var _this = $(this);
			var data={
				id : _this.data('id'),
			};
			$.post('/Muushop/api/cancel_order',data,function (ret) {
		        //ret = JSON.parse(ret);
			    if(ret.status==1){
			        toast.success(ret.info, '温馨提示');
			        setTimeout(function () {
			            window.location.href = ret.url;
			        }, 1000);
			    }else{
			        toast.error(ret.info, '温馨提示');
			    }
			});
		}
	});

});

$(function(){
	//确认收货
	$(document).on('click','[data-toggle="do_receipt"]',function(){
		if(confirm("是否确认收货？")==true){
			var _this = $(this);
			var data={
				id : _this.data('id'),
			};
			$.post('/Muushop/api/do_receipt',data,function (ret) {
			    if(ret.status==1){
			        toast.success(ret.info, '温馨提示');
			        setTimeout(function () {
			            window.location.href = ret.url;
			        }, 1000);
			    }else{
			        toast.error(ret.info, '温馨提示');
			    }
			})
		}
	})
});