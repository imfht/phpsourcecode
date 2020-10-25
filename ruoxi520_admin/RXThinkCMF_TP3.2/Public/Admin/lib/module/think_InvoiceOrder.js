/**
 *	用户开票申请单
 *
 *	@auth 牧羊人
 *	@date 2018-10-23
 */
layui.use(['func'],function(){
	
	//【声明变量】
	var func = layui.func
		,$ = layui.$;
	
	if(A=='index') {
		
		var status = parseInt($("#status").val());
		var option = {};
		if(status==0) {
			//全部订单
			option = { fixed:'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else if(status==1){
			//待确认订单
			option = { fixed:'right', width:280, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else if(status==2) {
			//已确认订单
			option = { fixed:'right', width:210, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				,{ field:'mobile', width:130, title: '用户手机号', align:'center' }
				,{ field:'receiver_name', width:100, title: '收货人名称', align:'center' }
				,{ field:'receiver_mobile', width:130, title: '收票人手机', align:'center' }
				,{ field:'format_amount', width:100, title: '开票总额', align:'center' }
				,{ field:'format_freight_amount', width:100, title: '运费', align:'center' }
				,{ field:'format_add_time', width:180, title: '申请时间', align:'center' }
				,{ field:'status_name', width:100, title: '申请单状态', align:'center'}
				,{ field:'city_name', width:200, title: '所属地区', align:'center' }
				,{ field:'address', width:200, title: '详细地址', align:'center' }
				,{ field:'shipping_status_name', width:100, title: '物流状态', align:'center'}
				,{ field:'format_shipping_time', width:180, title: '发货时间', align:'center' }
				,{ field:'format_sign_time', width:180, title: '签收时间', align:'center' }
				,option
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList",function(layEvent,data){
			if(layEvent==='confirmOrder') {
				//订单确认
				var url = cUrl + "/confirmOrder?id="+data.id;
				func.showWin("订单确认",url,600,380);
			}else if(layEvent==='shipping') {
				//发货
				var url = cUrl + "/shipping?id="+data.id;
				func.showWin("订单发货",url);
			}
		},cUrl+"/index?status="+status);
		
		//【设置弹框】
		func.setWin("发票申请单");
		
	}

});