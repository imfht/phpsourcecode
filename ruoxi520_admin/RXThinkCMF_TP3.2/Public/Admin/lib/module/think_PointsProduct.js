/**
 *	商品
 *
 *	@auth 牧羊人
 *	@date 2018-10-16
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
				,{ field:'product_sn', width:100, title: '商品编码', align:'center' }
				,{ field:'cover_url', width:80, title: '封面', align:'center', templet:function(d){
		              return '<a href="'+d.cover_url+'" target="_blank"><img src="'+d.cover_url+'" height="26" /></a>';
		          } }
				,{ field:'name', width:250, title: '商品名称', align:'center', templet:function(d){
					return '<a href="'+d.detail_url+'" title="'+d.name+'" class="layui-table-link" target="_blank">'+d.name+'</a>';
				} }
				,{ field:'brand_name', width:150, title: '品牌名称', align:'center' }
				,{ field:'cate_name', width:150, title: '品牌名称', align:'center' }
				,{ field:'points', width:100, title: '积分兑换值', align:'center' }
				,{ field:'stock_num', width:100, title: '商品库存', align:'center' }
				,{ field:'sales_num', width:100, title: '销售总量', align:'center' }
				,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
				,{ field:'format_upd_time', width:180, title: '更新时间', align:'center', sort: true }
				,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("商品");
		
	}

});