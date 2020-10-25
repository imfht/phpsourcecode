/**
 *	商品订单
 *
 *	@auth 牧羊人
 *	@date 2018-10-19
 */
layui.use(['func'],function(){
	
	//【声明变量】
	var func = layui.func
		,$ = layui.$;
	
	if(A=='index') {
		
		var status = parseInt($("#status").val());
		var option = {};
		if(status==1) {
			//待确认
			option = { fixed:'right', width:280, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else if(status==2) {
			//已确认待付款
			option = { fixed:'right', width:230, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else if(status==3) {
			//已付款待发货
			option = { fixed:'right', width:210, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else if(status==0 || status==5 || status==8) {
			//已完成、已结算
			option = { fixed:'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				,{ field:'order_num', width:280, title: '订单编号', align:'center' }
				,{ field:'order_type_name', width:100, title: '订单类型', align:'center' }
				,{ field:'format_amount', width:100, title: '商品总额', align:'center' }
				,{ field:'format_freight_amount', width:100, title: '快递费', align:'center' }
				,{ field:'format_pay_amount', width:100, title: '实际支付额', align:'center' }
				,{ field:'format_add_time', width:180, title: '下单时间', align:'center' }
				,{ field:'status_name', width:100, title: '订单状态', align:'center'}
				,{ field:'pay_type_name', width:100, title: '支付方式', align:'center' }
				,{ field:'pay_status_name', width:100, title: '支付状态', align:'center'}
				,{ field:'format_pay_time', width:180, title: '支付时间', align:'center' }
				,{ field:'mobile', width:130, title: '下单用户', align:'center' }
				,{ field:'receiver_name', width:120, title: '收货人姓名', align:'center' }
				,{ field:'receiver_mobile', width:130, title: '收货人手机', align:'center' }
				,{ field:'city_name', width:200, title: '所属地区', align:'center' }
				,{ field:'address', width:250, title: '详细地址', align:'center' }
				,{ field:'shipping_status_name', width:100, title: '物流状态', align:'center'}
				,{ field:'source_name', width:100, title: '订单来源', align:'center'}
				,{ field:'format_shipping_time', width:180, title: '发货时间', align:'center' }
				,{ field:'format_sign_time', width:180, title: '签收时间', align:'center' }
				,option
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList",function(layEvent,data){
			if(layEvent==='updateAddress') {
				//修改收货地址
				var url = cUrl + "/updateAddress?id="+data.id;
				func.showWin("修改收货地址",url,800,550);
				
			}else if(layEvent==='delivery') {
				//发货
				var url = cUrl + "/delivery?id="+data.id;
				func.showWin("订单发货",url);
				
			}else if(layEvent==='confirmOrder') {
				//订单确认
				var url = cUrl + "/confirmOrder?id="+data.id;
				func.showWin("订单确认",url,600,380);
				
			}else if(layEvent==='invoice') {
				//发票详情
				var url = cUrl + "/invoice?id="+data.id;
				func.showWin("发票详情",url,750,350);
			}else if(layEvent==='transfer') {
				//转账凭证审核
				var url = cUrl + "/transfer?id="+data.id;
				func.showWin("转账凭证审核",url,650,420);
			}

		},cUrl+"/index?status="+status);
		
		//【设置弹框】
		func.setWin("订单");
		
	}else{
		
		// 【查看订单商品列表详情】
		$("#btnProductList").click(function(){
		
			var orderId = $("#order_id").val();
			var url = mUrl + "/OrderProduct/index?order_id="+orderId;
			func.showWin("订单商品",url);
			
		});
		
	}

});