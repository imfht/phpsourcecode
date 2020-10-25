/**
 *	积分兑换订单
 *
 *	@auth 牧羊人
 *	@date 2018-10-25
 */
layui.use(['func'],function(){
	
	//【声明变量】
	var func = layui.func
		,$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				,{ field:'order_num', width:250, title: '订单编号', align:'center' }
				,{ field:'mobile', width:130, title: '兑换用户', align:'center' }
				,{ field:'format_add_time', width:180, title: '兑换时间', align:'center' }
				,{ field:'status_name', width:100, title: '订单状态', align:'center'}
				,{ field:'product_name', width:200, title: '商品名称', align:'center' }
				,{ field:'product_num', width:100, title: '商品数量', align:'center' }
				,{ field:'amount', width:100, title: '使用总积分', align:'center' }
				,{ field:'format_freight_amount', width:100, title: '运费/元', align:'center' }
				,{ field:'receiver_name', width:100, title: '收货人名称', align:'center' }
				,{ field:'receiver_mobile', width:130, title: '收货人手机', align:'center' }
				,{ field:'city_name', width:250, title: '收货地址', align:'center' }
				,{ field:'format_shipping_time', width:180, title: '发货时间', align:'center' }
				,{ field:'format_sign_time', width:180, title: '签收时间', align:'center' }
				,{ fixed:'right', width:280, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList",function(layEvent,data){
			if(layEvent==='shipping') {
				//发货
				
				if(data.status>=2) {
					layer.msg('兑换商品已发货,无需重复操作');
					return false;
				}
				
				var url = cUrl + "/shipping?id="+data.id;
				func.showWin("订单发货",url);
			}
		});
		
		//【设置弹框】
		func.setWin("积分兑换订单");
		
	}

});