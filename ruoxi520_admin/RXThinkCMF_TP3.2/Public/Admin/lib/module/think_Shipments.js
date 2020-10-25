/**
 *	寄件管理
 *
 *	@auth 牧羊人
 *	@date 2018-10-23
 */
layui.use(['func'],function(){
	
	//【声明变量】
	var func = layui.func
		,$ = layui.$;
	
	if(A=='index') {
		
		var status = $("#status").val();
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				,{ field:'express_no', width:200, title: '物流单号', align:'center' }
				,{ field:'express_name', width:100, title: '快递公司', align:'center' }
				,{ field:'type', width:100, title: '寄件类型', align:'center', templet:function(d){
	  	  			  var str = "";
	  	  			  if(d.type==1){
	  	  				  str = '<span class="layui-btn layui-btn-green layui-btn-xs">商品</span>';
	  	  			  }else if(d.type==2){
	  	  				  str = '<span class="layui-btn layui-bg-cyan layui-btn-xs">发票</span>';
	  	  			  }
	  	  			  return str;
	  	  		  }}
				,{ field:'format_freight_amount', width:100, title: '运费', align:'center' }
				,{ field:'city_name', width:200, title: '寄送城市', align:'center' }
				,{ field:'address', width:250, title: '详细地址', align:'center' }
				,{ field:'note', width:300, title: '备注', align:'center' }
				,{ field:'format_add_time', width:180, title: '发货时间', align:'center' }
				,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("货物物流");
		
	}

});