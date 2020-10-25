/**
 *	商品附件
 *
 *	@auth 牧羊人
 *	@date 2018-12-21
 */
layui.use(['func','form'],function(){
	
	//【声明变量】
	var func = layui.func
		,form = layui.form
		,$ = layui.$;
	
	if(A=='index') {
		
		// 商品ID
		var product_id = $("#product_id").val();
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				,{ field:'name', width:250, title: '附件名称', align:'center' }
				,{ field:'file', width:400, title: '附件地址', align:'center' }
				,{ field:'file_name', width:200, title: '文件原名', align:'center' }
				,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
				,{ field:'format_upd_time', width:180, title: '更新时间', align:'center', sort: true }
				,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList",null,cUrl+"/index?product_id="+product_id);
		
		//【设置弹框】
		func.setWin("商品附件",500,300,['product_id='+product_id]);
		
	}

});