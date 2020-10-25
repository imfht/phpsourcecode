/**
 *	订单商品
 *
 *	@auth 牧羊人
 *	@date 2018-10-22
 */
layui.use(['func'],function(){
	
	//【声明变量】
	var func = layui.func
		,$ = layui.$;
	
	if(A=='index') {
		
		var order_id = $("#order_id").val();
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				,{ field:'business_sn', width:150, title: '商家商品编号', align:'center' }
				,{ field:'cover_url', width:100, title: '商品封面', align:'center', templet:function(d){
					  var coverStr = "";
			 			if(d.cover_url) {
			 				coverStr = '<a href="'+d.cover_url+'" target="_blank"><img src="'+d.cover_url+'" height="26" /></a>';
			 			}
			 			return coverStr;
		          }}
				,{ field:'product_name', width:200, title: '商品名称', align:'center' }
				,{ field:'attr_value', width:350, title: '商品属性', align:'center' }
				,{ field:'format_price', width:100, title: '商品单价', align:'center' }
				,{ field:'product_num', width:100, title: '商品数量', align:'center' }
				,{ field:'format_add_time', width:210, title: '创建时间', align:'center' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList",null,cUrl+"/index?order_id="+order_id);
		
	}

});